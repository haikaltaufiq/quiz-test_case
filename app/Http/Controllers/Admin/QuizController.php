<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveQuizRequest;
use App\Models\Quiz;
use App\Models\Attempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        // 1. Search & Fetch Quizzes
        $searchQuizzes = $request->input('search_quizzes');
        $quizzesQuery = Quiz::withCount('questions');
        if ($searchQuizzes) {
            $quizzesQuery->where(function ($q) use ($searchQuizzes) {
                $q->where('title', 'like', "%{$searchQuizzes}%")
                    ->orWhere('description', 'like', "%{$searchQuizzes}%");
            });
        }
        $quizzes = $quizzesQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'quizzes_page');

        // 2. Search & Fetch Attempts
        $searchAttempts = $request->input('search_attempts');
        $attemptsQuery = Attempt::with(['user', 'quiz']);
        if ($searchAttempts) {
            $attemptsQuery->where(function ($q) use ($searchAttempts) {
                $q->whereHas('user', function ($qu) use ($searchAttempts) {
                    $qu->where('name', 'like', "%{$searchAttempts}%");
                })->orWhereHas('quiz', function ($qq) use ($searchAttempts) {
                    $qq->where('title', 'like', "%{$searchAttempts}%");
                });
            });
        }
        $attempts = $attemptsQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'attempts_page');

        // 3. Calculate Detailed Dashboard Statistics
        $stats = [
            'total_quizzes' => Quiz::count(),
            'published_quizzes' => Quiz::where('is_published', true)->count(),
            'total_attempts' => ActivityLog::whereIn('action', ['quiz_started', 'attempt_started'])->count(),
            'ungraded_attempts' => Attempt::whereHas('answers', function ($query) {
                $query->whereNull('is_correct')->whereHas('question', function ($q) {
                    $q->where('type', 'essay');
                });
            })->count(),
            'average_score' => round(Attempt::whereNotNull('completed_at')->avg('score') ?? 0),
        ];

        // 4. Fetch Recent Activity Logs
        $activityLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.quizzes.index', compact('quizzes', 'attempts', 'stats', 'activityLogs', 'searchQuizzes', 'searchAttempts'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(SaveQuizRequest $request)
    {
        $data = $request->validated();
        $data['is_published'] = $request->has('is_published');
        if ($data['attempt_limit_type'] === 'once') {
            $data['max_attempts'] = 1;
        } elseif ($data['attempt_limit_type'] === 'repeatable') {
            $data['max_attempts'] = null;
        }

        $quiz = Quiz::create($data);

        ActivityLog::log('quiz_created', "Quiz '{$quiz->title}' was created.");

        return redirect()->route('admin.dashboard')
            ->with('success', 'Quiz created successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(SaveQuizRequest $request, Quiz $quiz)
    {
        $data = $request->validated();
        $data['is_published'] = $request->has('is_published');
        if ($data['attempt_limit_type'] === 'once') {
            $data['max_attempts'] = 1;
        } elseif ($data['attempt_limit_type'] === 'repeatable') {
            $data['max_attempts'] = null;
        }

        $oldPublished = $quiz->is_published;
        $quiz->update($data);

        // Specific logging for publish state transitions
        if ($oldPublished !== $quiz->is_published) {
            $status = $quiz->is_published ? 'published' : 'unpublished';
            ActivityLog::log("quiz_{$status}", "Quiz '{$quiz->title}' was {$status}.");
        } else {
            ActivityLog::log('quiz_updated', "Quiz '{$quiz->title}' details were updated.");
        }

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $title = $quiz->title;
        $quiz->delete();

        ActivityLog::log('quiz_deleted', "Quiz '{$title}' was soft deleted.");

        return redirect()->route('admin.dashboard')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function togglePublish(Quiz $quiz)
    {
        $quiz->is_published = !$quiz->is_published;
        $quiz->save();

        $status = $quiz->is_published ? 'published' : 'unpublishing';
        ActivityLog::log("quiz_{$status}", "Quiz '{$quiz->title}' was " . ($quiz->is_published ? 'published' : 'un-published') . " via dashboard toggle.");

        return redirect()->back()
            ->with('success', "Quiz '{$quiz->title}' status updated successfully.");
    }
}
