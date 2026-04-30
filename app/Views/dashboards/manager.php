<?php
$user = $user ?? null;
$summary = $summary ?? ['total_expense' => 0, 'total_meals' => 0, 'meal_rate' => 0];
$pendingRequests = $pendingRequests ?? [];
$pendingCount = $pendingCount ?? 0;
$messSummaries = $messSummaries ?? [];
$startDate = $startDate ?? date('Y-m-d', strtotime('-6 days'));
$endDate = $endDate ?? date('Y-m-d');

$chartLabels = [];
$expenseData = [];
$mealData = [];
foreach ($messSummaries as $summaryRow) {
    $chartLabels[] = $summaryRow['mess']['name'] ?? 'Mess';
    $expenseData[] = round((float) ($summaryRow['total_expense'] ?? 0), 2);
    $mealData[] = round((float) ($summaryRow['total_meals'] ?? 0), 2);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manager Dashboard | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,197,94,0.22),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(59,130,246,0.20),_transparent_28%),linear-gradient(180deg,_#020617_0%,_#0f172a_55%,_#111827_100%)] font-['Space_Grotesk']">
        <nav class="sticky top-0 z-20 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-emerald-300/80">The Mess Hub</p>
                    <a class="text-lg font-semibold text-white" href="/dashboard">Manager command center</a>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/attendance">Attendance</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/menu/edit">Weekly menu</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/notice">Notices</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/feedback/report">Feedback</a>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/profile">Profile</a>
                    <form method="post" action="/logout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="rounded-full bg-white px-4 py-2 font-semibold text-slate-950 hover:bg-slate-200" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl space-y-8 px-6 py-8 lg:py-10">
            <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-emerald-950/20 backdrop-blur-xl">
                <div class="grid gap-8 lg:grid-cols-[1.3fr_0.9fr] lg:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.45em] text-emerald-300/80">Manager dashboard</p>
                        <h1 class="mt-3 max-w-3xl text-4xl font-semibold tracking-tight text-white md:text-5xl">
                            Welcome<?php echo $user ? ', ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') : ''; ?>.
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300 md:text-base">
                            Track finance, attendance, notices, feedback, and meal operations from one command surface.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a class="rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300" href="/expenses/create">Add expense</a>
                            <a class="rounded-full bg-sky-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-sky-300" href="/deposits/create">Record deposit</a>
                            <a class="rounded-full bg-violet-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-violet-300" href="/messes/create">Create mess</a>
                            <a class="rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300" href="/notice/create">Create notice</a>
                            <a class="rounded-full border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10" href="/attendance/create">Mark attendance</a>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total expenses</p>
                            <p class="mt-3 text-3xl font-semibold text-white">৳<?php echo number_format((float) $summary['total_expense'], 2); ?></p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Meal rate</p>
                            <p class="mt-3 text-3xl font-semibold text-white">৳<?php echo number_format((float) $summary['meal_rate'], 2); ?></p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pending requests</p>
                            <p class="mt-3 text-3xl font-semibold text-white"><?php echo (int) $pendingCount; ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total meals</p>
                    <p class="mt-3 text-3xl font-semibold text-white"><?php echo number_format((float) $summary['total_meals'], 2); ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Approval queue</p>
                    <p class="mt-3 text-3xl font-semibold text-white"><?php echo (int) $pendingCount; ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Average rate</p>
                    <p class="mt-3 text-3xl font-semibold text-white">৳<?php echo number_format((float) $summary['meal_rate'], 2); ?></p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Reporting window</p>
                    <p class="mt-3 text-xl font-semibold text-white"><?php echo htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-sm text-slate-400">to <?php echo htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.3fr_0.9fr]">
                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-emerald-300/80">Financial performance</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white">Mess comparison</h2>
                        </div>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">Chart.js</span>
                    </div>
                    <div class="mt-6 h-[320px]">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-sky-300/80">Join requests</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white">Pending approvals</h2>
                        </div>
                        <a class="text-sm font-semibold text-sky-300 hover:text-sky-200" href="/join-requests">Review all</a>
                    </div>

                    <?php if (empty($pendingRequests)): ?>
                        <p class="mt-6 text-sm text-slate-300">No pending requests right now.</p>
                    <?php else: ?>
                        <div class="mt-5 space-y-4">
                            <?php foreach ($pendingRequests as $request): ?>
                                <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($request['mess_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="mt-1 text-sm text-slate-300"><?php echo htmlspecialchars($request['seeker_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                        <span class="rounded-full bg-amber-400/15 px-3 py-1 text-xs font-semibold text-amber-200">Pending</span>
                                    </div>
                                    <p class="mt-3 text-xs uppercase tracking-[0.3em] text-slate-500"><?php echo htmlspecialchars($request['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1fr_1fr]">
                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-emerald-300/80">Attendance grid</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white">Weekly member coverage</h2>
                        </div>
                        <a class="text-sm font-semibold text-emerald-300 hover:text-emerald-200" href="/attendance">Open attendance</a>
                    </div>

                    <div class="mt-6 space-y-5">
                        <?php foreach ($messSummaries as $summaryRow): ?>
                            <?php
                            $attendanceRows = $summaryRow['attendance'] ?? [];
                            $attendanceAverage = 0.0;
                            $attendanceCount = count($attendanceRows);
                            foreach ($attendanceRows as $row) {
                                $attendanceAverage += (float) ($row['attendance_percentage'] ?? 0);
                            }
                            if ($attendanceCount > 0) {
                                $attendanceAverage /= $attendanceCount;
                            }
                            ?>
                            <article class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-base font-semibold text-white"><?php echo htmlspecialchars($summaryRow['mess']['name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="text-sm text-slate-400"><?php echo htmlspecialchars($summaryRow['mess']['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-slate-400">Avg attendance</p>
                                        <p class="text-xl font-semibold text-white"><?php echo number_format($attendanceAverage, 1); ?>%</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    <?php foreach (array_slice($attendanceRows, 0, 6) as $attendanceRow): ?>
                                        <?php $attendancePercentage = (float) ($attendanceRow['attendance_percentage'] ?? 0); ?>
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <span class="font-medium text-slate-100"><?php echo htmlspecialchars($attendanceRow['full_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                <span class="text-emerald-300"><?php echo number_format($attendancePercentage, 0); ?>%</span>
                                            </div>
                                            <div class="mt-3 h-2 rounded-full bg-white/10">
                                                <div class="h-2 rounded-full bg-gradient-to-r from-emerald-400 to-sky-400" style="width: <?php echo max(0, min(100, $attendancePercentage)); ?>%;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-amber-300/80">Notice feed</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white">Latest broadcasts</h2>
                            </div>
                            <a class="text-sm font-semibold text-amber-300 hover:text-amber-200" href="/notice">Open notices</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            <?php foreach ($messSummaries as $summaryRow): ?>
                                <?php foreach (array_slice($summaryRow['latest_notices'] ?? [], 0, 1) as $notice): ?>
                                    <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($notice['title'] ?? 'Notice', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo (($notice['priority'] ?? 'normal') === 'high') ? 'bg-rose-400/15 text-rose-200' : 'bg-emerald-400/15 text-emerald-200'; ?>">
                                                <?php echo htmlspecialchars($notice['priority'] ?? 'normal', ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-300"><?php echo htmlspecialchars($notice['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="mt-3 text-xs uppercase tracking-[0.3em] text-slate-500"><?php echo htmlspecialchars($summaryRow['mess']['name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></p>
                                    </article>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <?php if (empty($messSummaries)): ?>
                                <p class="text-sm text-slate-300">No notices available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-violet-300/80">Performance snapshot</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white">Meal rate by mess</h2>
                            </div>
                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">Live summary</span>
                        </div>

                        <div class="mt-5 space-y-4">
                            <?php foreach ($messSummaries as $summaryRow): ?>
                                <?php $feedbackAverage = (float) ($summaryRow['feedback_average'] ?? 0); ?>
                                <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-white"><?php echo htmlspecialchars($summaryRow['mess']['name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-sm text-slate-400">৳<?php echo number_format((float) ($summaryRow['rate_per_meal'] ?? 0), 2); ?> per meal</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-slate-400">Satisfaction</p>
                                            <p class="text-lg font-semibold text-white"><?php echo number_format($feedbackAverage, 1); ?>/5</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 h-2 rounded-full bg-white/10">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-violet-400 to-fuchsia-400" style="width: <?php echo max(10, min(100, ((float) ($summaryRow['rate_per_meal'] ?? 0) / max(1.0, (float) $summary['meal_rate'])) * 100)); ?>%;"></div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-sky-300/80">Workflow queue</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Join requests table</h2>
                    </div>
                    <a class="text-sm font-semibold text-sky-300 hover:text-sky-200" href="/join-requests">Open queue</a>
                </div>

                <?php if (empty($pendingRequests)): ?>
                    <p class="mt-6 text-sm text-slate-300">No pending requests right now.</p>
                <?php else: ?>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="border-b border-white/10 text-left text-xs uppercase tracking-[0.35em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">Mess</th>
                                    <th class="px-4 py-3">Seeker</th>
                                    <th class="px-4 py-3">Requested</th>
                                    <th class="px-4 py-3">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingRequests as $request): ?>
                                    <tr class="border-b border-white/5 last:border-0">
                                        <td class="px-4 py-4 text-slate-100"><?php echo htmlspecialchars($request['mess_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-4 text-slate-300"><?php echo htmlspecialchars($request['seeker_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-4 text-slate-300"><?php echo htmlspecialchars($request['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-4 text-slate-300"><?php echo htmlspecialchars($request['message'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        const financeChart = document.getElementById('financeChart');
        if (financeChart) {
            new Chart(financeChart, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartLabels, JSON_UNESCAPED_UNICODE); ?>,
                    datasets: [{
                        label: 'Expenses',
                        data: <?php echo json_encode($expenseData, JSON_UNESCAPED_UNICODE); ?>,
                        backgroundColor: 'rgba(52, 211, 153, 0.75)',
                        borderColor: 'rgba(52, 211, 153, 1)',
                        borderWidth: 1,
                        borderRadius: 12,
                    }, {
                        label: 'Meals',
                        data: <?php echo json_encode($mealData, JSON_UNESCAPED_UNICODE); ?>,
                        backgroundColor: 'rgba(56, 189, 248, 0.75)',
                        borderColor: 'rgba(56, 189, 248, 1)',
                        borderWidth: 1,
                        borderRadius: 12,
                        yAxisID: 'y1',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#e2e8f0' } },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: { ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(148,163,184,0.15)' } },
                        y: { 
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: { color: '#34d399' }, 
                            grid: { color: 'rgba(52,211,153,0.1)' },
                            title: { display: true, text: 'Expenses (৳)', color: '#34d399' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            ticks: { color: '#38bdf8' },
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'Meals', color: '#38bdf8' }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
