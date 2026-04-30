<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Mess | The Mess Hub</title>
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
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Create new</p>
                <h1 class="mt-2 text-3xl font-semibold">Create Mess</h1>
            </header>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <?php if (!empty($errors['general'])): ?>
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form class="space-y-6" method="post" action="/messes" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="name">Mess name</label>
                        <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="name" name="name" type="text" value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        <?php if (!empty($errors['name'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="location">Location</label>
                        <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="location" name="location" type="text" value="<?php echo htmlspecialchars($old['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        <?php if (!empty($errors['location'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="rent">Monthly rent</label>
                        <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="rent" name="rent" type="number" step="0.01" min="0" value="<?php echo htmlspecialchars((string) ($old['rent'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (!empty($errors['rent'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['rent'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="description">Description</label>
                        <textarea class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" id="description" name="description" rows="4"><?php echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

        
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="image">Mess Image</label>
                        <div class="mt-2 relative" id="drop-zone">
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="image" name="image" type="file" accept="image/*" onchange="previewImage(this)">
                            <div id="upload-placeholder" class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-8 text-center transition-all hover:border-blue-400 hover:bg-blue-50/30">
                                <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l6-6a4 4 0 015.656 0L28 34m0-14v-2a4 4 0 014-4h4"></path>
                                </svg>
                                <p class="mt-3 text-sm text-slate-600">Click or drag an image here</p>
                                <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP up to 5MB</p>
                            </div>
                            <div id="preview-container" class="hidden rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                                <img id="image-preview" class="w-full h-48 object-cover" src="" alt="Preview">
                                <div class="flex items-center justify-between bg-slate-50 px-4 py-2">
                                    <span id="file-name" class="text-xs text-slate-600 truncate"></span>
                                    <button type="button" onclick="clearImage()" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Remove</button>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($errors['image'])): ?>
                            <p class="mt-1 text-xs text-rose-600"><?php echo htmlspecialchars($errors['image'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition" type="submit">Create Mess</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('file-name').textContent = input.files[0].name;
                    document.getElementById('upload-placeholder').classList.add('hidden');
                    document.getElementById('preview-container').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function clearImage() {
            document.getElementById('image').value = '';
            document.getElementById('image-preview').src = '';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('preview-container').classList.add('hidden');
        }
    </script>
</body>
</html>
