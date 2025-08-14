<?php
require 'config.php';
if(!is_logged_in()){ header('Location:index.php'); exit; }

$task_id = intval($_POST['task_id'] ?? 0);
if($task_id){
    // delete user_tasks entries and the base tasks record
    $d1 = $mysqli->prepare('DELETE FROM user_tasks WHERE task_id = ?');
    $d1->bind_param('i', $task_id);
    $d1->execute();
    $d1->close();

    $d2 = $mysqli->prepare('DELETE FROM tasks WHERE id = ?');
    $d2->bind_param('i', $task_id);
    $d2->execute();
    $d2->close();
}
header("Location: user_dashboard.php?delete_success=1");
exit;
