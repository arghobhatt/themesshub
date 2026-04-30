<?php
$messes = $messes ?? [];
$selectedMessId = $selectedMessId ?? null;
$old = $old ?? [];
$errors = $errors ?? [];
$recent = $recent ?? [];
$flash = $flash ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Meals | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen font-['Space_Grotesk']">
        <nav class="border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                <a class="text-lg font-semibold" href="/">The Mess Hub</a>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <a class="text-slate-600 hover:text-slate-900" href="/dashboard">Dashboard</a>
                    <a class="rounded-full border border-slate-200 px-4 py-2 text-slate-700 hover:border-slate-300" href="/dashboard">Back</a>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-2xl px-6 py-10">
            <header class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-600">Tracking</p>
                <h1 class="mt-2 text-3xl font-semibold">Log Daily Meals</h1>
            </header>

            <?php if (!empty($flash['success'])): ?>
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($flash['error'])): ?>
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (empty($messes)): ?>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-slate-600">You are not an active member of any mess yet.</p>
                </div>
            <?php else: ?>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <form class="space-y-6" method="post" action="/meals">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="mess_id">Mess</label>
                            <select class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200" id="mess_id" name="mess_id" required>
                                <?php foreach ($messes as $mess): ?>
                                    <option value="<?php echo htmlspecialchars((string) $mess['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((int) $mess['id'] === (int) $selectedMessId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mess['name'] . ' - ' . $mess['location'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['mess_id'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['mess_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="meal_date">Meal date</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200" id="meal_date" name="meal_date" type="date" value="<?php echo htmlspecialchars($old['meal_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            <?php if (!empty($errors['meal_date'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['meal_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="meals_count">Meals</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200" id="meals_count" name="meals_count" type="number" step="0.5" min="0.5" value="<?php echo htmlspecialchars((string) ($old['meals_count'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                            <?php if (!empty($errors['meals_count'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['meals_count'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>

                        <button class="w-full rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500" type="submit">Save entry</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="mt-8">
                <h2 class="text-lg font-semibold">Recent meal entries</h2>
                <?php if (empty($recent)): ?>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm text-slate-500">No meal entries logged yet.</p>
                    </div>
                <?php else: ?>
                    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">Mess</th>
                                    <th class="px-4 py-3 text-left">Location</th>
                                    <th class="px-4 py-3 text-left">Date</th>
                                    <th class="px-4 py-3 text-left">Meals</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($recent as $row): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['mess_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['mess_location'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['meal_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars((string) $row['meals_count'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
