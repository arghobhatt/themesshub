<?php
$categories = $categories ?? [];
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-4xl px-6 py-10">
        <a class="mb-6 inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/dashboard">Back to dashboard</a>
        <?php if ($emptyState): ?>
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
            </section>
        <?php else: ?>
            <form method="post" action="/feedback/store" class="space-y-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-rose-300/80">Feedback</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white">Tell us how the mess is doing</h1>
                </div>

                <label class="block">
                    <span class="mb-2 block text-sm text-slate-300">Category</span>
                    <select name="category" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $category)), ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <div>
                    <span class="mb-3 block text-sm text-slate-300">Rating</span>
                    <div class="grid gap-3 sm:grid-cols-5">
                        <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                            <label class="cursor-pointer rounded-3xl border border-white/10 bg-slate-900/60 p-4 text-center hover:border-rose-400/50">
                                <input type="radio" name="rating" value="<?php echo $rating; ?>" class="sr-only" <?php echo $rating === 5 ? 'checked' : ''; ?>>
                                <div class="text-2xl font-semibold text-white"><?php echo $rating; ?></div>
                                <div class="mt-1 text-xs uppercase tracking-[0.3em] text-slate-400">Stars</div>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <label class="block">
                    <span class="mb-2 block text-sm text-slate-300">Comment</span>
                    <textarea name="comment" rows="6" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white" placeholder="Share what is working and what needs attention."></textarea>
                </label>

                <button type="submit" class="rounded-full bg-rose-300 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-rose-200">Submit feedback</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
