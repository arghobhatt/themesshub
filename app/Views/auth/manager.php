<?php
$user = $user ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manager Area | The Mess Hub</title>
</head>
<body>
    <h1>Manager Area</h1>

    <?php if ($user): ?>
        <p>Signed in as <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <p><a href="/">Back to dashboard</a></p>
</body>
</html>
