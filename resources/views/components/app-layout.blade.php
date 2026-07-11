<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Haikal Test-Case' }}</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-gray-900 bg-white antialiased flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 items-center">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <a href="/" class="flex items-center space-x-2">
                        <svg class="h-6 w-6 text-gray-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-bold text-sm tracking-tight text-gray-900">Haikal Test-Case</span>
                    </a>
                    
                    @auth
                        <span class="text-gray-300">|</span>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Admin Dashboard</a>
                        @else
                            <a href="{{ route('participant.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Participant Dashboard</a>
                        @endif
                    @endauth
                </div>

                <!-- User Info & Logout -->
                @auth
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-right">
                            <span class="text-xs font-medium text-gray-950">{{ auth()->user()->name }}</span>
                            <x-ui.badge :variant="auth()->user()->role === 'admin' ? 'blue' : 'gray'">
                                {{ ucfirst(auth()->user()->role) }}
                            </x-ui.badge>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" class="!px-2.5 !py-1 !text-xs">
                                Logout
                            </x-ui.button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900 px-3 py-1.5">Sign In</a>
                        <a href="{{ route('register') }}" class="text-xs font-medium bg-gray-900 text-white rounded-md px-3 py-1.5 hover:bg-gray-800">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Success Alert -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-md flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        <!-- Error Alert -->
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-md flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-md">
                <div class="flex justify-between items-start">
                    <div class="flex items-start space-x-2">
                        <svg class="h-4 w-4 text-red-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-semibold block">Please fix the following errors:</span>
                            <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} M Taufiq Karim Haikal. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
