<?php
$notice = $notice ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Notice | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-4xl px-6 py-10">
        <a class="mb-6 inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/notice">Back to notices</a>
        <?php if ($notice): ?>
            <article class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur-xl">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-4xl font-semibold text-white"><?php echo htmlspecialchars($notice['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo (($notice['priority'] ?? 'normal') === 'high') ? 'bg-rose-400/15 text-rose-200' : 'bg-emerald-400/15 text-emerald-200'; ?>"><?php echo htmlspecialchars($notice['priority'] ?? 'normal', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <p class="mt-4 text-sm uppercase tracking-[0.35em] text-slate-400"><?php echo htmlspecialchars($notice['created_by_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($notice['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="prose prose-invert mt-6 max-w-none">
                    <p class="text-lg leading-8 text-slate-200"><?php echo nl2br(htmlspecialchars($notice['content'] ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
            </article>
        <?php else: ?>
            <p class="text-slate-300">Notice not found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
