<?php
$records = $records ?? [];
$stats = $stats ?? [];
$startDate = $startDate ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $endDate ?? date('Y-m-d');
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Report | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,197,94,0.16),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#111827_100%)]">
        <main class="mx-auto max-w-7xl px-6 py-10">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-emerald-300/80">Attendance</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white">Attendance report</h1>
                    <p class="mt-2 text-sm text-slate-400"><?php echo htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8'); ?> to <?php echo htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/attendance/create">Mark attendance</a>
            </div>

            <?php if ($emptyState): ?>
                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
                </section>
            <?php else: ?>
                <section class="grid gap-4 md:grid-cols-3">
                    <?php foreach (['present' => 'Present', 'absent' => 'Absent', 'attendance_percentage' => 'Coverage'] as $key => $label): ?>
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400"><?php echo $label; ?></p>
                            <p class="mt-3 text-3xl font-semibold text-white">
                                <?php if ($key === 'attendance_percentage'): ?>
                                    <?php echo number_format((float) ($stats[0]['attendance_percentage'] ?? 0), 1); ?>%
                                <?php elseif ($key === 'present'): ?>
                                    <?php echo (int) array_sum(array_map(static fn ($row) => (int) ($row['days_present'] ?? 0), $stats)); ?>
                                <?php else: ?>
                                    <?php echo (int) array_sum(array_map(static fn ($row) => (int) ($row['days_absent'] ?? 0), $stats)); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <h2 class="text-2xl font-semibold text-white">Member attendance table</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="border-b border-white/10 text-left text-xs uppercase tracking-[0.35em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">Member</th>
                                    <th class="px-4 py-3">Present</th>
                                    <th class="px-4 py-3">Absent</th>
                                    <th class="px-4 py-3">Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats as $row): ?>
                                    <tr class="border-b border-white/5 last:border-0">
                                        <td class="px-4 py-4 text-white"><?php echo htmlspecialchars($row['full_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-4 text-slate-300"><?php echo (int) ($row['days_present'] ?? 0); ?></td>
                                        <td class="px-4 py-4 text-slate-300"><?php echo (int) ($row['days_absent'] ?? 0); ?></td>
                                        <td class="px-4 py-4 text-slate-300"><?php echo number_format((float) ($row['attendance_percentage'] ?? 0), 1); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <h2 class="text-2xl font-semibold text-white">Raw attendance records</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($records as $record): ?>
                            <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-white"><?php echo htmlspecialchars($record['full_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo ((int) ($record['is_present'] ?? 0) === 1) ? 'bg-emerald-400/15 text-emerald-200' : 'bg-rose-400/15 text-rose-200'; ?>"><?php echo ((int) ($record['is_present'] ?? 0) === 1) ? 'Present' : 'Absent'; ?></span>
                                </div>
                                <p class="mt-2 text-sm text-slate-400"><?php echo htmlspecialchars($record['attendance_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="mt-2 text-sm text-slate-300"><?php echo htmlspecialchars($record['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
