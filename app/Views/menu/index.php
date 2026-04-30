<?php
$menu = $menu ?? [];
$daysOfWeek = $daysOfWeek ?? ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weekly Menu | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.15),_transparent_26%),linear-gradient(180deg,_#020617_0%,_#111827_100%)]">
        <main class="mx-auto max-w-7xl px-6 py-10">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-amber-300/80">Menu</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white">Weekly menu</h1>
                </div>
                <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/dashboard">Dashboard</a>
            </div>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($daysOfWeek as $index => $dayLabel): ?>
                    <?php $row = $menu[$index] ?? null; ?>
                    <article class="rounded-[2rem] border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400"><?php echo htmlspecialchars($dayLabel, ENT_QUOTES, 'UTF-8'); ?></p>
                        <h2 class="mt-2 text-2xl font-semibold text-white"><?php echo $index === ((int) date('w') - 1 < 0 ? 6 : (int) date('w') - 1) ? 'Today' : 'Menu'; ?></h2>
                        <div class="mt-4 space-y-3 text-sm text-slate-300">
                            <p><span class="font-semibold text-white">Breakfast:</span> <?php echo htmlspecialchars($row['breakfast'] ?? 'Not set', ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><span class="font-semibold text-white">Lunch:</span> <?php echo htmlspecialchars($row['lunch'] ?? 'Not set', ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><span class="font-semibold text-white">Dinner:</span> <?php echo htmlspecialchars($row['dinner'] ?? 'Not set', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        </main>
    </div>
</body>
</html>
