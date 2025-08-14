<?php
require 'config.php';

if (!is_logged_in()) {
    header('Location:index.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? 'Server';

if (!$title) {
    header('Location:user_dashboard.php?error=missing_title');
    exit;
}

$stmt = $mysqli->prepare('INSERT INTO tasks (title, description, category, created_by) VALUES (?, ?, ?, ?)');
$uid = $_SESSION['user_id'];
$stmt->bind_param('sssi', $title, $desc, $category, $uid);

if ($stmt->execute()) {
    // Success modal HTML and auto-redirect script
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Added</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="modal fade show" id="successModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-success">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">✅ Task Added</h5>
                    </div>
                    <div class="modal-body">
                        Your task "<strong>' . htmlspecialchars($title) . '</strong>" has been successfully added.
                    </div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function(){
                window.location.href = "user_dashboard.php";
            }, 2000);
        </script>
    </body>
    </html>
    ';
} else {
    header('Location:user_dashboard.php?error=db_error');
}
exit;
?>
