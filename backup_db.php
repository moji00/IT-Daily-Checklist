<?php
require 'config.php';

// Get current timestamp
$backupFile = 'backup_' . date("Y-m-d") . '.sql';

// Run mysqldump
$command = "mysqldump --user={$DB_USER} --password={$DB_PASS} --host={$DB_HOST} {$DB_NAME} > {$backupFile}";
system($command, $output);

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename={$backupFile}");
readfile($backupFile);

// Delete the temp file after download
unlink($backupFile);
exit;
?>
