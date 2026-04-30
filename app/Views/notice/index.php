<?php
$notices = $notices ?? [];
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notices | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-amber-300/80">Notices</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">Broadcast feed</h1>
            </div>
            <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/dashboard">Dashboard</a>
        </div>

        <?php if ($emptyState): ?>
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
            </section>
        <?php else: ?>
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($notices as $notice): ?>
                    <article class="rounded-[2rem] border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($notice['title'] ?? 'Notice', ENT_QUOTES, 'UTF-8'); ?></h2>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo (($notice['priority'] ?? 'normal') === 'high') ? 'bg-rose-400/15 text-rose-200' : 'bg-emerald-400/15 text-emerald-200'; ?>"><?php echo htmlspecialchars($notice['priority'] ?? 'normal', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <p class="mt-3 text-sm text-slate-300 line-clamp-4"><?php echo htmlspecialchars($notice['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="mt-4 flex items-center justify-between gap-3 text-sm text-slate-400">
                            <span><?php echo htmlspecialchars($notice['created_by_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?php echo htmlspecialchars($notice['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <a class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-slate-200" href="/notice/view?id=<?php echo (int) ($notice['id'] ?? 0); ?>">Open</a>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
