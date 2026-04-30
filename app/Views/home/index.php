<?php
$messes = $messes ?? [];
$user = $user ?? null;
$flash = $flash ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Mess Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 bg-white px-3 py-2 rounded shadow" href="#listings">
        Skip to listings
    </a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">The Mess Hub</p>
                <h1 class="text-2xl font-semibold">Shared Housing and Finance Manager</h1>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                <a class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100" href="#contact">Contact</a>
                <?php if ($user): ?>
                    <span class="text-slate-600">Hi, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <a class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100" href="/messes">Messes</a>
                    <a class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100" href="/dashboard">Dashboard</a>
                <?php else: ?>
                    <a class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100" href="/login">Login</a>
                    <a class="rounded bg-slate-900 px-3 py-2 text-white hover:bg-slate-800" href="/register">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-10">
        <?php if (!empty($flash['success'])): ?>
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                <?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_1fr] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">Find your next mess</p>
                <h2 class="mt-3 text-4xl font-semibold leading-tight text-slate-900">
                    Discover shared housing that fits your budget and lifestyle.
                </h2>
                <p class="mt-4 text-lg text-slate-600">
                    Browse verified mess listings with transparent rent, location, and shared meal plans.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a class="rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-500" href="/messes">Browse listings</a>
                    <a class="rounded border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-100" href="/register">Join as seeker</a>
                </div>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-700 p-6 text-white">
                <h3 class="text-xl font-semibold">Why The Mess Hub?</h3>
                <ul class="mt-4 space-y-3 text-sm text-slate-100">
                    <li>Track rent, meals, and expenses in one place.</li>
                    <li>See real-time availability before you apply.</li>
                    <li>Managers can keep everything transparent.</li>
                </ul>
            </div>
        </section>

        <!-- Create Mess CTA -->
        <?php if ($user && ($user['role'] ?? '') === 'manager'): ?>
        <section class="mt-12 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 p-8 text-white shadow-lg">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-semibold">Create Your Mess</h2>
                    <p class="mt-2 text-emerald-100">List your mess, manage members, track expenses — all in one place.</p>
                </div>
                <a class="shrink-0 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-emerald-700 shadow hover:bg-emerald-50 transition" href="/messes/create">
                    + Create New Mess
                </a>
            </div>
        </section>
        <?php endif; ?>

        <section id="listings" class="mt-12">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold">Available mess listings</h2>
                <span class="text-sm text-slate-500">Total: <?php echo count($messes); ?></span>
            </div>

            <?php if (empty($messes)): ?>
                <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-slate-600">No mess listings yet. Managers can create the first one.</p>
                </div>
            <?php else: ?>
                <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($messes as $mess): ?>
                        <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                            <?php if (!empty($mess['image'])): ?>
                                <img class="w-full h-40 object-cover" src="<?php echo htmlspecialchars($mess['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($mess['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <div class="w-full h-40 bg-gradient-to-br from-slate-200 via-emerald-100 to-teal-100 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 22V12h6v10"></path></svg>
                                </div>
                            <?php endif; ?>
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="text-lg font-semibold text-slate-900">
                                        <?php echo htmlspecialchars($mess['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        <?php if (!empty($mess['rent'])): ?>
                                            ৳<?php echo number_format((float) $mess['rent'], 2); ?>
                                        <?php else: ?>
                                            On request
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm text-slate-600">
                                    <?php echo htmlspecialchars($mess['location'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <p class="mt-4 text-sm text-slate-700">
                                    <?php echo htmlspecialchars($mess['description'] ?: 'No description provided.', ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <div class="mt-auto pt-6">
                                    <?php if ($user && ($user['role'] ?? '') === 'seeker'): ?>
                                        <form method="post" action="/join-requests">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="mess_id" value="<?php echo htmlspecialchars((string) $mess['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <button class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-800 transition" type="submit">
                                                Apply to join
                                            </button>
                                        </form>
                                    <?php elseif (!$user): ?>
                                        <a class="block w-full rounded-xl border border-slate-300 px-4 py-2 text-center text-sm text-slate-700 hover:bg-slate-100 transition" href="/login">
                                            Login to apply
                                        </a>
                                    <?php else: ?>
                                        <span class="block text-center text-xs text-slate-500">Only seekers can apply</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="mt-16 mb-4">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="grid md:grid-cols-2">
                    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-800 p-8 md:p-10 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Get in touch</p>
                        <h2 class="mt-3 text-3xl font-semibold">Contact Us</h2>
                        <p class="mt-4 text-slate-300 text-sm leading-relaxed">Have questions, suggestions, or need help? We'd love to hear from you. Reach out and our team will get back to you shortly.</p>
                        <div class="mt-8 space-y-5">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <div><p class="text-xs text-slate-400">Email</p><p class="text-sm text-white">support@themesshub.com</p></div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <div><p class="text-xs text-slate-400">Phone</p><p class="text-sm text-white">+880 1XXX-XXXXXX</p></div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <div><p class="text-xs text-slate-400">Address</p><p class="text-sm text-white">Dhaka, Bangladesh</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 md:p-10">
                        <form class="space-y-5" id="contact-form" onsubmit="return handleContactSubmit(event)">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="contact_name">Your name</label>
                                <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200" id="contact_name" type="text" required placeholder="John Doe">
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="contact_email">Email</label>
                                <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200" id="contact_email" type="email" required placeholder="you@example.com">
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="contact_message">Message</label>
                                <textarea class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200" id="contact_message" rows="4" required placeholder="How can we help?"></textarea>
                            </div>
                            <button class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition" type="submit">Send Message</button>
                            <div id="contact-success" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">Thank you! We'll get back to you soon.</div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white mt-8">
        <div class="mx-auto max-w-6xl px-6 py-6 text-center text-sm text-slate-500">
            &copy; <?php echo date('Y'); ?> The Mess Hub. All rights reserved.
        </div>
    </footer>

    <script>
    function handleContactSubmit(e) {
        e.preventDefault();
        document.getElementById('contact-form').reset();
        document.getElementById('contact-success').classList.remove('hidden');
        setTimeout(() => document.getElementById('contact-success').classList.add('hidden'), 4000);
        return false;
    }
    </script>
</body>
</html>
