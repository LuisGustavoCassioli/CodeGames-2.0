<?php
// views/layouts/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cart_session'])) {
    $_SESSION['cart_session'] = bin2hex(random_bytes(16));
}
$isLoggedIn = isset($_SESSION['user_id']);
$cartCount = 0;
try {
    $cartModel = new \App\Models\CartModel();
    $cartCount = $cartModel->getItemCount($_SESSION['cart_session'], $_SESSION['user_id'] ?? null);
} catch (\Exception $e) {
    // Fallback if db error
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeGames | Premium Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        surface: {
                            50: '#f8fafc',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; }
        .glass-panel {
            background: rgba(10, 10, 10, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Hide scrollbar for category menu */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Prevent native browser autocomplete/clear buttons from interfering */
        input[type="text"]::-ms-clear,
        input[type="text"]::-ms-reveal {
            display: none;
            width: 0;
            height: 0;
        }
        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            display: none;
        }
    </style>
</head>
<body class="text-slate-50 min-h-screen flex flex-col antialiased">

<header class="sticky top-0 z-50 glass-panel">
    <!-- Top Bar -->
    <div class="container mx-auto px-4 max-w-7xl h-20 flex items-center justify-between gap-6">
        
        <!-- Logo -->
        <a href="/" class="flex-shrink-0 flex items-center gap-2 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center shadow-lg shadow-brand-500/20 group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-white">Code<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-purple-500">Games</span></span>
        </a>

        <!-- Search Bar -->
        <div class="flex-1 max-w-2xl hidden md:block">
            <div class="relative group" x-data="{ query: '<?= htmlspecialchars($_GET['q'] ?? '') ?>', results: [], open: false, loading: false }" @click.away="open = false">
                <form action="/" method="GET" class="relative group" @submit="if(!query.trim()) $event.preventDefault()">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" x-model="query" autocomplete="off" spellcheck="false" role="presentation"
                        @input.debounce.300ms="
                            if(query.length >= 2) {
                                loading = true;
                                fetch('/api/search?q=' + encodeURIComponent(query))
                                    .then(res => res.json())
                                    .then(data => { results = data; open = true; loading = false; })
                            } else {
                                open = false;
                            }
                        "
                        @focus="if(query.length >= 2) open = true"
                        class="w-full bg-white/5 border border-white/10 rounded-full py-2.5 pl-11 pr-4 text-sm text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 focus:bg-white/10 transition-all" placeholder="Busque por jogos, plataformas ou categorias...">
                </form>

                <!-- Search Dropdown -->
                <div x-show="open && results.length > 0" style="display: none;" class="absolute top-full left-0 right-0 mt-2 bg-[#121212] border border-white/10 rounded-xl shadow-2xl py-2 z-50 max-h-[400px] overflow-y-auto hide-scrollbar">
                    <template x-for="game in results" :key="game.id">
                        <a :href="'/product?slug=' + game.slug" class="flex items-center gap-4 px-4 py-3 hover:bg-white/5 transition-colors">
                            <div class="w-10 h-14 bg-gray-800 rounded flex-shrink-0 overflow-hidden border border-white/5">
                                <template x-if="game.image_url">
                                    <img :src="game.image_url" class="w-full h-full object-cover">
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-white truncate" x-text="game.title"></h4>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mt-0.5" x-text="game.platform"></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-white whitespace-nowrap">R$ <span x-text="Number(game.price).toFixed(2).replace('.', ',')"></span></div>
                                <template x-if="game.original_price && Number(game.original_price) > Number(game.price)">
                                    <div class="text-xs text-gray-500 line-through">R$ <span x-text="Number(game.original_price).toFixed(2).replace('.', ',')"></span></div>
                                </template>
                            </div>
                        </a>
                    </template>
                    <a :href="'/?q=' + encodeURIComponent(query)" class="block text-center text-xs text-brand-400 hover:text-brand-300 font-medium py-3 border-t border-white/5 bg-white/[0.02]">Ver todos os resultados</a>
                </div>
                
                <div x-show="open && results.length === 0 && !loading" style="display: none;" class="absolute top-full left-0 right-0 mt-2 bg-[#121212] border border-white/10 rounded-xl shadow-2xl py-6 z-50 text-center">
                    <p class="text-sm text-gray-400">Nenhum jogo encontrado para &quot;<span x-text="query" class="text-white"></span>&quot;</p>
                </div>
            </div>
        </div>

        <!-- User Actions -->
        <div class="flex items-center gap-3">
            <?php if ($isLoggedIn): ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN'): ?>
                    <!-- Admin Dropdown (Alpine) -->
                    <div class="relative hidden sm:block" x-data="{ openAdmin: false }">
                        <button @click="openAdmin = !openAdmin" @click.away="openAdmin = false" class="flex items-center gap-2 text-xs font-semibold text-purple-400 bg-purple-500/10 border border-purple-500/20 px-3 py-1.5 rounded-full hover:bg-purple-500/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Admin
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="openAdmin" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-[#121212] border border-white/10 rounded-xl shadow-xl py-1 z-50" style="display: none;">
                            <a href="/admin/products" class="block px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors">
                                Produtos
                            </a>
                            <a href="/admin/coupons" class="block px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors">
                                Cupons de Desconto
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Cart Button -->
                <a href="/cart" class="relative p-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-[#0a0a0a]"><?= $cartCount > 9 ? '9+' : $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown (Alpine) -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 p-1.5 pr-3 text-sm text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 rounded-full transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-brand-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-sm">
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <span class="hidden sm:block font-medium truncate max-w-[100px]"><?php echo htmlspecialchars(explode(' ', trim($_SESSION['user_name'] ?? 'User'))[0]); ?></span>
                        <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-[#121212] border border-white/10 rounded-xl shadow-xl py-1" style="display: none;">
                        <a href="/orders" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Meus Pedidos
                        </a>
                        <div class="h-px bg-white/10 my-1"></div>
                        <a href="/logout" class="flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Sair da conta
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="text-sm font-medium text-gray-300 hover:text-white px-3 py-2 transition-colors">Entrar</a>
                <a href="/register" class="text-sm font-medium bg-white text-black hover:bg-gray-200 px-4 py-2 rounded-full transition-colors">Cadastrar</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="border-t border-white/5 bg-[#050505]/50 hidden md:block">
        <div class="container mx-auto px-4 max-w-7xl">
            <nav class="flex items-center gap-6 overflow-x-auto hide-scrollbar py-3 text-sm font-medium text-gray-400">
                <a href="/" class="text-white flex items-center gap-2 flex-shrink-0 hover:text-white transition-colors">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                    Destaques
                </a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors">Lançamentos</a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors">Pré-venda</a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors">PC</a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors">PlayStation</a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors">Xbox</a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors">Nintendo</a>
                <a href="#" class="flex-shrink-0 hover:text-white transition-colors flex items-center gap-1.5 text-orange-400 hover:text-orange-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Promoções
                </a>
            </nav>
        </div>
    </div>
</header>

<main class="flex-grow">

