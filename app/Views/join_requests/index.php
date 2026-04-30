<?php
$requests = $requests ?? [];
$user = $user ?? null;
$flash = $flash ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join Requests | The Mess Hub</title>
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

        <main class="mx-auto max-w-6xl px-6 py-10">
            <header class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">Management</p>
                <h1 class="mt-2 text-3xl font-semibold">Join Requests</h1>
            </header>

            <?php if (!empty($flash['success'])): ?>
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($flash['error'])): ?>
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($user): ?>
                <p class="mb-6 text-sm text-slate-600">Manager: <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <?php endif; ?>

            <?php if (empty($requests)): ?>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-slate-600">No pending requests.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Mess</th>
                                <th class="px-4 py-3 text-left">Seeker</th>
                                <th class="px-4 py-3 text-left">Message</th>
                                <th class="px-4 py-3 text-left">Requested</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($requests as $request): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium"><?php echo htmlspecialchars($request['mess_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars($request['mess_location'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium"><?php echo htmlspecialchars($request['seeker_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars($request['seeker_email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-slate-600"><?php echo htmlspecialchars($request['message'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo htmlspecialchars($request['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <form method="post" action="/join-requests/approve">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars((string) $request['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <button class="rounded-lg bg-green-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-green-500" type="submit">Approve</button>
                                            </form>
                                            <form method="post" action="/join-requests/reject">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars((string) $request['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <button class="rounded-lg bg-rose-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-rose-500" type="submit">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
