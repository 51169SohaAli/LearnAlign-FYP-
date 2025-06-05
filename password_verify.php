<?php
$hash = '$2y$10$AG1/dli7M1ydSA4N2Ng/Uupx05TpKfITVFDeU7xctQdfd4hIkQ3cy';
$password = 'Riphah51169'; // Replace with the password you want to test

if (password_verify($password, $hash)) {
    echo "Password is correct!";
} else {
    echo "Incorrect password.";
}

$password = 'Riphah51169';
$newHash = password_hash($password, PASSWORD_BCRYPT);
echo "New Hash: $newHash\n";

?>
