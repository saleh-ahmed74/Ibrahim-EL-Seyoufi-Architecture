<?php
$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query('SELECT count(*) as cnt FROM users');
$row = $stmt->fetch();
echo "Total users: " . $row['cnt'] . "\n";
if ($row['cnt'] > 0) {
    $stmt = $db->query('SELECT id, name, email FROM users LIMIT 1');
    $user = $stmt->fetch();
    echo "First user: " . $user['email'] . " (" . $user['name'] . ")\n";
}
