<?php
require_once '../includes/db.php';

$adminEmail = "admin@minae.mg";
$admin = $usersCollection->findOne(['email' => $adminEmail]);

if (!$admin) {
    $usersCollection->insertOne([
        'email' => $adminEmail,
        'name' => 'Admin Principal',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin'
    ]);
    echo "? Admin inséré avec succès.";
} else {
    echo "?? L'administrateur existe déjà.";
}
?>
