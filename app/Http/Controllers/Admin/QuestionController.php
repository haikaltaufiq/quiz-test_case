<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveQuestionRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\ActivityLog;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    public function store(SaveQuestionRequest $request, Quiz $quiz)
    {
        $data = $request->validated();
        
        if ($data['type'] === 'essay') {
            $data['options'] = null;
        }

        $question = $quiz->questions()->create($data);

        ActivityLog::log('question_created', "Added a new {$question->type} question to Quiz '{$quiz->title}'.");

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Question added successfully.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    public function update(SaveQuestionRequest $request, Quiz $quiz, Question $question)
    {
        $data = $request->validated();

        if ($data['type'] === 'essay') {
            $data['options'] = null;
        }

        $question->update($data);

        ActivityLog::log('question_updated', "Updated a question (ID: {$question->id}) in Quiz '{$quiz->title}'.");

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $questionId = $question->id;
        $question->delete();

        ActivityLog::log('question_deleted', "Deleted question (ID: {$questionId}) from Quiz '{$quiz->title}' (Soft Deleted).");

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Question deleted successfully.');
    }
}
