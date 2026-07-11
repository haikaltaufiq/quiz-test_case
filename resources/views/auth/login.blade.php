<x-app-layout>
    <x-slot:title>Sign In - Haikal Test-Case</x-slot:title>

    <div class="flex min-h-[70vh] flex-col justify-center py-6 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="text-center text-xl font-bold tracking-tight text-gray-900">Sign in to your account</h2>
            <p class="mt-2 text-center text-xs text-gray-500">
                Or
                <a href="{{ route('register') }}" class="font-medium text-gray-900 hover:underline">create a new participant account</a>
            </p>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white px-6 py-6 border border-gray-200 rounded-md">
                <form class="space-y-4" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div>
                        <x-ui.label for="email" value="Email Address" />
                        <x-ui.input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="you@example.com" />
                    </div>

                    <div>
                        <x-ui.label for="password" value="Password" />
                        <x-ui.input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••" />
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-gray-950 focus:ring-gray-950 cursor-pointer">
                            <label for="remember" class="ml-2 block text-xs text-gray-600 cursor-pointer">Remember me</label>
                        </div>
                    </div>

                    <div>
                        <x-ui.button type="submit" class="w-full justify-center">
                            Sign In
                        </x-ui.button>
                    </div>
                </form>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <span class="text-xs text-gray-400 font-semibold block uppercase tracking-wider mb-2">Demo Accounts</span>
                    <div class="space-y-1.5 text-xs text-gray-600 bg-gray-50 p-2.5 rounded border border-gray-100">
                        <div>
                            <span class="font-semibold text-gray-700">Administrator:</span> admin@example.com / password
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Participant:</span> participant@example.com / password
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
