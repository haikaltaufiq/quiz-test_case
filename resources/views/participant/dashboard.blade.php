<x-app-layout>
    <x-slot:title>Dashboard - Haikal Test-Case</x-slot:title>

    <div class="space-y-6">
        <!-- Welcome banner -->
        <div class="pb-4 border-b border-gray-200">
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Participant Dashboard</h1>
            <p class="text-xs text-gray-500 mt-1">Select an active quiz below or track your attempt performance history.
            </p>
        </div>

        <!-- Detailed Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1">
                <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Completed
                    Quizzes</span>
                <span class="text-xl font-bold text-gray-900 block">{{ $stats['completed'] }}</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1 border-l-emerald-400 border-l-2">
                <span class="text-[10px] text-emerald-600 font-semibold uppercase tracking-wider block">Average
                    Score</span>
                <span class="text-xl font-bold text-emerald-700 block">{{ $stats['average_score'] }}%</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1 border-l-yellow-400 border-l-2">
                <span class="text-[10px] text-yellow-600 font-semibold uppercase tracking-wider block">Pending Grading
                    Reviews</span>
                <span class="text-xl font-bold text-yellow-700 block">{{ $stats['pending_review'] }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Available Quizzes (2 columns) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Available Assessments</h2>

                    <!-- Search Input -->
                    <form action="{{ route('participant.dashboard') }}" method="GET"
                        class="flex items-center space-x-2 w-full sm:w-auto">
                        <x-ui.input name="search" type="text" placeholder="Search assessments..."
                            value="{{ $search }}" class="!py-1 !text-xs max-w-[200px]" />
                        <x-ui.button type="submit" variant="secondary"
                            class="!px-2 !py-1 !text-xs">Search</x-ui.button>
                        @if ($search)
                            <a href="{{ route('participant.dashboard') }}"
                                class="text-xs text-gray-500 hover:text-gray-900">Clear</a>
                        @endif
                    </form>
                </div>

                @if ($quizzes->isEmpty())
                    <div class="border border-dashed border-gray-200 rounded-md p-8 text-center bg-white">
                        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <h3 class="mt-2 text-xs font-semibold text-gray-950">No quizzes available</h3>
                        <p class="mt-1 text-xs text-gray-400">There are no quizzes configured matching your criteria at
                            the moment.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($quizzes as $quiz)
                            <div
                                class="bg-white border border-gray-200 rounded-md p-4 flex flex-col justify-between hover:border-gray-300 transition-colors">
                                <div class="space-y-2">
                                    <div class="flex items-start justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900 leading-5">{{ $quiz->title }}
                                        </h3>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                                        <x-ui.badge variant="gray">{{ $quiz->duration_minutes }} mins</x-ui.badge>
                                        <x-ui.badge variant="blue">{{ $quiz->questions_count }}
                                            {{ Str::plural('question', $quiz->questions_count) }}</x-ui.badge>
                                        @if ($quiz->attempt_limit_type === 'once')
                                            <x-ui.badge variant="gray">Single Attempt</x-ui.badge>
                                        @elseif($quiz->attempt_limit_type === 'repeatable')
                                            <x-ui.badge variant="green">Repeatable</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="yellow">Max {{ $quiz->max_attempts }}
                                                Attempts</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-3">
                                        {{ $quiz->description ?? 'No description provided.' }}</p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-400">
                                        @if ($quiz->attempt_limit_type === 'custom')
                                            Attempts: {{ $quiz->completed_attempts_count }}/{{ $quiz->max_attempts }}
                                        @elseif($quiz->attempt_limit_type === 'once')
                                            Attempts: {{ $quiz->completed_attempts_count }}/1
                                        @else
                                            Taken: {{ $quiz->completed_attempts_count }} times
                                        @endif
                                    </span>

                                    @if ($quiz->questions_count > 0)
                                        @if ($quiz->active_attempt)
                                            <a href="{{ route('participant.attempts.take', $quiz->active_attempt) }}">
                                                <x-ui.button variant="primary"
                                                    class="!px-3 !py-1 !text-xs !bg-amber-600 hover:!bg-amber-700 !border-amber-600 text-white animate-pulse">
                                                    Resume Attempt
                                                </x-ui.button>
                                            </a>
                                        @elseif($quiz->is_limit_reached)
                                            <span
                                                class="text-[11px] font-semibold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded">{{ $quiz->limit_message }}</span>
                                        @else
                                            <form action="{{ route('participant.quizzes.start', $quiz) }}"
                                                method="POST">
                                                @csrf
                                                <x-ui.button type="submit" variant="primary"
                                                    class="!px-3 !py-1 !text-xs">
                                                    {{ $quiz->attempt_limit_type === 'repeatable' && $quiz->completed_attempts_count > 0 ? 'Retake Assessment' : 'Start Assessment' }}
                                                </x-ui.button>
                                            </form>
                                        @endif
                                    @else
                                        <x-ui.badge variant="gray">No Questions</x-ui.badge>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Performance History (1 column) -->
            <div class="space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Your Attempts</h2>

                @if ($attempts->isEmpty())
                    <div class="border border-dashed border-gray-200 rounded-md p-6 text-center bg-white">
                        <p class="text-xs text-gray-400">You haven't attempted any quizzes yet.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($attempts as $attempt)
                            <div
                                class="bg-white border border-gray-200 rounded-md p-3.5 space-y-3 hover:border-gray-300 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="space-y-0.5">
                                        <span
                                            class="text-xs font-semibold text-gray-950 block truncate max-w-[180px]">{{ $attempt->quiz->title }}</span>
                                        <span class="text-[10px] text-gray-400 block">
                                            {{ $attempt->completed_at ? 'Last finished: ' . $attempt->completed_at->format('M d, Y - H:i') : 'Started: ' . $attempt->created_at->format('M d, Y - H:i') }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        @if ($attempt->completed_at)
                                            @php
                                                $hasUngraded = $attempt
                                                    ->answers()
                                                    ->whereHas('question', function ($q) {
                                                        $q->where('type', 'essay');
                                                    })
                                                    ->whereNull('is_correct')
                                                    ->exists();
                                            @endphp

                                            @if ($hasUngraded)
                                                <x-ui.badge variant="yellow">Awaiting Review</x-ui.badge>
                                                <span class="text-[10px] text-gray-400 block mt-1">Score:
                                                    {{ $attempt->score }}% (MC)</span>
                                            @else
                                                <x-ui.badge variant="green">{{ $attempt->score }}% Score</x-ui.badge>
                                            @endif
                                        @else
                                            <x-ui.badge variant="blue">In Progress</x-ui.badge>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-between items-center pt-2.5 border-t border-gray-100">
                                    <span class="text-[10px] text-gray-400">
                                        @if ($attempt->completed_at)
                                            Took: {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }}m
                                        @else
                                            Running
                                        @endif
                                    </span>
                                    @if ($attempt->completed_at)
                                        <a href="{{ route('participant.attempts.show', $attempt) }}">
                                            <x-ui.button variant="secondary" class="!px-2 !py-0.5 !text-[11px]">View
                                                Results</x-ui.button>
                                        </a>
                                    @else
                                        <a href="{{ route('participant.attempts.take', $attempt) }}">
                                            <x-ui.button variant="primary"
                                                class="!px-2 !py-0.5 !text-[11px]">Resume</x-ui.button>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
