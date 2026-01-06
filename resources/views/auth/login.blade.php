<!DOCTYPE html>
<html lang="uz" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tizimga Kirish - Kollej Davomat Tizimi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        border: 'var(--border)',
                        background: 'var(--background)',
                        foreground: 'var(--foreground)',
                        primary: { DEFAULT: 'var(--primary)', foreground: 'var(--primary-foreground)' },
                        secondary: { DEFAULT: 'var(--secondary)', foreground: 'var(--secondary-foreground)' },
                        muted: { DEFAULT: 'var(--muted)', foreground: 'var(--muted-foreground)' },
                        card: { DEFAULT: 'var(--card)', foreground: 'var(--card-foreground)' },
                        destructive: { DEFAULT: 'var(--destructive)', foreground: 'var(--destructive-foreground)' },
                        success: { DEFAULT: 'var(--success)', foreground: 'var(--success-foreground)' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --background: #f8fafc;
            --foreground: #0f172a;
            --card: #ffffff;
            --card-foreground: #1e293b;
            --primary: #0891b2;
            --primary-foreground: #ffffff;
            --secondary: #6366f1;
            --secondary-foreground: #ffffff;
            --muted: #f1f5f9;
            --muted-foreground: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --success-foreground: #ffffff;
            --destructive: #ef4444;
            --destructive-foreground: #ffffff;
        }

        .dark {
            --background: #0f172a;
            --foreground: #f1f5f9;
            --card: #1e293b;
            --card-foreground: #e2e8f0;
            --primary: #22d3ee;
            --primary-foreground: #0f172a;
            --secondary: #818cf8;
            --secondary-foreground: #0f172a;
            --muted: #334155;
            --muted-foreground: #94a3b8;
            --border: #334155;
            --success: #34d399;
            --success-foreground: #0f172a;
            --destructive: #f87171;
            --destructive-foreground: #0f172a;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--background);
            color: var(--foreground);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-primary/30 to-secondary/30 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-secondary/30 to-primary/30 rounded-full blur-3xl animate-float" style="animation-delay: 3s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10 animate-slide-up">
        <!-- Theme Toggle -->
        <div class="absolute -top-16 right-0">
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                    class="p-3 rounded-xl bg-card border border-border text-muted-foreground hover:text-foreground transition-colors">
                <i x-show="darkMode" data-lucide="sun" class="w-5 h-5"></i>
                <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary to-secondary rounded-2xl shadow-lg mb-4">
                <i data-lucide="clipboard-check" class="w-10 h-10 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-foreground">Kollej Davomat Tizimi</h1>
            <p class="text-muted-foreground mt-2">Tizimga kirish</p>
        </div>

        <!-- Login Form -->
        <div class="bg-card rounded-2xl shadow-2xl border border-border p-8">
            @if(session('xato'))
            <div class="mb-4 p-4 bg-destructive/10 border border-destructive/30 text-destructive rounded-lg text-sm flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                {{ session('xato') }}
            </div>
            @endif

            @if(session('muvaffaqiyat'))
            <div class="mb-4 p-4 bg-success/10 border border-success/30 text-success rounded-lg text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                {{ session('muvaffaqiyat') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Email manzil
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 bg-muted border border-border rounded-lg text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('email') border-destructive @enderror"
                            placeholder="email@example.com"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                    <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-foreground mb-2">
                        Parol
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            class="w-full pl-10 pr-12 py-3 bg-muted border border-border rounded-lg text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('password') border-destructive @enderror"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-muted-foreground hover:text-foreground">
                            <i x-show="!showPassword" data-lucide="eye" class="w-5 h-5"></i>
                            <i x-show="showPassword" data-lucide="eye-off" class="w-5 h-5"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 rounded border-border text-primary focus:ring-primary"
                    >
                    <label for="remember" class="ml-2 text-sm text-muted-foreground">
                        Meni eslab qol
                    </label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-primary hover:bg-primary/90 text-primary-foreground font-semibold rounded-lg shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 flex items-center justify-center gap-2"
                >
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Kirish
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-muted-foreground text-sm mt-6">
            &copy; {{ date('Y') }} Kollej Davomat Tizimi
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
    </script>
</body>
</html>
