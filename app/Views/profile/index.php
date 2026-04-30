<?php
$user = $user ?? null;
$profile = $profile ?? ['full_name' => '', 'email' => ''];
$memberMesses = $memberMesses ?? [];
$managedMesses = $managedMesses ?? [];
$errorsProfile = $errorsProfile ?? [];
$errorsPassword = $errorsPassword ?? [];
$flash = $flash ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | The Mess Hub</title>
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
                    <form method="post" action="/logout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="rounded-full border border-slate-200 px-4 py-2 text-slate-700 hover:border-slate-300" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl space-y-8 px-6 py-10">
            <header>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Account settings</p>
                <h1 class="mt-2 text-3xl font-semibold">Profile<?php echo $user ? ', ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') : ''; ?></h1>
            </header>

            <?php if (!empty($flash['success'])): ?>
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (!empty($flash['error'])): ?>
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">Update profile</h2>
                    <?php if (!empty($errorsProfile['general'])): ?>
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($errorsProfile['general'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <form class="mt-4 space-y-4" method="post" action="/profile">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="full_name">Full name</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="full_name" name="full_name" type="text" value="<?php echo htmlspecialchars($profile['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            <?php if (!empty($errorsProfile['full_name'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errorsProfile['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="email">Email</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="email" name="email" type="email" value="<?php echo htmlspecialchars($profile['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            <?php if (!empty($errorsProfile['email'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errorsProfile['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500" type="submit">Save profile</button>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">Change password</h2>
                    <?php if (!empty($errorsPassword['general'])): ?>
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($errorsPassword['general'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <form class="mt-4 space-y-4" method="post" action="/profile/password">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="current_password">Current password</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="current_password" name="current_password" type="password" required>
                            <?php if (!empty($errorsPassword['current_password'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errorsPassword['current_password'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="new_password">New password</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="new_password" name="new_password" type="password" required>
                            <?php if (!empty($errorsPassword['new_password'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errorsPassword['new_password'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="confirm_password">Confirm password</label>
                            <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="confirm_password" name="confirm_password" type="password" required>
                            <?php if (!empty($errorsPassword['confirm_password'])): ?>
                                <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errorsPassword['confirm_password'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500" type="submit">Update password</button>
                    </form>
                </div>
            </section>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Your mess info</h2>
                <?php if (empty($memberMesses)): ?>
                    <p class="mt-4 text-sm text-slate-500">You are not a member of any mess yet.</p>
                <?php else: ?>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Mess</th>
                                    <th class="px-4 py-2 text-left">Location</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($memberMesses as $mess): ?>
                                    <tr>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($mess['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($mess['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($mess['role_in_mess'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($managedMesses)): ?>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">Managed messes</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Mess</th>
                                    <th class="px-4 py-2 text-left">Location</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($managedMesses as $mess): ?>
                                    <tr>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($mess['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($mess['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
