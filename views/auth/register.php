<!-- views/auth/register.php -->
<div class="flex justify-center items-center min-h-[60vh]">
    <div class="glass-panel p-8 rounded-2xl w-full max-w-md shadow-lg">
        <h1 class="text-4xl font-bold text-center mb-2 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Cadastro</h1>
        <p class="text-center text-slate-400 mb-8">Crie sua conta na CodeGames</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-md mb-6 font-medium text-center">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="/register" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-400 mb-1">Nome Completo</label>
                <input type="text" id="name" name="name" class="w-full bg-black/20 border border-white/10 rounded-md px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/50 transition-all" required autocomplete="name">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-400 mb-1">E-mail</label>
                <input type="email" id="email" name="email" class="w-full bg-black/20 border border-white/10 rounded-md px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/50 transition-all" required autocomplete="email">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-slate-400 mb-1">Senha</label>
                <input type="password" id="password" name="password" class="w-full bg-black/20 border border-white/10 rounded-md px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/50 transition-all" required minlength="6">
            </div>

            <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-medium py-3 rounded-md transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-0.5 mt-6">
                Criar Conta
            </button>
        </form>

        <p class="text-center text-sm text-slate-400 mt-8">
            Já possui conta? <a href="/login" class="text-brand-500 hover:text-white transition-colors">Entrar</a>
        </p>
    </div>
</div>
