<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-emerald-50 text-slate-900">
    <div class="min-h-screen font-['Space_Grotesk']">
        <nav class="border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a class="text-lg font-semibold" href="/">The Mess Hub</a>
                <div class="flex items-center gap-3 text-sm">
                    <a class="text-slate-600 hover:text-slate-900" href="/">Home</a>
                    <a class="rounded-full border border-slate-200 px-4 py-2 text-slate-700 hover:border-slate-300" href="/register">Create account</a>
                </div>
            </div>
        </nav>

        <main class="mx-auto grid max-w-5xl gap-6 px-6 py-10 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-600">Welcome back</p>
                <h1 class="mt-2 text-2xl font-semibold">Sign in to your account</h1>

                <?php if (!empty($errors['general'])): ?>
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <?php echo htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form class="mt-6 space-y-4" method="post" action="/login">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="email">Email</label>
                        <input class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200" id="email" name="email" type="email" autocomplete="email" value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        <?php if (!empty($errors['email'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="password">Password</label>
                        <input class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200" id="password" name="password" type="password" autocomplete="current-password" required>
                        <?php if (!empty($errors['password'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <button class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500" type="submit">Login</button>
                </form>

                <p class="mt-6 text-sm text-slate-600">New here? <a class="font-semibold text-emerald-600 hover:text-emerald-500" href="/register">Create an account</a></p>
            </section>

            <aside class="rounded-2xl bg-slate-900 p-6 text-white shadow-sm">
                <h2 class="text-xl font-semibold">Manage your mess with clarity</h2>
                <p class="mt-3 text-sm text-slate-200">Track meals, deposits, and expenses in one shared space.</p>
                <ul class="mt-6 space-y-3 text-sm text-slate-200">
                    <li>See real-time meal rates.</li>
                    <li>Keep everyone aligned on balances.</li>
                    <li>Move from seeker to member quickly.</li>
                </ul>
            </aside>
        </main>
    </div>
</body>
</html>
