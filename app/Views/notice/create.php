<?php
$messId = $messId ?? null;
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Notice | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-4xl px-6 py-10">
        <a class="mb-6 inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/notice">Back to notices</a>
        <?php if ($emptyState): ?>
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
            </section>
        <?php else: ?>
            <form method="post" action="/notice/store" class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl space-y-5">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="mess_id" value="<?php echo (int) $messId; ?>">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-amber-300/80">Create notice</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white">Write a broadcast</h1>
                </div>
                <label class="block">
                    <span class="mb-2 block text-sm text-slate-300">Title</span>
                    <input type="text" name="title" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm text-slate-300">Priority</span>
                    <select name="priority" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm text-slate-300">Content</span>
                    <textarea name="content" rows="7" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white"></textarea>
                </label>
                <button type="submit" class="rounded-full bg-amber-300 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-amber-200">Publish notice</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
