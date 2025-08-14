<?php
require 'config.php';
if(!is_logged_in()){ header('Location:index.php'); exit; }

$task_id = intval($_POST['task_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? 'Security';

if($task_id && $title){
    // Update task fields
    $stmt = $mysqli->prepare('UPDATE tasks SET title=?, description=?, category=? WHERE id=?');
    $stmt->bind_param('sssi', $title, $description, $category, $task_id);
    $stmt->execute();
    $stmt->close();

    // Handle multiple sub-descriptions (text lines under a task title)
    $desc_ids = $_POST['desc_id'] ?? [];        // may contain empty strings for new ones
    $desc_texts = $_POST['desc_text'] ?? [];    // texts aligned to desc_id

    if (is_array($desc_texts) && is_array($desc_ids)) {
        for($i=0; $i<count($desc_texts); $i++){
            $did = intval($desc_ids[$i] ?? 0);
            $txt = trim($desc_texts[$i] ?? '');

            if($did > 0 && $txt !== ''){
                // Update existing
                $st = $mysqli->prepare('UPDATE task_descriptions SET text=? WHERE id=? AND task_id=?');
                $st->bind_param('sii', $txt, $did, $task_id);
                $st->execute();
                $st->close();
            } elseif($did > 0 && $txt === ''){
                // Delete if text cleared
                $st = $mysqli->prepare('DELETE FROM task_descriptions WHERE id=? AND task_id=?');
                $st->bind_param('ii', $did, $task_id);
                $st->execute();
                $st->close();
            } elseif($did === 0 && $txt !== ''){
                // Insert new
                $st = $mysqli->prepare('INSERT INTO task_descriptions (task_id, text) VALUES (?,?)');
                $st->bind_param('is', $task_id, $txt);
                $st->execute();
                $st->close();
            }
        }
    }
}
header("Location: user_dashboard.php?edit_success=1");
exit;
