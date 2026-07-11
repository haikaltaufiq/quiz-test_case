<x-app-layout>
    <x-slot:title>Add Question - {{ $quiz->title }}</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-2 pb-4 border-b border-gray-200">
            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-xs text-gray-500 hover:text-gray-900 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Quiz details
            </a>
        </div>

        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Add New Question</h1>
            <p class="text-xs text-gray-500 mt-1">Configure a new question for "{{ $quiz->title }}".</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-md p-6" x-data="{ type: '{{ old('type', 'multiple_choice') }}' }">
            <form action="{{ route('admin.questions.store', $quiz) }}" method="POST" class="space-y-5">
                @csrf

                <!-- Question Text -->
                <div>
                    <x-ui.label for="question_text" value="Question Text" />
                    <textarea id="question_text" name="question_text" rows="3" required class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900" placeholder="Type the question content here...">{{ old('question_text') }}</textarea>
                </div>

                <!-- Question Type -->
                <div>
                    <x-ui.label for="type" value="Question Type" />
                    <select id="type" name="type" x-model="type" class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                <!-- Multiple Choice Config Section -->
                <div x-show="type === 'multiple_choice'" class="space-y-4 border-l-2 border-gray-200 pl-4 py-1" x-transition>
                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Configure Options</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-ui.label for="option_a" value="Option A" />
                            <x-ui.input id="option_a" name="options[a]" type="text" value="{{ old('options.a') }}" placeholder="Enter option A value" ::required="type === 'multiple_choice'" />
                        </div>
                        <div>
                            <x-ui.label for="option_b" value="Option B" />
                            <x-ui.input id="option_b" name="options[b]" type="text" value="{{ old('options.b') }}" placeholder="Enter option B value" ::required="type === 'multiple_choice'" />
                        </div>
                        <div>
                            <x-ui.label for="option_c" value="Option C" />
                            <x-ui.input id="option_c" name="options[c]" type="text" value="{{ old('options.c') }}" placeholder="Enter option C value" ::required="type === 'multiple_choice'" />
                        </div>
                        <div>
                            <x-ui.label for="option_d" value="Option D" />
                            <x-ui.input id="option_d" name="options[d]" type="text" value="{{ old('options.d') }}" placeholder="Enter option D value" ::required="type === 'multiple_choice'" />
                        </div>
                    </div>

                    <div class="max-w-[200px]">
                        <x-ui.label for="correct_answer_mc" value="Correct Option" />
                        <select id="correct_answer_mc" name="correct_answer" class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900" ::required="type === 'multiple_choice'" :disabled="type !== 'multiple_choice'">
                            <option value="a" {{ old('correct_answer') === 'a' ? 'selected' : '' }}>Option A</option>
                            <option value="b" {{ old('correct_answer') === 'b' ? 'selected' : '' }}>Option B</option>
                            <option value="c" {{ old('correct_answer') === 'c' ? 'selected' : '' }}>Option C</option>
                            <option value="d" {{ old('correct_answer') === 'd' ? 'selected' : '' }}>Option D</option>
                        </select>
                    </div>
                </div>

                <!-- Essay Config Section -->
                <div x-show="type === 'essay'" class="space-y-3 border-l-2 border-gray-200 pl-4 py-1" x-transition>
                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Configure Answer Key</h3>
                    
                    <div>
                        <x-ui.label for="correct_answer_essay" value="Correct Answer / Guide" />
                        <textarea id="correct_answer_essay" name="correct_answer" rows="4" class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900" placeholder="Type the keywords or ideal response details to assist manual grading..." ::required="type === 'essay'" :disabled="type !== 'essay'">{{ old('correct_answer') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-2">
                    <a href="{{ route('admin.quizzes.show', $quiz) }}">
                        <x-ui.button type="button" variant="secondary">Cancel</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Add Question</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
