<x-app-layout>
    <x-slot:title>Register - Haikal Test-Case</x-slot:title>

    <div class="flex min-h-[70vh] flex-col justify-center py-6 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="text-center text-xl font-bold tracking-tight text-gray-900">Create participant account</h2>
            <p class="mt-2 text-center text-xs text-gray-500">
                Or
                <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:underline">sign in to your existing account</a>
            </p>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white px-6 py-6 border border-gray-200 rounded-md">
                <form class="space-y-4" action="{{ route('register') }}" method="POST">
                    @csrf

                    <div>
                        <x-ui.label for="name" value="Full Name" />
                        <x-ui.input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" placeholder="John Doe" />
                    </div>
                    
                    <div>
                        <x-ui.label for="email" value="Email Address" />
                        <x-ui.input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="you@example.com" />
                    </div>

                    <div>
                        <x-ui.label for="password" value="Password" />
                        <x-ui.input id="password" name="password" type="password" required placeholder="Min. 8 characters" />
                    </div>

                    <div>
                        <x-ui.label for="password_confirmation" value="Confirm Password" />
                        <x-ui.input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Re-enter password" />
                    </div>

                    <div>
                        <x-ui.button type="submit" class="w-full justify-center">
                            Register
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
