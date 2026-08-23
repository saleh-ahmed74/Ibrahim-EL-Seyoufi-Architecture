<?php
 $db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("UPDATE site_settings SET hero_image='projects/01M0D89MDYZRQ1KW1844DGDA50.jpg', profile_image='projects/01M0D89MDNN6KZ46VJ7V0WQDTJ.png' WHERE id=1;");
$stmt = $db->query("SELECT hero_image, profile_image FROM site_settings WHERE id=1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($row) . PHP_EOL;
