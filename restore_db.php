<?php
require 'config.php';

if (isset($_FILES['restore_file']) && $_FILES['restore_file']['error'] === 0) {
    $filePath = $_FILES['restore_file']['tmp_name'];

    // Import SQL into the database
    $command = "mysql --user={$DB_USER} --password={$DB_PASS} --host={$DB_HOST} {$DB_NAME} < {$filePath}";
    system($command, $output);

    header("Location: dashboard.php?restore=success");
    exit;
} else {
    header("Location: dashboard.php?restore=fail");
    exit;
}
?>
