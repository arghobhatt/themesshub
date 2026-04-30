<?php
$menu = $menu ?? [];
$daysOfWeek = $daysOfWeek ?? ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Weekly Menu | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-amber-300/80">Menu</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">Edit weekly menu</h1>
            </div>
            <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/menu">View menu</a>
        </div>

        <?php if ($emptyState): ?>
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
            </section>
        <?php else: ?>
            <div class="grid gap-4">
                <?php foreach ($daysOfWeek as $index => $dayLabel): ?>
                    <?php $row = null; foreach ($menu as $item) { if ((int) ($item['day_of_week'] ?? -1) === $index) { $row = $item; break; } } ?>
                    <form method="post" action="/menu/update-day" class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <input type="hidden" name="day_of_week" value="<?php echo (int) $index; ?>">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <h2 class="text-2xl font-semibold text-white"><?php echo htmlspecialchars($dayLabel, ENT_QUOTES, 'UTF-8'); ?></h2>
                            <button type="submit" class="rounded-full bg-amber-300 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-amber-200">Save day</button>
                        </div>
                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                            <label class="block">
                                <span class="mb-2 block text-sm text-slate-300">Breakfast</span>
                                <input type="text" name="breakfast" value="<?php echo htmlspecialchars($row['breakfast'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm text-slate-300">Lunch</span>
                                <input type="text" name="lunch" value="<?php echo htmlspecialchars($row['lunch'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm text-slate-300">Dinner</span>
                                <input type="text" name="dinner" value="<?php echo htmlspecialchars($row['dinner'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                            </label>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
