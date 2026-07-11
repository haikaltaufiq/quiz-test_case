<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function checkAndClose()
    {
        if ($this->completed_at) {
            return;
        }

        $totalSeconds = $this->quiz->duration_minutes * 60;
        $elapsedSeconds = max(0, now()->timestamp - $this->started_at->timestamp);

        if ($elapsedSeconds >= $totalSeconds) {
            $questions = $this->quiz->questions;
            $correctCount = 0;
            $totalQuestions = $questions->count();

            foreach ($questions as $question) {
                $answer = $this->answers()->where('question_id', $question->id)->first();
                if ($answer && $question->type === 'multiple_choice') {
                    if (trim(strtolower($answer->submitted_answer)) === trim(strtolower($question->correct_answer))) {
                        $correctCount++;
                    }
                }
            }

            $this->score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 100;
            $this->completed_at = $this->started_at->copy()->addMinutes($this->quiz->duration_minutes);
            $this->save();

            ActivityLog::log('attempt_auto_submitted', "Attempt ID: {$this->id} was automatically submitted due to time expiration.");
        }
    }
}
