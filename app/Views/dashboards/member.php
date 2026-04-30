<?php
$user = $user ?? null;
$summary = $summary ?? ['total_meals' => 0, 'total_deposits' => 0, 'balance' => 0];
$messSummaries = $messSummaries ?? [];
$depositHistory = $depositHistory ?? [];
$balanceValue = (float) ($summary['balance'] ?? 0);
$balanceLabel = $balanceValue >= 0 ? 'Due' : 'Credit';
$combinedAttendance = 0.0;
$attendanceCount = 0;
foreach ($messSummaries as $summaryRow) {
    $attendance = (float) (($summaryRow['attendance']['attendance_percentage'] ?? 0));
    if ($attendance > 0) {
        $combinedAttendance += $attendance;
        $attendanceCount++;
    }
}
$averageAttendance = $attendanceCount > 0 ? ($combinedAttendance / $attendanceCount) : 0.0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Dashboard | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.20),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(244,114,182,0.15),_transparent_24%),linear-gradient(180deg,_#020617_0%,_#0f172a_55%,_#111827_100%)] font-['Space_Grotesk']">
        <nav class="sticky top-0 z-20 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-sky-300/80">The Mess Hub</p>
                    <a class="text-lg font-semibold text-white" href="/dashboard">Member dashboard</a>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/menu">Menu</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/notice">Notices</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/feedback/create">Feedback</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/profile">Profile</a>
                    <form method="post" action="/logout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="rounded-full bg-white px-4 py-2 font-semibold text-slate-950 hover:bg-slate-200" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl space-y-8 px-6 py-8 lg:py-10">
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-sky-950/20 backdrop-blur-xl">
                <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.45em] text-sky-300/80">Member dashboard</p>
                        <h1 class="mt-3 text-4xl font-semibold tracking-tight text-white md:text-5xl">Welcome<?php echo $user ? ', ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') : ''; ?>.</h1>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300 md:text-base">Balance, attendance, menu, notices, and deposit history at a glance.</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a class="rounded-full bg-sky-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-sky-300" href="/meals/create">Log meals</a>
                            <a class="rounded-full border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10" href="/feedback/create">Send feedback</a>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Balance</p>
                            <p class="mt-3 text-3xl font-semibold text-white"><?php echo htmlspecialchars($balanceLabel . ': ' . number_format(abs($balanceValue), 2), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Attendance</p>
                            <p class="mt-3 text-3xl font-semibold text-white"><?php echo number_format($averageAttendance, 1); ?>%</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Meals logged</p>
                    <p class="mt-3 text-3xl font-semibold text-white"><?php echo number_format((float) $summary['total_meals'], 2); ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total deposits</p>
                    <p class="mt-3 text-3xl font-semibold text-white"><?php echo number_format((float) $summary['total_deposits'], 2); ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Balance label</p>
                    <p class="mt-3 text-3xl font-semibold text-white"><?php echo htmlspecialchars($balanceLabel, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Mess count</p>
                    <p class="mt-3 text-3xl font-semibold text-white"><?php echo count($messSummaries); ?></p>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-sky-300/80">Meal summary</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white">By mess</h2>
                            </div>
                            <a class="text-sm font-semibold text-sky-300 hover:text-sky-200" href="/menu">See menu</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            <?php foreach ($messSummaries as $summaryRow): ?>
                                <?php
                                $rowBalance = (float) ($summaryRow['balance'] ?? 0);
                                $rowAttendance = (float) (($summaryRow['attendance']['attendance_percentage'] ?? 0));
                                ?>
                                <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-base font-semibold text-white"><?php echo htmlspecialchars($summaryRow['mess']['name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-400"><?php echo htmlspecialchars($summaryRow['mess']['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                        <div class="text-right text-sm text-slate-300">
                                            <p><?php echo htmlspecialchars($rowBalance >= 0 ? 'Due' : 'Credit', ENT_QUOTES, 'UTF-8'); ?>: ৳<?php echo number_format(abs($rowBalance), 2); ?></p>
                                            <p>Attendance: <?php echo number_format($rowAttendance, 1); ?>%</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Meals</p>
                                            <p class="mt-2 text-xl font-semibold text-white"><?php echo number_format((float) ($summaryRow['user_meals'] ?? 0), 2); ?></p>
                                        </div>
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Rate</p>
                                            <p class="mt-2 text-xl font-semibold text-white">৳<?php echo number_format((float) ($summaryRow['rate_per_meal'] ?? 0), 2); ?></p>
                                        </div>
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Feedback</p>
                                            <p class="mt-2 text-xl font-semibold text-white"><?php echo number_format((float) ($summaryRow['feedback_average'] ?? 0), 1); ?>/5</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 h-2 rounded-full bg-white/10">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-sky-400 to-fuchsia-400" style="width: <?php echo max(5, min(100, $rowAttendance)); ?>%;"></div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                            <?php if (empty($messSummaries)): ?>
                                <p class="text-sm text-slate-300">No meal data yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-amber-300/80">Deposit history</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white">Recent contributions</h2>
                            </div>
                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">Latest 15</span>
                        </div>

                        <?php if (empty($depositHistory)): ?>
                            <p class="mt-6 text-sm text-slate-300">No deposits recorded yet.</p>
                        <?php else: ?>
                            <div class="mt-5 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="border-b border-white/10 text-left text-xs uppercase tracking-[0.35em] text-slate-400">
                                        <tr>
                                            <th class="px-4 py-3">Mess</th>
                                            <th class="px-4 py-3">Date</th>
                                            <th class="px-4 py-3">Amount</th>
                                            <th class="px-4 py-3">Method</th>
                                            <th class="px-4 py-3">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($depositHistory as $row): ?>
                                            <tr class="border-b border-white/5 last:border-0">
                                                <td class="px-4 py-4 text-slate-100"><?php echo htmlspecialchars($row['mess_name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="px-4 py-4 text-slate-300"><?php echo htmlspecialchars($row['deposited_on'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="px-4 py-4 text-slate-300">৳<?php echo number_format((float) ($row['amount'] ?? 0), 2); ?></td>
                                                <td class="px-4 py-4 text-slate-300"><?php echo htmlspecialchars($row['method'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="px-4 py-4 text-slate-300"><?php echo htmlspecialchars($row['reference'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-emerald-300/80">Today and tomorrow</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white">Weekly menu</h2>
                            </div>
                            <a class="text-sm font-semibold text-emerald-300 hover:text-emerald-200" href="/menu">Open full menu</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            <?php foreach ($messSummaries as $summaryRow): ?>
                                <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-white"><?php echo htmlspecialchars($summaryRow['mess']['name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-400"><?php echo htmlspecialchars($summaryRow['mess']['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                        <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200"><?php echo number_format((float) ($summaryRow['feedback_average'] ?? 0), 1); ?>/5</span>
                                    </div>
                                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Today</p>
                                            <p class="mt-2 text-sm text-slate-100"><?php echo htmlspecialchars(($summaryRow['today_menu']['breakfast'] ?? 'Not set'), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-300"><?php echo htmlspecialchars(($summaryRow['today_menu']['lunch'] ?? 'Not set'), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-300"><?php echo htmlspecialchars(($summaryRow['today_menu']['dinner'] ?? 'Not set'), ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Tomorrow</p>
                                            <p class="mt-2 text-sm text-slate-100"><?php echo htmlspecialchars(($summaryRow['tomorrow_menu']['breakfast'] ?? 'Not set'), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-300"><?php echo htmlspecialchars(($summaryRow['tomorrow_menu']['lunch'] ?? 'Not set'), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-300"><?php echo htmlspecialchars(($summaryRow['tomorrow_menu']['dinner'] ?? 'Not set'), ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-rose-300/80">Notice feed</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white">Latest broadcasts</h2>
                            </div>
                            <a class="text-sm font-semibold text-rose-300 hover:text-rose-200" href="/notice">Open notices</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            <?php foreach ($messSummaries as $summaryRow): ?>
                                <?php foreach (array_slice($summaryRow['latest_notices'] ?? [], 0, 1) as $notice): ?>
                                    <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="font-semibold text-white"><?php echo htmlspecialchars($notice['title'] ?? 'Notice', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo (($notice['priority'] ?? 'normal') === 'high') ? 'bg-rose-400/15 text-rose-200' : 'bg-emerald-400/15 text-emerald-200'; ?>"><?php echo htmlspecialchars($notice['priority'] ?? 'normal', ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-300"><?php echo htmlspecialchars($notice['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    </article>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <?php if (empty($messSummaries)): ?>
                                <p class="text-sm text-slate-300">No notices yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
