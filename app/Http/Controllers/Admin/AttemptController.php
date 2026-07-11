<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    public function show(Attempt $attempt)
    {
        $attempt->load(['user', 'quiz.questions', 'answers.question']);
        return view('admin.attempts.show', compact('attempt'));
    }

    public function grade(Request $request, Attempt $attempt)
    {
        $grades = $request->input('grades', []); // [answer_id => 'correct'/'incorrect']

        foreach ($grades as $answerId => $status) {
            $answer = $attempt->answers()->find($answerId);
            if ($answer && $answer->question->type === 'essay') {
                $answer->update([
                    'is_correct' => ($status === 'correct'),
                ]);
            }
        }

        // Recalculate score based on all questions
        $totalQuestions = $attempt->quiz->questions()->count();
        $correctAnswers = $attempt->answers()->where('is_correct', true)->count();

        $oldScore = $attempt->score;
        $attempt->update([
            'score' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 100,
        ]);

        ActivityLog::log('attempt_graded', "Graded essay answers for attempt ID: {$attempt->id} (User: {$attempt->user->name}). Score changed from {$oldScore}% to {$attempt->score}%.");

        return redirect()->route('admin.attempts.show', $attempt)
            ->with('success', 'Attempt graded and score updated successfully.');
    }
}
