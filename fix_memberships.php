<?php
declare(strict_types=1);


require __DIR__ . '/config/database.php';
require __DIR__ . '/app/Models/MessMembership.php';

$db = require __DIR__ . '/config/database.php';
$membershipModel = new \App\Models\MessMembership();


$stmt = $db->prepare("
    SELECT m.id, m.created_by
    FROM messes m
    LEFT JOIN mess_memberships mm ON mm.mess_id = m.id AND mm.user_id = m.created_by AND mm.role_in_mess = 'manager' AND mm.status = 'active'
    WHERE mm.id IS NULL
");
$stmt->execute();
$messesToFix = $stmt->fetchAll();

$fixed = 0;
foreach ($messesToFix as $mess) {
    $membershipModel->create((int) $mess['id'], (int) $mess['created_by'], 'manager');
    $fixed++;
}

echo "Fixed $fixed mess(es) by adding manager memberships for creators.\n";