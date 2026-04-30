<?php
$messes = $messes ?? [];
$user = $user ?? null;
$role = $user['role'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mess Listings | The Mess Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,197,94,0.22),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(59,130,246,0.20),_transparent_28%),linear-gradient(180deg,_#020617_0%,_#0f172a_55%,_#111827_100%)] font-['Space_Grotesk']">
        <nav class="sticky top-0 z-20 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-emerald-300/80">The Mess Hub</p>
                    <a class="text-lg font-semibold text-white" href="/dashboard">Mess listings</a>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <?php if ($role === 'manager'): ?>
                        <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/messes/create">Create mess</a>
                    <?php endif; ?>
                    <a class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-slate-200 hover:bg-white/10" href="/dashboard">Dashboard</a>
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
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.45em] text-emerald-300/80">Mess directory</p>
                        <h1 class="mt-3 max-w-3xl text-4xl font-semibold tracking-tight text-white">
                            All available messes
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300">
                            Browse all active mess listings. <?php if ($role === 'manager'): ?>You can create new messes or manage existing ones.<?php else: ?>Apply to join messes that interest you.<?php endif; ?>
                        </p>
                    </div>
                    <?php if ($role === 'manager'): ?>
                        <a class="rounded-full bg-violet-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-violet-300" href="/messes/create">
                            Create new mess
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <section>
                <?php if (empty($messes)): ?>
                    <div class="rounded-[2rem] border border-dashed border-white/20 bg-white/5 p-12 text-center backdrop-blur-xl">
                        <p class="text-slate-300">No mess listings available yet.</p>
                        <?php if ($role === 'manager'): ?>
                            <p class="mt-2 text-sm text-slate-400">Be the first to create one!</p>
                            <a class="mt-4 inline-block rounded-full bg-violet-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-violet-300" href="/messes/create">
                                Create mess
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($messes as $mess): ?>
                            <article class="rounded-[2rem] border border-white/10 bg-white/5 backdrop-blur-xl shadow-2xl shadow-emerald-950/10 overflow-hidden">
                                <?php if (!empty($mess['image'])): ?>
                                    <img class="w-full h-44 object-cover" src="<?php echo htmlspecialchars($mess['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($mess['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php else: ?>
                                    <div class="w-full h-44 bg-gradient-to-br from-emerald-600/30 via-blue-600/20 to-violet-600/30 flex items-center justify-center">
                                        <svg class="h-14 w-14 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 4l9 5.5v9L12 24l-9-5.5v-9z M12 4v20 M3 9.5l9 5.5 9-5.5"></path></svg>
                                    </div>
                                <?php endif; ?>
                                <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-white">
                                            <?php echo htmlspecialchars($mess['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </h3>
                                        <p class="mt-2 text-sm text-slate-300">
                                            <?php echo htmlspecialchars($mess['location'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <?php if (!empty($mess['rent'])): ?>
                                            <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200">
                                                ৳<?php echo number_format((float) $mess['rent'], 2); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="rounded-full bg-slate-400/15 px-3 py-1 text-xs font-semibold text-slate-200">
                                                Rent on request
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-slate-300">
                                    <?php echo htmlspecialchars($mess['description'] ?: 'No description provided.', ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <div class="mt-6 flex gap-3">
                                    <?php if ($role === 'manager'): ?>
                                        <a class="flex-1 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-white/10" href="/messes/edit?id=<?php echo htmlspecialchars((string) $mess['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            Manage
                                        </a>
                                    <?php elseif ($role === 'seeker'): ?>
                                        <form method="post" action="/join-requests" class="flex-1">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="mess_id" value="<?php echo htmlspecialchars((string) $mess['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <button class="w-full rounded-full bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300" type="submit">
                                                Apply to join
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="flex-1 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-center text-sm text-slate-400">
                                            Join a mess first
                                        </span>
                                    <?php endif; ?>
                                </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>