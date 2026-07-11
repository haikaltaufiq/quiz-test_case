<x-app-layout>
    <x-slot:title>{{ $quiz->title }} - Quiz Details</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-900 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.quizzes.edit', $quiz) }}">
                    <x-ui.button variant="secondary" class="!px-2.5 !py-1 !text-xs">Edit Details</x-ui.button>
                </a>
                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz? All questions and participant results will be lost.')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" class="!px-2.5 !py-1 !text-xs">Delete Quiz</x-ui.button>
                </form>
            </div>
        </div>

        <!-- Quiz Details Card -->
        <div class="bg-white border border-gray-200 rounded-md p-4 space-y-3">
            <div class="flex items-center space-x-2">
                <h1 class="text-base font-bold text-gray-900">{{ $quiz->title }}</h1>
                <x-ui.badge variant="blue">{{ $quiz->duration_minutes }} minutes</x-ui.badge>
                <x-ui.badge variant="gray">{{ $quiz->questions->count() }} questions</x-ui.badge>
            </div>
            
            @if($quiz->description)
                <div class="text-sm text-gray-600 border-t border-gray-100 pt-3">
                    <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Description</span>
                    <p class="whitespace-pre-line">{{ $quiz->description }}</p>
                </div>
            @endif
        </div>

        <!-- Question List Header -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div>
                <h2 class="text-sm font-semibold text-gray-950 uppercase tracking-wider">Quiz Questions</h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage questions inside this quiz.</p>
            </div>
            <a href="{{ route('admin.questions.create', $quiz) }}">
                <x-ui.button variant="primary">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Question
                </x-ui.button>
            </a>
        </div>

        <!-- Questions list -->
        @if($quiz->questions->isEmpty())
            <div class="border border-dashed border-gray-200 rounded-md p-8 text-center bg-white">
                <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-xs font-semibold text-gray-950">No questions added yet</h3>
                <p class="mt-1 text-xs text-gray-400">Add multiple choice or essay questions to build this assessment.</p>
                <div class="mt-4">
                    <a href="{{ route('admin.questions.create', $quiz) }}">
                        <x-ui.button variant="secondary" class="!text-xs">Add Question</x-ui.button>
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($quiz->questions as $index => $question)
                    <div class="bg-white border border-gray-200 rounded-md p-4 space-y-3 relative pr-36">
                        <!-- Question Badge (Top Right) -->
                        <div class="absolute top-4 right-4">
                            <x-ui.badge :variant="$question->type === 'multiple_choice' ? 'blue' : 'yellow'">
                                {{ $question->type === 'multiple_choice' ? 'Multiple Choice' : 'Essay' }}
                            </x-ui.badge>
                        </div>

                        <!-- Question Text -->
                        <div class="flex items-start">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-gray-900 whitespace-pre-line">{{ $index + 1 }}. {{ $question->question_text }}</p>
                            </div>
                        </div>

                        <!-- Options or Answer Details -->
                        <div class="border-t border-gray-50 pt-3 pl-6">
                            @if($question->type === 'multiple_choice')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                    @foreach($question->options as $key => $optionValue)
                                        <div class="flex items-center space-x-2 p-2 rounded border {{ $question->correct_answer === $key ? 'bg-emerald-50 border-emerald-200 text-emerald-800 font-semibold' : 'bg-white border-gray-100 text-gray-600' }}">
                                            <span class="uppercase font-bold">{{ $key }}.</span>
                                            <span>{{ $optionValue }}</span>
                                            @if($question->correct_answer === $key)
                                                <svg class="h-3.5 w-3.5 text-emerald-600 ml-auto" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-100 rounded-md p-2.5">
                                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Answer Guide / Ideal Response</span>
                                    <p class="text-xs text-gray-700 whitespace-pre-line">{{ $question->correct_answer }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons Footer -->
                        <div class="flex justify-end space-x-1.5 pt-2.5 border-t border-gray-50 mt-2">
                            <a href="{{ route('admin.questions.edit', [$quiz, $question]) }}">
                                <x-ui.button variant="secondary" class="!px-2.5 !py-1 !text-xs">Edit</x-ui.button>
                            </a>
                            <form action="{{ route('admin.questions.destroy', [$quiz, $question]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="danger" class="!px-2.5 !py-1 !text-xs">Delete</x-ui.button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
