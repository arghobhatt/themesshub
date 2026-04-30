<?php
$date = $date ?? date('Y-m-d');
$records = $records ?? [];
$presentCount = $presentCount ?? 0;
$absentCount = $absentCount ?? 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance by Date | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <main class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-emerald-300/80">Attendance</p>
                <h1 class="mt-2 text-3xl font-semibold text-white"><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
            <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/attendance">Back to report</a>
        </div>

        <section class="grid gap-4 md:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Present</p>
                <p class="mt-3 text-3xl font-semibold text-white"><?php echo (int) $presentCount; ?></p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Absent</p>
                <p class="mt-3 text-3xl font-semibold text-white"><?php echo (int) $absentCount; ?></p>
            </div>
        </section>

        <section class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="text-2xl font-semibold text-white">Daily records</h2>
            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($records as $record): ?>
                    <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-white"><?php echo htmlspecialchars($record['full_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo ((int) ($record['is_present'] ?? 0) === 1) ? 'bg-emerald-400/15 text-emerald-200' : 'bg-rose-400/15 text-rose-200'; ?>"><?php echo ((int) ($record['is_present'] ?? 0) === 1) ? 'Present' : 'Absent'; ?></span>
                        </div>
                        <p class="mt-3 text-sm text-slate-400"><?php echo htmlspecialchars($record['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mt-2 text-sm text-slate-300"><?php echo htmlspecialchars($record['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
