<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Attempt;
use App\Models\Answer;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Core Users
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $participant = User::create([
            'name' => 'Haikal Participant',
            'email' => 'participant@example.com',
            'password' => bcrypt('password'),
            'role' => 'participant',
        ]);

        // Create some more dummy users using factory
        $students = User::factory(5)->create([
            'role' => 'participant',
        ]);

        // 2. Create Sample Published Quiz 1 (Web Development)
        $quiz1 = Quiz::create([
            'title' => 'Web Development Fundamentals',
            'description' => 'A quiz covering basic concepts of HTML, CSS, Javascript, and PHP/Laravel.',
            'duration_minutes' => 15,
            'is_published' => true,
            'attempt_limit_type' => 'custom',
            'max_attempts' => 3,
        ]);

        $q1_1 = $quiz1->questions()->create([
            'question_text' => 'What does CSS stand for?',
            'type' => 'multiple_choice',
            'options' => [
                'a' => 'Creative Style Sheets',
                'b' => 'Cascading Style Sheets',
                'c' => 'Computer Style Sheets',
                'd' => 'Colorful Style Sheets',
            ],
            'correct_answer' => 'b',
        ]);

        $q1_2 = $quiz1->questions()->create([
            'question_text' => 'Which of the following is a backend language/framework?',
            'type' => 'multiple_choice',
            'options' => [
                'a' => 'HTML5',
                'b' => 'CSS3',
                'c' => 'Laravel',
                'd' => 'Redux',
            ],
            'correct_answer' => 'c',
        ]);

        $q1_3 = $quiz1->questions()->create([
            'question_text' => 'Explain the concept of Separation of Concerns (SoC) in software engineering, and how it is applied in Laravel applications.',
            'type' => 'essay',
            'options' => null,
            'correct_answer' => 'Separation of Concerns is a design principle for separating a computer program into distinct sections, such that each section addresses a separate concern. In Laravel, this is applied through MVC architecture (Models handle data/business logic, Views handle presentation, Controllers orchestrate request/response flow), Form Requests (validation), and Middleware (HTTP request filtering).',
        ]);

        // 3. Create Sample Published Quiz 2 (SQL & Database Basics)
        $quiz2 = Quiz::create([
            'title' => 'Relational Database Fundamentals',
            'description' => 'Test your basic SQL knowledge, indexing, relationships, and queries.',
            'duration_minutes' => 20,
            'is_published' => true,
            'attempt_limit_type' => 'once',
            'max_attempts' => 1,
        ]);

        $q2_1 = $quiz2->questions()->create([
            'question_text' => 'Which SQL keyword is used to sort the result-set?',
            'type' => 'multiple_choice',
            'options' => [
                'a' => 'SORT BY',
                'b' => 'ORDER BY',
                'c' => 'ALIGN BY',
                'd' => 'SORT',
            ],
            'correct_answer' => 'b',
        ]);

        $q2_2 = $quiz2->questions()->create([
            'question_text' => 'Define what a Foreign Key is and why it is critical in a Relational Database Management System (RDBMS).',
            'type' => 'essay',
            'options' => null,
            'correct_answer' => 'A Foreign Key is a column or group of columns in one table that provides a link between data in two tables. It acts as a cross-reference between tables because it references the primary key of another table, thereby establishing link and enforcing referential integrity.',
        ]);

        // 4. Create Sample Unpublished Quiz 3 (Draft: Advanced Laravel Concepts)
        $quiz3 = Quiz::create([
            'title' => 'Advanced Laravel & Design Patterns',
            'description' => 'Draft quiz covering Repository pattern, Service Container, binding, and event listeners.',
            'duration_minutes' => 30,
            'is_published' => false, // This is unpublished
            'attempt_limit_type' => 'repeatable',
            'max_attempts' => null,
        ]);

        $quiz3->questions()->create([
            'question_text' => 'How do you bind a singleton in the Laravel Service Container?',
            'type' => 'multiple_choice',
            'options' => [
                'a' => '$this->app->bind()',
                'b' => '$this->app->singleton()',
                'c' => '$this->app->instance()',
                'd' => '$this->app->share()',
            ],
            'correct_answer' => 'b',
        ]);

        // 5. Create Student Attempts & Answers
        // Let's create an attempt for Haikal Participant on Quiz 1
        $attempt1 = Attempt::create([
            'user_id' => $participant->id,
            'quiz_id' => $quiz1->id,
            'score' => 67, // initially graded (2 MC correct, essay ungraded)
            'started_at' => now()->subHours(2),
            'completed_at' => now()->subHours(2)->addMinutes(12),
        ]);

        $attempt1->answers()->create([
            'question_id' => $q1_1->id,
            'submitted_answer' => 'b', // Correct
            'is_correct' => true,
        ]);
        $attempt1->answers()->create([
            'question_id' => $q1_2->id,
            'submitted_answer' => 'c', // Correct
            'is_correct' => true,
        ]);
        $attempt1->answers()->create([
            'question_id' => $q1_3->id,
            'submitted_answer' => 'SoC is about dividing the system. In Laravel we use controllers, models, and views to make sure HTML is separated from database queries.',
            'is_correct' => null, // Needs manual grading!
        ]);

        // Let's create attempts for other students
        foreach ($students as $student) {
            // Attempt on Quiz 2
            $attempt = Attempt::create([
                'user_id' => $student->id,
                'quiz_id' => $quiz2->id,
                'score' => 100,
                'started_at' => now()->subDays(2),
                'completed_at' => now()->subDays(2)->addMinutes(10),
            ]);

            $attempt->answers()->create([
                'question_id' => $q2_1->id,
                'submitted_answer' => 'b', // Correct
                'is_correct' => true,
            ]);
            $attempt->answers()->create([
                'question_id' => $q2_2->id,
                'submitted_answer' => 'A link to another table primary key.',
                'is_correct' => true, // Already graded correct
            ]);
        }

        // 6. Create Seeded Activity Logs
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'quiz_created',
            'description' => "Quiz 'Web Development Fundamentals' was created.",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(5),
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'quiz_created',
            'description' => "Quiz 'Relational Database Fundamentals' was created.",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(4),
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'quiz_created',
            'description' => "Quiz 'Advanced Laravel & Design Patterns' (Draft) was created.",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(3),
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'quiz_published',
            'description' => "Quiz 'Web Development Fundamentals' was published.",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(2.5),
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'quiz_published',
            'description' => "Quiz 'Relational Database Fundamentals' was published.",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(2.4),
        ]);

        ActivityLog::create([
            'user_id' => $participant->id,
            'action' => 'user_registered',
            'description' => 'New participant account registered.',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(2.1),
        ]);

        ActivityLog::create([
            'user_id' => $participant->id,
            'action' => 'attempt_started',
            'description' => "Started attempt for Quiz 'Web Development Fundamentals' (Attempt ID: {$attempt1->id}).",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(2),
        ]);

        ActivityLog::create([
            'user_id' => $participant->id,
            'action' => 'attempt_submitted',
            'description' => "Submitted attempt for Quiz 'Web Development Fundamentals' (Attempt ID: {$attempt1->id}). Score: 67%.",
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(1.8),
        ]);
    }
}
