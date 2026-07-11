<x-app-layout>
    <x-slot:title>Taking Quiz - {{ $quiz->title }}</x-slot:title>

    @php
        $totalSeconds = $quiz->duration_minutes * 60;
        $elapsedSeconds = max(0, now()->timestamp - $attempt->started_at->timestamp);
        $remainingSeconds = (int) max(0, $totalSeconds - $elapsedSeconds);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ 
        timeLeft: Math.floor({{ $remainingSeconds }}),
        formatTime() {
            if (this.timeLeft <= 0) return '0:00';
            const minutes = Math.floor(this.timeLeft / 60);
            const seconds = this.timeLeft % 60;
            return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        }
    }" x-init="setInterval(() => { 
        if (timeLeft > 0) {
            timeLeft--;
            if (timeLeft === 0) {
                alert('Time is up! Your quiz will be automatically submitted.');
                document.getElementById('quiz-form').submit();
            }
        }
    }, 1000)">

        <!-- Left/Main: Questions list (3 cols wide) -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white border border-gray-200 rounded-md p-4">
                <h1 class="text-base font-bold text-gray-900">{{ $quiz->title }}</h1>
                @if($quiz->description)
                    <p class="text-xs text-gray-500 mt-1 whitespace-pre-line">{{ $quiz->description }}</p>
                @endif
            </div>

            <!-- Quiz taking form -->
            <form id="quiz-form" action="{{ route('participant.attempts.submit', $attempt) }}" method="POST" class="space-y-4">
                @csrf

                @foreach($quiz->questions as $index => $question)
                    <div class="bg-white border border-gray-200 rounded-md p-4 space-y-4 relative pr-36">
                        <!-- Question Badge (Top Right) -->
                        <div class="absolute top-4 right-4">
                            <x-ui.badge :variant="$question->type === 'multiple_choice' ? 'blue' : 'yellow'">
                                {{ $question->type === 'multiple_choice' ? 'Multiple Choice' : 'Essay' }}
                            </x-ui.badge>
                        </div>

                        <!-- Question Text -->
                        <div class="flex items-start">
                            <p class="text-sm font-semibold text-gray-900 whitespace-pre-line">{{ $index + 1 }}. {{ $question->question_text }}</p>
                        </div>

                        <!-- Inputs dynamic rendering -->
                        <div class="border-t border-gray-50 pt-3 pl-6">
                            @if($question->type === 'multiple_choice')
                                <div class="space-y-2">
                                    @foreach($question->options as $key => $optionValue)
                                        <label class="flex items-start space-x-3 p-2 rounded border border-gray-100 bg-white hover:bg-gray-50 cursor-pointer transition-colors text-xs text-gray-700">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="h-4 w-4 text-gray-900 border-gray-300 focus:ring-gray-950 mt-0.5">
                                            <span class="flex-1">
                                                <span class="uppercase font-bold mr-1.5">{{ $key }}.</span>
                                                {{ $optionValue }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-1.5">
                                    <x-ui.label for="ans_{{ $question->id }}" value="Your Response" />
                                    <textarea id="ans_{{ $question->id }}" name="answers[{{ $question->id }}]" rows="5" class="block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900" placeholder="Type your full explanatory essay response here..."></textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Submit Banner -->
                <div class="bg-white border border-gray-200 rounded-md p-4 flex justify-between items-center">
                    <span class="text-xs text-gray-400">Please review all your answers before submitting.</span>
                    <x-ui.button type="submit" variant="primary" onclick="return confirm('Are you sure you want to finish and submit your quiz answers?')">
                        Finish & Submit Quiz
                    </x-ui.button>
                </div>
            </form>
        </div>

        <!-- Right/Sidebar: Sticky timer & info (1 col wide) -->
        <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-md p-4 sticky top-20 space-y-4">
                <div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Remaining Time</span>
                    <div class="text-2xl font-bold text-gray-950 tracking-tight mt-1 flex items-center space-x-2" :class="timeLeft < 60 ? 'text-red-600 animate-pulse' : ''">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="formatTime()"></span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-3 text-xs text-gray-500 space-y-2">
                    <div>
                        <span class="font-semibold block text-gray-700">Instructions:</span>
                        <ul class="list-disc list-inside space-y-1 text-gray-500 mt-1 pl-1 text-[11px]">
                            <li>Do not refresh or close this browser tab during active exam.</li>
                            <li>Timer will auto-submit when remaining minutes reach zero.</li>
                            <li>Multiple choice is graded instantly. Essay requires manual review by examiner.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
