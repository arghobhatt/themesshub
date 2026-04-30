<?php
$feedback = $feedback ?? [];
$statistics = $statistics ?? [];
$categoryAverage = $categoryAverage ?? [];
$overallAverage = (float) ($overallAverage ?? 0);
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Report | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-rose-300/80">Feedback</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">Feedback report</h1>
            </div>
            <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/dashboard">Dashboard</a>
        </div>

        <?php if ($emptyState): ?>
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
            </section>
        <?php else: ?>
            <section class="grid gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Overall average</p>
                    <p class="mt-3 text-4xl font-semibold text-white"><?php echo number_format($overallAverage, 1); ?>/5</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Entries</p>
                    <p class="mt-3 text-4xl font-semibold text-white"><?php echo count($feedback); ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Categories</p>
                    <p class="mt-3 text-4xl font-semibold text-white"><?php echo count($categoryAverage); ?></p>
                </div>
            </section>

            <section class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <h2 class="text-2xl font-semibold text-white">Average by category</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($categoryAverage as $row): ?>
                        <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $row['category'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="mt-3 text-3xl font-semibold text-white"><?php echo number_format((float) ($row['average_rating'] ?? 0), 1); ?>/5</p>
                            <p class="mt-2 text-sm text-slate-400"><?php echo (int) ($row['feedback_count'] ?? 0); ?> feedback items</p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <h2 class="text-2xl font-semibold text-white">Recent feedback</h2>
                <div class="mt-5 space-y-4">
                    <?php foreach ($feedback as $row): ?>
                        <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-white"><?php echo htmlspecialchars($row['full_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-sm text-slate-400"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $row['category'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <span class="rounded-full bg-rose-400/15 px-3 py-1 text-xs font-semibold text-rose-200"><?php echo (int) ($row['rating'] ?? 0); ?>/5</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-300"><?php echo htmlspecialchars($row['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
