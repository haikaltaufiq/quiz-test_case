<x-app-layout>
    <x-slot:title>Review Attempt - {{ $attempt->user->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-900 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Attempt Metadata -->
        <div class="bg-white border border-gray-200 rounded-md p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider">Participant Attempt</span>
                <h1 class="text-base font-bold text-gray-900 mt-0.5">{{ $attempt->user->name }}</h1>
                <p class="text-xs text-gray-500 mt-1">Quiz: <span class="font-medium text-gray-700">{{ $attempt->quiz->title }}</span></p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-left md:text-right">
                    <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider">Score</span>
                    <span class="text-lg font-bold text-gray-950">{{ $attempt->score ?? 0 }}%</span>
                </div>
                <div class="text-left md:text-right">
                    <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider">Status</span>
                    @if($attempt->completed_at)
                        @php
                            $hasUngraded = $attempt->answers()
                                ->whereHas('question', function($q) { $q->where('type', 'essay'); })
                                ->whereNull('is_correct')
                                ->exists();
                        @endphp
                        @if($hasUngraded)
                            <x-ui.badge variant="yellow">Needs Review</x-ui.badge>
                        @else
                            <x-ui.badge variant="green">Graded</x-ui.badge>
                        @endif
                    @else
                        <x-ui.badge variant="blue">In Progress</x-ui.badge>
                    @endif
                </div>
                <div class="text-left md:text-right text-xs text-gray-500">
                    <span class="text-[10px] text-gray-400 font-semibold block uppercase tracking-wider">Timeline</span>
                    Started: {{ $attempt->started_at->format('M d, H:i') }}<br>
                    @if($attempt->completed_at)
                        Finished: {{ $attempt->completed_at->format('M d, H:i') }} ({{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} mins)
                    @endif
                </div>
            </div>
        </div>

        <!-- Grading Form (Wraps questions) -->
        <form action="{{ route('admin.attempts.grade', $attempt) }}" method="POST" class="space-y-4">
            @csrf

            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Submitted Answers</h2>

            <div class="space-y-4">
                @foreach($attempt->answers as $index => $answer)
                    <div class="bg-white border border-gray-200 rounded-md p-4 space-y-3 relative pr-52">
                        <!-- Badges (Top Right) -->
                        <div class="absolute top-4 right-4 flex items-center space-x-1.5">
                            <x-ui.badge :variant="$answer->question->type === 'multiple_choice' ? 'blue' : 'yellow'">
                                {{ $answer->question->type === 'multiple_choice' ? 'Multiple Choice' : 'Essay' }}
                            </x-ui.badge>
                            
                            @if($answer->question->type === 'multiple_choice')
                                @if($answer->is_correct)
                                    <x-ui.badge variant="green">Correct</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red">Incorrect</x-ui.badge>
                                @endif
                            @else
                                @if(is_null($answer->is_correct))
                                    <x-ui.badge variant="yellow">Awaiting Review</x-ui.badge>
                                @elseif($answer->is_correct)
                                    <x-ui.badge variant="green">Correct</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red">Incorrect</x-ui.badge>
                                @endif
                            @endif
                        </div>

                        <!-- Question Text -->
                        <div class="flex items-start">
                            <p class="text-sm font-semibold text-gray-900 whitespace-pre-line">{{ $index + 1 }}. {{ $answer->question->question_text }}</p>
                        </div>

                        <!-- Answer Comparison Section -->
                        <div class="border-t border-gray-50 pt-3 pl-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Left: Submitted Answer -->
                            <div class="space-y-1">
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Submitted Answer</span>
                                @if($answer->question->type === 'multiple_choice')
                                    <div class="p-2 border border-gray-150 rounded bg-gray-50 text-xs">
                                        @if($answer->submitted_answer)
                                            <span class="uppercase font-bold mr-1.5">{{ $answer->submitted_answer }}:</span>
                                            <span>{{ $answer->question->options[$answer->submitted_answer] ?? 'Unknown option' }}</span>
                                        @else
                                            <span class="text-gray-400 italic">No answer submitted.</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="p-2 border border-gray-150 rounded bg-gray-50 text-xs text-gray-800 whitespace-pre-line">
                                        {{ $answer->submitted_answer ?? 'No answer submitted.' }}
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Correct Answer / Grading Controls -->
                            <div class="space-y-1.5">
                                @if($answer->question->type === 'multiple_choice')
                                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Correct Key</span>
                                    <div class="p-2 border border-emerald-100 rounded bg-emerald-50 text-emerald-800 text-xs font-semibold">
                                        <span class="uppercase font-bold mr-1.5">{{ $answer->question->correct_answer }}:</span>
                                        <span>{{ $answer->question->options[$answer->question->correct_answer] ?? '' }}</span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Ideal Answer Guide</span>
                                    <div class="p-2 border border-gray-150 rounded bg-white text-xs text-gray-600 whitespace-pre-line mb-3">
                                        {{ $answer->question->correct_answer }}
                                    </div>

                                    @if($attempt->completed_at)
                                        <!-- Grading Controls for Essays -->
                                        <div class="flex items-center space-x-4 p-2 bg-gray-50 border border-gray-100 rounded-md">
                                            <span class="text-xs font-medium text-gray-700">Mark as:</span>
                                            <div class="flex items-center space-x-3">
                                                <label class="inline-flex items-center cursor-pointer text-xs text-gray-700">
                                                    <input type="radio" name="grades[{{ $answer->id }}]" value="correct" {{ $answer->is_correct === true ? 'checked' : '' }} class="h-3.5 w-3.5 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                                    <span class="ml-1.5 font-medium text-emerald-700">Correct</span>
                                                </label>
                                                <label class="inline-flex items-center cursor-pointer text-xs text-gray-700">
                                                    <input type="radio" name="grades[{{ $answer->id }}]" value="incorrect" {{ $answer->is_correct === false ? 'checked' : '' }} class="h-3.5 w-3.5 text-red-600 focus:ring-red-500 border-gray-300">
                                                    <span class="ml-1.5 font-medium text-red-700">Incorrect</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Grade Submission Footer -->
            @if($attempt->completed_at && $attempt->quiz->questions()->where('type', 'essay')->exists())
                <div class="bg-white border border-gray-200 rounded-md p-4 flex justify-between items-center mt-6">
                    <p class="text-xs text-gray-500">Grading essay questions will update the candidate's total score.</p>
                    <x-ui.button type="submit" variant="primary">
                        Submit Grades & Update Score
                    </x-ui.button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>
