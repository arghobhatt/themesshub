<?php
$user = $user ?? null;
$mealSummaries = $mealSummaries ?? [];
$periodLabel = $periodLabel ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | The Mess Hub</title>
</head>
<body>
    <h1>Welcome<?php echo $user ? ', ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') : ''; ?></h1>

    <?php if ($user): ?>
        <p>Email: <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Role: <?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <h2>Meal rate summary<?php echo $periodLabel !== '' ? ' (' . htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') . ')' : ''; ?></h2>

    <?php if (empty($mealSummaries)): ?>
        <p>No mess summary available yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Mess</th>
                    <th>Total expenses</th>
                    <th>Total meals</th>
                    <th>Meal rate</th>
                    <th>Your meals</th>
                    <th>Your deposits</th>
                    <th>Your balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mealSummaries as $summary): ?>
                    <?php
                    $balance = (float) ($summary['balance'] ?? 0);
                    $balanceLabel = $balance >= 0 ? 'Due' : 'Credit';
                    $balanceValue = number_format(abs($balance), 2);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($summary['mess']['name'] ?? 'Mess', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format((float) ($summary['total_expense'] ?? 0), 2); ?></td>
                        <td><?php echo number_format((float) ($summary['total_meals'] ?? 0), 2); ?></td>
                        <td><?php echo number_format((float) ($summary['rate_per_meal'] ?? 0), 4); ?></td>
                        <td><?php echo number_format((float) ($summary['user_meals'] ?? 0), 2); ?></td>
                        <td><?php echo number_format((float) ($summary['user_deposits'] ?? 0), 2); ?></td>
                        <td><?php echo htmlspecialchars($balanceLabel . ': ' . $balanceValue, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="/manager">Manager area</a> (managers only)</p>

    <form method="post" action="/logout">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
