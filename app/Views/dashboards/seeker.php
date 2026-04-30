<?php
$user = $user ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen font-['Space_Grotesk']">
        <nav class="border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a class="text-lg font-semibold" href="/">The Mess Hub</a>
                <div class="flex items-center gap-3 text-sm">
                    <a class="text-slate-600 hover:text-slate-900" href="/">Home</a>
                    <a class="rounded-full border border-slate-200 px-4 py-2 text-slate-700 hover:border-slate-300" href="/profile">Profile</a>
                    <form method="post" action="/logout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="rounded-full border border-slate-200 px-4 py-2 text-slate-700 hover:border-slate-300" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-5xl px-6 py-12">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Seeker dashboard</p>
                <h1 class="mt-2 text-2xl font-semibold">Welcome<?php echo $user ? ', ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') : ''; ?></h1>
                <p class="mt-3 text-sm text-slate-600">Your dashboard will appear after you join a mess.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500" href="/messes">Browse listings</a>
                    <a class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:border-slate-300" href="/profile">View profile</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
