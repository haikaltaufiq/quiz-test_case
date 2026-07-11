<x-app-layout>
    <x-slot:title>Create Quiz - Haikal Test-Case</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-2 pb-4 border-b border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-900 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Create New Quiz</h1>
            <p class="text-xs text-gray-500 mt-1">Configure details for a new interactive assessment.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-md p-6">
            <form action="{{ route('admin.quizzes.store') }}" method="POST" class="space-y-4" x-data="{ attemptType: '{{ old('attempt_limit_type', 'once') }}' }">
                @csrf

                <div>
                    <x-ui.label for="title" value="Quiz Title" />
                    <x-ui.input id="title" name="title" type="text" required value="{{ old('title') }}" placeholder="e.g. Web Security Basics" />
                </div>

                <div>
                    <x-ui.label for="description" value="Description / Instructions" />
                    <textarea id="description" name="description" rows="4" class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900" placeholder="Describe the topics covered or quiz guidelines...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-ui.label for="duration_minutes" value="Duration (in minutes)" />
                        <x-ui.input id="duration_minutes" name="duration_minutes" type="number" min="1" max="480" required value="{{ old('duration_minutes', 15) }}" placeholder="e.g. 30" class="w-full" />
                        <span class="text-[11px] text-gray-400 mt-1 block">Maximum time allowed for students.</span>
                    </div>

                    <div>
                        <x-ui.label for="attempt_limit_type" value="Attempt Limit" />
                        <select id="attempt_limit_type" name="attempt_limit_type" x-model="attemptType" class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                            <option value="once">Once (Single Attempt)</option>
                            <option value="repeatable">Repeatable (Unlimited Attempts)</option>
                            <option value="custom">Custom (Specify limits)</option>
                        </select>
                        <span class="text-[11px] text-gray-400 mt-1 block">Choose how many times a user can take this quiz.</span>
                    </div>
                </div>

                <div x-show="attemptType === 'custom'" x-transition class="p-3 bg-gray-50 border border-gray-150 rounded-md">
                    <x-ui.label for="max_attempts" value="Maximum Attempts Allowed" />
                    <x-ui.input id="max_attempts" name="max_attempts" type="number" min="2" max="100" value="{{ old('max_attempts', 2) }}" placeholder="e.g. 3" class="max-w-[200px]" />
                    <span class="text-[11px] text-gray-400 mt-1 block">Set number of times a user can attempt this assessment.</span>
                </div>

                <div class="flex items-center space-x-2.5 pt-1.5">
                    <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="h-4 w-4 text-gray-900 border-gray-200 rounded focus:ring-gray-950 cursor-pointer">
                    <x-ui.label for="is_published" value="Publish Quiz (Visible to participants)" class="!mb-0 cursor-pointer text-xs font-semibold text-gray-700" />
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-2">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-ui.button type="button" variant="secondary">Cancel</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Create Quiz</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
