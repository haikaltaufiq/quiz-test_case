<x-app-layout>
    <x-slot:title>Quiz Results - {{ $attempt->quiz->title }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <a href="{{ route('participant.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-900 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Result Overview Card -->
        <div class="bg-white border border-gray-200 rounded-md p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider">Quiz Completed</span>
                <h1 class="text-base font-bold text-gray-900 mt-0.5">{{ $attempt->quiz->title }}</h1>
                <p class="text-xs text-gray-500 mt-1">Attempt taken on {{ $attempt->completed_at->format('M d, Y @ H:i') }}</p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-left md:text-right">
                    <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider">Score</span>
                    <span class="text-lg font-bold text-gray-950">{{ $attempt->score ?? 0 }}%</span>
                </div>
                <div class="text-left md:text-right">
                    <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider">Status</span>
                    @php
                        $hasUngraded = $attempt->answers()
                            ->whereHas('question', function($q) { $q->where('type', 'essay'); })
                            ->whereNull('is_correct')
                            ->exists();
                    @endphp
                    @if($hasUngraded)
                        <x-ui.badge variant="yellow">Awaiting Review</x-ui.badge>
                    @else
                        <x-ui.badge variant="green">Graded</x-ui.badge>
                    @endif
                </div>
                <div class="text-[10px] text-gray-400 text-left md:text-right">
                    <span class="font-semibold block uppercase tracking-wider text-gray-400">Duration</span>
                    {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} mins
                </div>
            </div>
        </div>

        @if($hasUngraded)
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3.5 text-xs text-yellow-800 flex items-start space-x-2">
                <svg class="h-4 w-4 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <span class="font-bold">Essay review pending:</span>
                    <p class="mt-0.5">Your score currently reflects only multiple-choice questions. An instructor needs to manually review and grade your essay answers. Once graded, your final score will be updated automatically.</p>
                </div>
            </div>
        @endif

        <!-- Answers Review List -->
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Review Performance Details</h2>

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
                                <x-ui.badge variant="yellow">Awaiting Grade</x-ui.badge>
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

                    <!-- Answers Display -->
                    <div class="border-t border-gray-50 pt-3 pl-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Submitted -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Your Submission</span>
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
                                <div class="p-2 border border-gray-150 rounded bg-gray-50 text-xs text-gray-850 whitespace-pre-line">
                                    {{ $answer->submitted_answer ?? 'No answer submitted.' }}
                                </div>
                            @endif
                        </div>

                        <!-- Correct Key / Grade Status -->
                        <div class="space-y-1">
                            @if($answer->question->type === 'multiple_choice')
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Correct Answer Key</span>
                                <div class="p-2 border border-emerald-100 rounded bg-emerald-50 text-emerald-800 text-xs font-semibold">
                                    <span class="uppercase font-bold mr-1.5">{{ $answer->question->correct_answer }}:</span>
                                    <span>{{ $answer->question->options[$answer->question->correct_answer] ?? '' }}</span>
                                </div>
                            @else
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Grade Status</span>
                                @if(is_null($answer->is_correct))
                                    <div class="p-2 border border-yellow-100 rounded bg-yellow-50 text-yellow-800 text-xs">
                                        Pending instructor evaluation...
                                    </div>
                                @elseif($answer->is_correct)
                                    <div class="p-2 border border-emerald-100 rounded bg-emerald-50 text-emerald-800 text-xs font-semibold">
                                        Marked Correct by examiner
                                    </div>
                                @else
                                    <div class="p-2 border border-red-100 rounded bg-red-50 text-red-800 text-xs font-semibold">
                                        Marked Incorrect by examiner
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
