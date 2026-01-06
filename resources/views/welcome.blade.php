<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kollej Davomat Tizimi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --primary: #0891b2;
            --primary-hover: #0e7490;
            --background: #f8fafc;
            --foreground: #0f172a;
            --card: #ffffff;
            --border: #e2e8f0;
        }
        .dark {
            --primary: #22d3ee;
            --primary-hover: #06b6d4;
            --background: #0f172a;
            --foreground: #f1f5f9;
            --card: #1e293b;
            --border: #334155;
        }
        body {
            background-color: var(--background);
            color: var(--foreground);
        }

        .hero-gradient {
            background: linear-gradient(135deg,
                rgba(8, 145, 178, 0.1) 0%,
                rgba(34, 211, 238, 0.05) 50%,
                rgba(251, 191, 36, 0.05) 100%);
        }
        .dark .hero-gradient {
            background: linear-gradient(135deg,
                rgba(34, 211, 238, 0.15) 0%,
                rgba(8, 145, 178, 0.1) 50%,
                rgba(251, 191, 36, 0.1) 100%);
        }

        .feature-card {
            background-color: var(--card);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(8, 145, 178, 0.2);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
    </style>
</head>
<body class="min-h-screen antialiased">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-lg border-b" style="border-color: var(--border); background: var(--card); background-opacity: 0.9;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold" style="color: var(--foreground);">Kollej DT</span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                            class="p-2 rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-gray-800">
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5" style="color: var(--foreground);"></i>
                        <i data-lucide="moon" x-show="!darkMode" x-cloak class="w-5 h-5" style="color: var(--foreground);"></i>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary px-5 py-2 rounded-lg font-medium">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary px-5 py-2 rounded-lg font-medium flex items-center gap-2">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                Kirish
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden hero-gradient">
        <!-- Floating Shapes -->
        <div class="floating-shape w-72 h-72 bg-cyan-500 -top-20 -left-20" style="animation-delay: 0s;"></div>
        <div class="floating-shape w-96 h-96 bg-teal-500 -bottom-32 -right-32" style="animation-delay: 2s;"></div>
        <div class="floating-shape w-48 h-48 bg-amber-500 top-1/2 left-1/4" style="animation-delay: 4s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium mb-6" style="background: var(--card); border: 1px solid var(--border);">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                    <span style="color: var(--foreground);">Zamonaviy va qulay tizim</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight" style="color: var(--foreground);">
                    Kollej Talabalar<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-teal-500">Davomat Tizimi</span>
                </h1>

                <p class="text-lg sm:text-xl max-w-2xl mx-auto mb-10 text-gray-600 dark:text-gray-400">
                    Talabalar davomatini oson va samarali boshqarish. Kundalik, haftalik va oylik hisobotlar. Professional Excel eksport.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-3 rounded-xl font-semibold flex items-center gap-2 text-lg">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            Dashboardga O'tish
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-8 py-3 rounded-xl font-semibold flex items-center gap-2 text-lg">
                            <i data-lucide="log-in" class="w-5 h-5"></i>
                            Tizimga Kirish
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Stats -->
            <div class="mt-20 grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center p-6 rounded-2xl" style="background: var(--card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-cyan-500"></i>
                    </div>
                    <p class="text-3xl font-bold" style="color: var(--foreground);">500+</p>
                    <p class="text-sm text-gray-500">Talabalar</p>
                </div>
                <div class="text-center p-6 rounded-2xl" style="background: var(--card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-teal-500/10 flex items-center justify-center">
                        <i data-lucide="book-open" class="w-6 h-6 text-teal-500"></i>
                    </div>
                    <p class="text-3xl font-bold" style="color: var(--foreground);">20+</p>
                    <p class="text-sm text-gray-500">Guruhlar</p>
                </div>
                <div class="text-center p-6 rounded-2xl" style="background: var(--card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-amber-500/10 flex items-center justify-center">
                        <i data-lucide="clipboard-check" class="w-6 h-6 text-amber-500"></i>
                    </div>
                    <p class="text-3xl font-bold" style="color: var(--foreground);">1000+</p>
                    <p class="text-sm text-gray-500">Davomat yozuvlari</p>
                </div>
                <div class="text-center p-6 rounded-2xl" style="background: var(--card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                    <p class="text-3xl font-bold" style="color: var(--foreground);">100+</p>
                    <p class="text-sm text-gray-500">Eksport fayllar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20" style="background: var(--background);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4" style="color: var(--foreground);">
                    Asosiy Imkoniyatlar
                </h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                    Tizimning barcha funksiyalari bir joyda
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center mb-4">
                        <i data-lucide="clipboard-list" class="w-7 h-7 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--foreground);">Davomat Olish</h3>
                    <p class="text-gray-500">
                        Har bir para uchun talabalar davomatini tez va qulay belgilash. Real vaqtda statistika.
                    </p>
                </div>

                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center mb-4">
                        <i data-lucide="users" class="w-7 h-7 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--foreground);">Guruhlar Boshqaruvi</h3>
                    <p class="text-gray-500">
                        Guruhlarni yaratish, tahrirlash va talabalarni guruhga biriktirish.
                    </p>
                </div>

                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-4">
                        <i data-lucide="user-plus" class="w-7 h-7 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--foreground);">Talabalar Bazasi</h3>
                    <p class="text-gray-500">
                        Talabalarni qo'shish, tahrirlash va to'liq ma'lumotlarni saqlash.
                    </p>
                </div>

                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-4">
                        <i data-lucide="file-spreadsheet" class="w-7 h-7 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--foreground);">Excel Export</h3>
                    <p class="text-gray-500">
                        Professional formatda Excel fayllarni yuklab olish. Ranglar va statistika bilan.
                    </p>
                </div>

                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-4">
                        <i data-lucide="bar-chart-3" class="w-7 h-7 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--foreground);">Statistika</h3>
                    <p class="text-gray-500">
                        Kunlik, haftalik, oylik va yillik statistikalar. Grafiklar va trendlar.
                    </p>
                </div>

                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-7 h-7 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--foreground);">Xavfsizlik</h3>
                    <p class="text-gray-500">
                        Admin va xodim rollari bilan xavfsiz kirish tizimi. Ma'lumotlar himoyasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 border-t" style="border-color: var(--border); background: var(--card);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-white"></i>
                    </div>
                    <span class="font-semibold" style="color: var(--foreground);">Kollej Davomat Tizimi</span>
                </div>
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Barcha huquqlar himoyalangan
                </p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
