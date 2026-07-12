<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Clean up expired attempts for the logged-in user in real-time
        $userAttempts = Attempt::where('user_id', auth()->id())->whereNull('completed_at')->get();
        foreach ($userAttempts as $att) {
            $att->checkAndClose();

            // If attempt is still not completed and it is NOT a repeatable quiz,
            // visiting the dashboard means they left the exam. We force-submit/close it now.
            if (!$att->completed_at && $att->quiz->attempt_limit_type !== 'repeatable') {
                $questions = $att->quiz->questions;
                $correctCount = 0;
                $totalQuestions = $questions->count();

                foreach ($questions as $question) {
                    $answer = $att->answers()->where('question_id', $question->id)->first();
                    if ($answer && $question->type === 'multiple_choice') {
                        if (trim(strtolower($answer->submitted_answer)) === trim(strtolower($question->correct_answer))) {
                            $correctCount++;
                        }
                    }
                }

                $att->score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 100;
                $att->completed_at = now();
                $att->save();

                ActivityLog::log('attempt_closed_on_exit', "Attempt ID: {$att->id} was automatically finalized because the user left the exam page.");
            }
        }

        // 2. Search & Filter Published Quizzes only
        $search = $request->input('search');
        $quizzesQuery = Quiz::where('is_published', true)->withCount('questions');

        if ($search) {
            $quizzesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $quizzes = $quizzesQuery->get();

        // 3. Attach attempt statistics/status metadata to each quiz
        foreach ($quizzes as $quiz) {
            // Find active attempt if any (already cleaned by checkAndClose)
            $activeAttempt = Attempt::where('user_id', auth()->id())
                ->where('quiz_id', $quiz->id)
                ->whereNull('completed_at')
                ->first();

            $quiz->active_attempt = $activeAttempt;

            // Count completed attempts
            $completedCount = Attempt::where('user_id', auth()->id())
                ->where('quiz_id', $quiz->id)
                ->whereNotNull('completed_at')
                ->count();

            $quiz->completed_attempts_count = $completedCount;

            $quiz->is_limit_reached = false;
            $quiz->limit_message = '';

            if ($quiz->attempt_limit_type === 'once' && $completedCount >= 1) {
                $quiz->is_limit_reached = true;
                $quiz->limit_message = 'Single attempt completed';
            } elseif ($quiz->attempt_limit_type === 'custom' && $completedCount >= $quiz->max_attempts) {
                $quiz->is_limit_reached = true;
                $quiz->limit_message = "Limit reached ({$quiz->max_attempts}/{$quiz->max_attempts})";
            }
        }

        // 4. Fetch User Attempt History
        $attempts = Attempt::with(['quiz'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Calculate Detailed Participant Statistics
        $completedAttempts = $attempts->whereNotNull('completed_at');
        $stats = [
            'completed' => $completedAttempts->count(),
            'average_score' => round($completedAttempts->avg('score') ?? 0),
            'pending_review' => $completedAttempts->filter(function ($attempt) {
                return $attempt->answers()
                    ->whereHas('question', function ($q) {
                        $q->where('type', 'essay');
                    })
                    ->whereNull('is_correct')
                    ->exists();
            })->count(),
        ];

        return view('participant.dashboard', compact('quizzes', 'attempts', 'stats', 'search'));
    }

    public function start(Quiz $quiz)
    {
        if (!$quiz->is_published) {
            return back()->with('error', 'This quiz is not available.');
        }

        if ($quiz->questions()->count() === 0) {
            return back()->with('error', 'This quiz has no questions yet.');
        }

        // 1. Check if there is an active in-progress attempt for this quiz
        $activeAttempt = Attempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($activeAttempt) {
            $activeAttempt->checkAndClose();
            if (is_null($activeAttempt->completed_at)) {
                return redirect()->route('participant.attempts.take', $activeAttempt)
                    ->with('info', 'Resuming your active attempt.');
            }
        }

        // 2. Check attempt limits for non-repeatable quizzes
        $completedAttemptQuery = Attempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at');

        $completedCount = $completedAttemptQuery->count();

        if ($quiz->attempt_limit_type === 'once' && $completedCount >= 1) {
            return back()->with('error', 'You have already completed this quiz. Only 1 attempt is allowed.');
        }

        if ($quiz->attempt_limit_type === 'custom' && $completedCount >= $quiz->max_attempts) {
            return back()->with('error', "You have reached the maximum attempt limit of {$quiz->max_attempts} for this quiz.");
        }

        // 3. Logic handling for Repeatable Option: Recycle existing row instead of creating a new one
        if ($quiz->attempt_limit_type === 'repeatable') {
            $existingAttempt = Attempt::where('user_id', auth()->id())
                ->where('quiz_id', $quiz->id)
                ->first();

            if ($existingAttempt) {
                DB::transaction(function () use ($existingAttempt) {
                    // Flush previous answers linked to this specific attempt
                    $existingAttempt->answers()->delete();

                    // Re-initialize attempt metadata
                    $existingAttempt->update([
                        'started_at' => now(),
                        'score' => null,
                        'completed_at' => null,
                    ]);
                });

                ActivityLog::log('attempt_restarted', "Restarted repeatable attempt for Quiz '{$quiz->title}' (Attempt ID: {$existingAttempt->id}).");

                return redirect()->route('participant.attempts.take', $existingAttempt);
            }
        }

        // Fallback for new attempts (first time repeatable, once, or custom)
        $attempt = Attempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'score' => null,
            'completed_at' => null,
        ]);

        ActivityLog::log('attempt_started', "Started attempt for Quiz '{$quiz->title}' (Attempt ID: {$attempt->id}).");

        return redirect()->route('participant.attempts.take', $attempt);
    }

    public function take(Attempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($attempt->completed_at) {
            return redirect()->route('participant.attempts.show', $attempt);
        }

        $attempt->checkAndClose();
        if ($attempt->completed_at) {
            return redirect()->route('participant.attempts.show', $attempt)
                ->with('error', 'The time limit for this attempt has expired. It was automatically submitted.');
        }

        $quiz = $attempt->quiz()->with('questions')->first();

        return view('participant.quizzes.take', compact('attempt', 'quiz'));
    }

    public function submit(Request $request, Attempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($attempt->completed_at) {
            return redirect()->route('participant.attempts.show', $attempt);
        }

        $attempt->checkAndClose();
        if ($attempt->completed_at) {
            return redirect()->route('participant.attempts.show', $attempt)
                ->with('error', 'Your attempt time limit has expired and was automatically submitted.');
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions;
        $submittedAnswers = $request->input('answers', []);

        $correctCount = 0;
        $totalQuestions = $questions->count();

        DB::transaction(function () use ($attempt, $questions, $submittedAnswers, &$correctCount, $totalQuestions) {
            // Extra insurance for repeatable: Ensure current answers are clean before writing updates
            if ($attempt->quiz->attempt_limit_type === 'repeatable') {
                $attempt->answers()->delete();
            }

            foreach ($questions as $question) {
                $submitted = $submittedAnswers[$question->id] ?? null;
                $isCorrect = null;

                if ($question->type === 'multiple_choice') {
                    $isCorrect = (trim(strtolower($submitted)) === trim(strtolower($question->correct_answer)));
                    if ($isCorrect) {
                        $correctCount++;
                    }
                }

                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'submitted_answer' => $submitted,
                    'is_correct' => $isCorrect,
                ]);
            }

            $attempt->score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 100;
            $attempt->completed_at = now();
            $attempt->save();
        });

        ActivityLog::log('attempt_submitted', "Submitted attempt for Quiz '{$quiz->title}' (Attempt ID: {$attempt->id}). Score: {$attempt->score}%.");

        return redirect()->route('participant.attempts.show', $attempt)
            ->with('success', 'Quiz submitted successfully.');
    }

    public function showAttempt(Attempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $attempt->load(['quiz.questions', 'answers.question']);

        return view('participant.quizzes.result', compact('attempt'));
    }
}
