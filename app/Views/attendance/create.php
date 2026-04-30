<?php
$members = $members ?? [];
$messId = $messId ?? null;
$date = $date ?? date('Y-m-d');
$emptyState = $emptyState ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mark Attendance | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-['Space_Grotesk']">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,197,94,0.18),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#111827_100%)]">
        <main class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-emerald-300/80">Attendance</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white">Mark daily attendance</h1>
                </div>
                <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200 hover:bg-white/10" href="/attendance">Back to report</a>
            </div>

            <?php if ($emptyState): ?>
                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <p class="text-lg font-semibold text-white"><?php echo htmlspecialchars($emptyState, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="mt-2 text-sm text-slate-300">Once a mess exists, you can mark members present and absent here.</p>
                </section>
            <?php else: ?>
                <form method="post" action="/attendance/batch-mark" class="space-y-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm text-slate-300">Date</span>
                            <input type="date" name="date" value="<?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white outline-none ring-0 focus:border-emerald-400">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-sm text-slate-300">Notes</span>
                            <input type="text" name="notes" placeholder="Optional note" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white outline-none ring-0 focus:border-emerald-400">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($members as $member): ?>
                            <label class="flex cursor-pointer items-center justify-between rounded-3xl border border-white/10 bg-slate-900/60 p-4 hover:border-emerald-400/50">
                                <div>
                                    <p class="font-semibold text-white"><?php echo htmlspecialchars($member['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-sm text-slate-400"><?php echo htmlspecialchars($member['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <input type="checkbox" name="present_ids[]" value="<?php echo (int) $member['id']; ?>" class="h-5 w-5 rounded border-white/20 bg-slate-900 text-emerald-400 focus:ring-emerald-400">
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-slate-400">Check the members present, then submit to auto-mark the rest absent for the date.</p>
                        <button type="submit" class="rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-emerald-300">Save attendance</button>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
