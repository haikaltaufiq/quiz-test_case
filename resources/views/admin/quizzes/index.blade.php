<x-app-layout>
    <x-slot:title>Admin Dashboard - Haikal Test-Case</x-slot:title>

    <div class="space-y-6">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 gap-4">
            <div>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight">Admin Dashboard</h1>
                <p class="text-xs text-gray-500 mt-1">Manage quizzes, configure questions, and grade participant answers.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.quizzes.create') }}">
                    <x-ui.button variant="primary">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Quiz
                    </x-ui.button>
                </a>
            </div>
        </div>

        <!-- Detailed Statistics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1">
                <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Total
                    Quizzes</span>
                <span class="text-xl font-bold text-gray-900 block">{{ $stats['total_quizzes'] }}</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1">
                <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Published
                    Quizzes</span>
                <span class="text-xl font-bold text-gray-900 block">{{ $stats['published_quizzes'] }}</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1">
                <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Total
                    Attempts</span>
                <span class="text-xl font-bold text-gray-900 block">{{ $stats['total_attempts'] }}</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1 border-l-yellow-400 border-l-2">
                <span class="text-[10px] text-yellow-600 font-semibold uppercase tracking-wider block">Needs
                    Review</span>
                <span class="text-xl font-bold text-yellow-700 block">{{ $stats['ungraded_attempts'] }}</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-md p-4 space-y-1 border-l-emerald-400 border-l-2">
                <span class="text-[10px] text-emerald-600 font-semibold uppercase tracking-wider block">Average
                    Score</span>
                <span class="text-xl font-bold text-emerald-700 block">{{ $stats['average_score'] }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Quizzes List (2 columns wide) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Active Quizzes</h2>

                    <!-- Search Input -->
                    <form action="{{ route('admin.dashboard') }}" method="GET"
                        class="flex items-center space-x-2 w-full sm:w-auto">
                        @if ($searchAttempts)
                            <input type="hidden" name="search_attempts" value="{{ $searchAttempts }}">
                        @endif
                        <x-ui.input name="search_quizzes" type="text" placeholder="Search quizzes..."
                            value="{{ $searchQuizzes }}" class="!py-1 !text-xs max-w-[200px]" />
                        <x-ui.button type="submit" variant="secondary"
                            class="!px-2 !py-1 !text-xs">Search</x-ui.button>
                        @if ($searchQuizzes)
                            <a href="{{ route('admin.dashboard', ['search_attempts' => $searchAttempts]) }}"
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
                        <h3 class="mt-2 text-xs font-semibold text-gray-950">No quizzes found</h3>
                        <p class="mt-1 text-xs text-gray-400">Try adjusting your keywords or create a new online
                            assessment quiz.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($quizzes as $quiz)
                            <div
                                class="bg-white border border-gray-200 rounded-md p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:border-gray-300 transition-colors gap-4">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}"
                                            class="text-sm font-semibold text-gray-900 hover:text-gray-700 hover:underline">
                                            {{ $quiz->title }}
                                        </a>
                                        <x-ui.badge variant="gray">{{ $quiz->duration_minutes }} mins</x-ui.badge>
                                        <x-ui.badge :variant="$quiz->questions_count > 0 ? 'green' : 'yellow'">
                                            {{ $quiz->questions_count }}
                                            {{ Str::plural('question', $quiz->questions_count) }}
                                        </x-ui.badge>
                                        @if ($quiz->is_published)
                                            <x-ui.badge variant="green">Published</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="yellow">Draft</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 line-clamp-2 max-w-xl">
                                        {{ $quiz->description ?? 'No description provided.' }}</p>
                                </div>
                                <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                                    <form action="{{ route('admin.quizzes.toggle-publish', $quiz) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if ($quiz->is_published)
                                            <x-ui.button type="submit" variant="secondary"
                                                class="!px-2.5 !py-1 !text-xs !bg-amber-50 hover:!bg-amber-100 !text-amber-800 !border-amber-200">
                                                Revert to Draft
                                            </x-ui.button>
                                        @else
                                            <x-ui.button type="submit" variant="primary"
                                                class="!px-2.5 !py-1 !text-xs !bg-slate-800 hover:!bg-slate-700 !border-slate-600 text-white">
                                                Publish Draft
                                            </x-ui.button>
                                        @endif
                                    </form>
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}">
                                        <x-ui.button variant="secondary" class="!px-2.5 !py-1 !text-xs">View &
                                            Edit</x-ui.button>
                                    </a>
                                    <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this quiz? All associated questions and student attempts will be soft-deleted.')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger"
                                            class="!px-2.5 !py-1 !text-xs">Delete</x-ui.button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $quizzes->appends(['attempts_page' => $attempts->currentPage(), 'search_quizzes' => $searchQuizzes, 'search_attempts' => $searchAttempts])->links() }}
                    </div>
                @endif
            </div>

            <!-- Right Side: Participant Attempts (1 column wide) -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Recent Attempts</h2>

                    <!-- Search Input -->
                    <form action="{{ route('admin.dashboard') }}" method="GET"
                        class="flex items-center space-x-2 w-full sm:w-auto">
                        @if ($searchQuizzes)
                            <input type="hidden" name="search_quizzes" value="{{ $searchQuizzes }}">
                        @endif
                        <x-ui.input name="search_attempts" type="text" placeholder="Search candidate..."
                            value="{{ $searchAttempts }}" class="!py-1 !text-xs max-w-[150px]" />
                        <x-ui.button type="submit" variant="secondary"
                            class="!px-2 !py-1 !text-xs">Search</x-ui.button>
                        @if ($searchAttempts)
                            <a href="{{ route('admin.dashboard', ['search_quizzes' => $searchQuizzes]) }}"
                                class="text-xs text-gray-500 hover:text-gray-900">Clear</a>
                        @endif
                    </form>
                </div>

                @if ($attempts->isEmpty())
                    <div class="border border-dashed border-gray-200 rounded-md p-6 text-center bg-white">
                        <p class="text-xs text-gray-400">No attempts found matching query.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($attempts as $attempt)
                            <div
                                class="bg-white border border-gray-200 rounded-md p-3.5 space-y-3 hover:border-gray-300 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="space-y-0.5">
                                        <span
                                            class="text-xs font-semibold text-gray-950 block">{{ $attempt->user->name }}</span>
                                        <span
                                            class="text-xs text-gray-500 block truncate max-w-[180px]">{{ $attempt->quiz->title }}</span>
                                    </div>
                                    <div class="text-right">
                                        @if ($attempt->completed_at)
                                            @php
                                                $hasUngradedEssays = $attempt
                                                    ->answers()
                                                    ->whereHas('question', function ($q) {
                                                        $q->where('type', 'essay');
                                                    })
                                                    ->whereNull('is_correct')
                                                    ->exists();
                                            @endphp

                                            @if ($hasUngradedEssays)
                                                <x-ui.badge variant="yellow">Needs Review</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="green">{{ $attempt->score }}%</x-ui.badge>
                                            @endif
                                        @else
                                            <x-ui.badge variant="blue">In Progress</x-ui.badge>
                                        @endif
                                        <span
                                            class="text-[10px] text-gray-400 block mt-1">{{ $attempt->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center pt-2.5 border-t border-gray-100">
                                    <span class="text-[10px] text-gray-400">
                                        @if ($attempt->completed_at)
                                            Completed: {{ $attempt->completed_at->format('M d, H:i') }}
                                        @else
                                            Started: {{ $attempt->started_at->format('M d, H:i') }}
                                        @endif
                                    </span>
                                    <a href="{{ route('admin.attempts.show', $attempt) }}">
                                        <x-ui.button variant="secondary" class="!px-2 !py-0.5 !text-[11px]">
                                            {{ $attempt->completed_at && $hasUngradedEssays ? 'Grade Essay' : 'View Detail' }}
                                        </x-ui.button>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $attempts->appends(['quizzes_page' => $quizzes->currentPage(), 'search_quizzes' => $searchQuizzes, 'search_attempts' => $searchAttempts])->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Activity Logs Section (Bottom, Full Width) -->
        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">System Activity Logs</h2>
            <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-xs">
                    <thead class="bg-gray-50 text-gray-400 font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Time</th>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Action</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600">
                        @forelse($activityLogs as $log)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-2.5 whitespace-nowrap text-gray-400">
                                    {{ $log->created_at->format('M d, H:i:s') }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap font-medium text-gray-950">
                                    {{ $log->user ? $log->user->name : 'System' }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    @php
                                        $badgeVar = 'gray';
                                        if (Str::contains($log->action, 'created')) {
                                            $badgeVar = 'blue';
                                        }
                                        if (Str::contains($log->action, 'published')) {
                                            $badgeVar = 'green';
                                        }
                                        if (Str::contains($log->action, 'submitted')) {
                                            $badgeVar = 'green';
                                        }
                                        if (Str::contains($log->action, 'deleted')) {
                                            $badgeVar = 'red';
                                        }
                                        if (Str::contains($log->action, 'graded')) {
                                            $badgeVar = 'yellow';
                                        }
                                    @endphp
                                    <x-ui.badge :variant="$badgeVar">{{ $log->action }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-2.5">{{ $log->description }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-gray-400">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400 italic">No activities
                                    logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
