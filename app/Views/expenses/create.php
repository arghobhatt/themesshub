<?php
$messes = $messes ?? [];
$members = $members ?? [];
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
    <title>Add Expense | The Mess Hub</title>
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
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Finance</p>
                <h1 class="mt-2 text-3xl font-semibold">Add Daily Bazaar Expense</h1>
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
                    <p class="text-slate-600">You do not manage any mess yet.</p>
                </div>
            <?php else: ?>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <form class="mb-8 space-y-4" method="get" action="/expenses/create">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="mess_id">Select mess</label>
                            <select class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="mess_id" name="mess_id">
                                <?php foreach ($messes as $mess): ?>
                                    <option value="<?php echo htmlspecialchars((string) $mess['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((int) $mess['id'] === (int) $selectedMessId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mess['name'] . ' - ' . $mess['location'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500" type="submit">Load members</button>
                    </form>

                    <?php if ($selectedMessId === null): ?>
                        <p class="text-slate-600">Select a mess to continue.</p>
                    <?php else: ?>
                        <form class="space-y-6" method="post" action="/expenses">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="mess_id" value="<?php echo htmlspecialchars((string) $selectedMessId, ENT_QUOTES, 'UTF-8'); ?>">

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="purchaser_id">Purchased by</label>
                                <select class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="purchaser_id" name="purchaser_id" required>
                                    <option value="">Select a member</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo htmlspecialchars((string) $member['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string) ($old['purchaser_id'] ?? '') === (string) $member['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($member['full_name'] . ' (' . $member['email'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['purchaser_id'])): ?>
                                    <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['purchaser_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="expense_date">Expense date</label>
                                <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="expense_date" name="expense_date" type="date" value="<?php echo htmlspecialchars($old['expense_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <?php if (!empty($errors['expense_date'])): ?>
                                    <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['expense_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="amount">Amount</label>
                                <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="amount" name="amount" type="number" step="0.01" min="0" value="<?php echo htmlspecialchars((string) ($old['amount'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                <?php if (!empty($errors['amount'])): ?>
                                    <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['amount'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="vendor">Vendor (optional)</label>
                                <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="vendor" name="vendor" type="text" value="<?php echo htmlspecialchars($old['vendor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if (!empty($errors['vendor'])): ?>
                                    <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['vendor'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="notes">Notes (optional)</label>
                                <textarea class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <?php if (!empty($errors['notes'])): ?>
                                    <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['notes'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500" type="submit">Add expense</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="mt-8">
                <h2 class="text-lg font-semibold">Recent expenses</h2>
                <?php if (empty($recent)): ?>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm text-slate-500">No expenses recorded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">Mess</th>
                                    <th class="px-4 py-3 text-left">Purchaser</th>
                                    <th class="px-4 py-3 text-left">Date</th>
                                    <th class="px-4 py-3 text-left">Amount</th>
                                    <th class="px-4 py-3 text-left">Vendor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($recent as $row): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['mess_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['purchaser_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['expense_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars((string) $row['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3 text-slate-500"><?php echo htmlspecialchars($row['vendor'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
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
