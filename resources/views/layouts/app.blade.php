<!DOCTYPE html>
<html lang="uz" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kollej Davomat Tizimi')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        border: 'var(--border)',
                        input: 'var(--input)',
                        ring: 'var(--ring)',
                        background: 'var(--background)',
                        foreground: 'var(--foreground)',
                        primary: {
                            DEFAULT: 'var(--primary)',
                            foreground: 'var(--primary-foreground)',
                        },
                        secondary: {
                            DEFAULT: 'var(--secondary)',
                            foreground: 'var(--secondary-foreground)',
                        },
                        accent: {
                            DEFAULT: 'var(--accent)',
                            foreground: 'var(--accent-foreground)',
                        },
                        destructive: {
                            DEFAULT: 'var(--destructive)',
                            foreground: 'var(--destructive-foreground)',
                        },
                        success: {
                            DEFAULT: 'var(--success)',
                            foreground: 'var(--success-foreground)',
                        },
                        warning: {
                            DEFAULT: 'var(--warning)',
                            foreground: 'var(--warning-foreground)',
                        },
                        muted: {
                            DEFAULT: 'var(--muted)',
                            foreground: 'var(--muted-foreground)',
                        },
                        card: {
                            DEFAULT: 'var(--card)',
                            foreground: 'var(--card-foreground)',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Light Theme */
        :root {
            --background: #f8fafc;
            --foreground: #0f172a;
            --card: #ffffff;
            --card-foreground: #1e293b;
            --primary: #0891b2;
            --primary-foreground: #ffffff;
            --secondary: #6366f1;
            --secondary-foreground: #ffffff;
            --accent: #f59e0b;
            --accent-foreground: #0f172a;
            --muted: #f1f5f9;
            --muted-foreground: #64748b;
            --border: #e2e8f0;
            --input: #ffffff;
            --ring: #0891b2;
            --success: #10b981;
            --success-foreground: #ffffff;
            --warning: #f59e0b;
            --warning-foreground: #0f172a;
            --destructive: #ef4444;
            --destructive-foreground: #ffffff;
        }

        /* Dark Theme */
        .dark {
            --background: #0f172a;
            --foreground: #f1f5f9;
            --card: #1e293b;
            --card-foreground: #e2e8f0;
            --primary: #22d3ee;
            --primary-foreground: #0f172a;
            --secondary: #818cf8;
            --secondary-foreground: #0f172a;
            --accent: #fbbf24;
            --accent-foreground: #0f172a;
            --muted: #334155;
            --muted-foreground: #94a3b8;
            --border: #334155;
            --input: #1e293b;
            --ring: #22d3ee;
            --success: #34d399;
            --success-foreground: #0f172a;
            --warning: #fbbf24;
            --warning-foreground: #0f172a;
            --destructive: #f87171;
            --destructive-foreground: #0f172a;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--background);
            color: var(--foreground);
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--muted); border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--secondary); }

        .animate-slide-up {
            animation: slideUp 0.4s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hover-lift { transition: all 200ms ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }

        .progress-bar { background: var(--muted); border-radius: 9999px; overflow: hidden; }
        .progress-fill { border-radius: 9999px; transition: width 500ms ease-out; }

        .stat-card { position: relative; overflow: hidden; }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 150ms ease;
            cursor: pointer;
        }
        .btn-primary { background-color: var(--primary); color: var(--primary-foreground); }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary { background-color: var(--secondary); color: var(--secondary-foreground); }
        .btn-outline { border: 1px solid var(--border); background-color: transparent; color: var(--foreground); }
        .btn-outline:hover { background-color: var(--muted); }
        .btn-ghost { background-color: transparent; color: var(--muted-foreground); }
        .btn-ghost:hover { background-color: var(--muted); color: var(--foreground); }
        .btn-success { background-color: var(--success); color: var(--success-foreground); }
        .btn-destructive { background-color: var(--destructive); color: var(--destructive-foreground); }

        .input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            background-color: var(--input);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            color: var(--foreground);
            font-size: 0.875rem;
            transition: all 150ms ease;
        }
        .input:focus { outline: none; border-color: var(--ring); box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1); }
        .input::placeholder { color: var(--muted-foreground); }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }
        .badge-primary { background-color: color-mix(in srgb, var(--primary) 20%, transparent); color: var(--primary); }
        .badge-success { background-color: color-mix(in srgb, var(--success) 20%, transparent); color: var(--success); }
        .badge-warning { background-color: color-mix(in srgb, var(--warning) 20%, transparent); color: var(--warning); }
        .badge-destructive { background-color: color-mix(in srgb, var(--destructive) 20%, transparent); color: var(--destructive); }
        .badge-secondary { background-color: color-mix(in srgb, var(--secondary) 20%, transparent); color: var(--secondary); }
        .badge-muted { background-color: var(--muted); color: var(--muted-foreground); }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        @auth
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-border transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-border">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-foreground">Davomat</h1>
                        <p class="text-xs text-muted-foreground">Kollej tizimi</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3">
                <ul class="space-y-1">
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            Dashboard
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->canTakeAttendance())
                    <li>
                        <a href="{{ route('davomat.olish') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('davomat.olish') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="check-square" class="w-5 h-5"></i>
                            Davomat Olish
                            <span class="ml-auto w-2 h-2 bg-success rounded-full animate-pulse"></span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <li class="pt-4">
                        <span class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">Boshqaruv</span>
                    </li>
                    <li>
                        <a href="{{ route('guruhlar.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('guruhlar.*') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            Guruhlar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('talabalar.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('talabalar.*') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            Talabalar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('foydalanuvchilar.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('foydalanuvchilar.*') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="user-cog" class="w-5 h-5"></i>
                            Foydalanuvchilar
                        </a>
                    </li>
                    @endif

                    <li class="pt-4">
                        <span class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider">Hisobotlar</span>
                    </li>

                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('davomat.tarixi') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('davomat.tarixi') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="history" class="w-5 h-5"></i>
                            Davomat Tarixi
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->canTakeAttendance())
                    <li>
                        <a href="{{ route('davomat.mening-tarixim') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('davomat.mening-tarixim') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                            Mening Tarixim
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('export.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('export.*') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="download" class="w-5 h-5"></i>
                            Export
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('davomat.hisobot') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('davomat.hisobot*') ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                            Davomat Hisoboti
                            <span class="ml-auto px-1.5 py-0.5 text-xs font-medium bg-secondary/10 text-secondary rounded">Yangi</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>

            <!-- User & Theme -->
            <div class="p-3 border-t border-border space-y-3">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-muted/50 hover:bg-muted transition-colors">
                    <span class="flex items-center gap-2 text-sm text-muted-foreground">
                        <i x-show="darkMode" data-lucide="sun" class="w-4 h-4"></i>
                        <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                        <span x-text="darkMode ? 'Yorqin rejim' : 'Tungi rejim'"></span>
                    </span>
                    <div class="relative w-10 h-5 rounded-full transition-colors" :class="darkMode ? 'bg-primary' : 'bg-muted'">
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="darkMode ? 'translate-x-5' : 'translate-x-0'"></div>
                    </div>
                </button>

                <div class="flex items-center gap-3 p-2 rounded-lg bg-muted/30">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-muted-foreground">{{ auth()->user()->role_name }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors" title="Chiqish">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
        @endauth

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            @auth
            <!-- Header -->
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-4 lg:px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-muted text-muted-foreground">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h2 class="text-lg font-semibold text-foreground hidden sm:block">@yield('page-title', 'Dashboard')</h2>
                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-muted/50 text-sm text-muted-foreground">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        {{ now()->format('d.m.Y') }}
                    </div>
                </div>
            </header>
            @endauth

            <!-- Main -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @if(session('muvaffaqiyat'))
                <div class="mb-4 p-4 bg-success/10 border border-success/20 text-success rounded-lg animate-slide-up flex items-center gap-2"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('muvaffaqiyat') }}
                </div>
                @endif

                @if(session('xato'))
                <div class="mb-4 p-4 bg-destructive/10 border border-destructive/20 text-destructive rounded-lg animate-slide-up flex items-center gap-2"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    {{ session('xato') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
        document.addEventListener('alpine:initialized', function() { lucide.createIcons(); });
    </script>
    @stack('scripts')
</body>
</html>
