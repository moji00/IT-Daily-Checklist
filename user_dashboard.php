<?php
require 'config.php';
if(!is_logged_in()){
    header('Location:index.php');
    exit;
}

$uid = isset($_GET['view_user']) && is_admin() ? intval($_GET['view_user']) : $_SESSION['user_id'];
$today = date('Y-m-d');

// Ensure tasks exist for today (if tasks table has some global tasks)
$tasks_result = $mysqli->query('SELECT * FROM tasks ORDER BY category DESC, id');
$tasks = $tasks_result ? $tasks_result->fetch_all(MYSQLI_ASSOC) : [];
foreach($tasks as $t){
    $stmt = $mysqli->prepare('INSERT IGNORE INTO user_tasks (user_id,task_id,task_date,status) VALUES (?,?,?,"pending")');
    $stmt->bind_param('iis', $uid, $t['id'], $today);
    $stmt->execute();
    $stmt->close();
}

// Fetch tasks for user today
$stmt = $mysqli->prepare('SELECT ut.id AS utid, t.id AS tid, t.title, t.description, t.category, ut.status 
                          FROM user_tasks ut 
                          JOIN tasks t ON ut.task_id = t.id 
                          WHERE ut.user_id=? AND ut.task_date=? 
                          ORDER BY t.category DESC, t.id ASC');
$stmt->bind_param('is', $uid, $today);
$stmt->execute();
$res = $stmt->get_result();
$user_tasks = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();


// Fetch descriptions for all tasks and today's status per description
$taskIds = array_column($user_tasks, 'tid');
$descs_by_task = [];
if (!empty($taskIds)) {
    $in = implode(',', array_map('intval', $taskIds));
    $sql = "SELECT td.id AS desc_id, td.task_id, td.text,
                   COALESCE(udc.status, 'pending') AS dstatus
            FROM task_descriptions td
            LEFT JOIN user_description_checks udc
              ON udc.task_description_id = td.id
             AND udc.user_id = ?
             AND udc.check_date = ?
            WHERE td.task_id IN ($in)
            ORDER BY td.id ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('is', $uid, $today);
    $stmt->execute();
    $resD = $stmt->get_result();
    while($row = $resD->fetch_assoc()){
        $descs_by_task[$row['task_id']][] = $row;
    }
    $stmt->close();
}

// Progress stats
$completed = 0;
foreach($user_tasks as $u){
    if($u['status'] === 'completed') $completed++;
}
$total = count($user_tasks);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>User Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head>
<body>

<!-- Top bar -->
<div class="topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="brand">
      <div class="logo">🔷</div>
      <div>
        <div style="font-weight:700">IT Daily Checklist</div>
      </div>
    </div>
    <div class="header-right">
      <div class="me-3">
        <input class="form-control" value="<?php echo date('F jS, Y'); ?>" readonly style="width:200px;border-radius:10px;">
      </div>
      <div class="dropdown">
        <button class="btn account-btn" data-bs-toggle="dropdown">
          <?php echo htmlspecialchars($_SESSION['name']); ?>
          <span class="text-muted" style="display:block;font-size:12px">User</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
          <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Stats Cards -->
<div class="container my-4">
  <div class="row g-3">
    <div class="col-md-3">
      <div class="card-ghost h-100">
        <small>Overall Progress</small>
        <h3 class="mt-2"><?php echo $completed . '/' . $total; ?></h3>
        <div class="progress mt-2">
          <div class="progress-bar" role="progressbar" style="width: <?php echo $total ? round($completed/$total*100) : 0; ?>%"></div>
        </div>
        <div class="text-muted small mt-2">
          <?php echo $total ? round($completed/$total*100) : 0; ?>% complete
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card-ghost h-100">
        <small>Category</small>
        <h3 class="mt-2">
          <?php
            $crit = $mysqli->query("SELECT COUNT(*) AS c FROM tasks WHERE category='critical'")->fetch_assoc()['c'] ?? 0;
            echo $crit;
          ?>
        </h3>
        <div class="text-muted small"></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card-ghost h-100">
        <small>Status</small>
        <h3 class="mt-4">Pending</h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card-ghost h-100">
        <small>Actions</small>
        <div class="mt-2">
          <!-- Download Checklist Button with Download Icon -->
        <a class="btn btn-outline-secondary w-100 mb-2" href="export_csv.php">
         <i class="bi bi-download"></i> Download Checklist
          </a>

          <!-- Reset All Button with Reset Icon -->
          <form method="post" action="reset_all.php">
           <button class="btn btn-outline-secondary w-100 mb-2">
            <i class="bi bi-arrow-counterclockwise"></i> Reset All
              </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Task list -->
  <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
    <h4>Daily IT Tasks</h4>
    <?php if(is_admin()): ?><a class="btn btn-primary" href="admin_dashboard.php">Admin</a><?php endif; ?>
    <a class="btn" href="#addTask" data-bs-toggle="modal" style="background:var(--btn-primary);color:#fff;border-radius:8px">+ Add Task</a>
  </div>

  <div class="tasks">
    <?php foreach($user_tasks as $ut): ?>
      <div class="task-card mb-3 d-flex align-items-start p-3 border rounded bg-white">
        <div class="flex-grow-1">
            <div style="font-weight:600"><?php echo htmlspecialchars($ut['title']); ?></div>
            <?php
            // Existing main description shown (if any) without checkbox change
           if (trim($ut['description']) !== ''): ?>
              <div class="text-muted small d-flex align-items-center mb-1">
                <input type="checkbox" class="me-2"
                      onchange="toggleMainDescription(<?php echo (int)$ut['utid']; ?>, this.checked)"
                      <?php echo $ut['status']==='completed' ? 'checked' : ''; ?>>
                <?php echo htmlspecialchars($ut['description']); ?>
              </div>
              <?php endif; ?>

            <?php if (!empty($descs_by_task[$ut['tid']])): foreach($descs_by_task[$ut['tid']] as $d): ?>
              <div class="text-muted small d-flex align-items-center mb-1">
                <input type="checkbox" class="me-2"
                       onchange="toggleDescription(<?php echo (int)$d['desc_id']; ?>, this.checked)"
                       <?php echo $d['dstatus']==='completed' ? 'checked' : ''; ?>>
                <?php echo htmlspecialchars($d['text']); ?>
              </div>
            <?php endforeach; endif; ?>

          <div class="mt-2"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($ut['category']); ?></span></div>
        </div>
        <div class="text-end">
          <div class="mb-2"><span class="text-muted small">status: <?php echo htmlspecialchars($ut['status']); ?></span></div>
          <div class="btn-group">
            <!-- Edit opens modal -->
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal<?php echo $ut['tid']; ?>" title="Edit">
              <i class="bi bi-pencil"></i>
            </button>

            <!-- Delete opens modal -->
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskModal<?php echo $ut['tid']; ?>" title="Delete">
              <i class="bi bi-trash"></i>
            </button>
          </div>

        </div>
      </div>

      <!-- Edit Task Modal -->
      <div class="modal fade" id="editTaskModal<?php echo $ut['tid']; ?>" tabindex="-1">
        <div class="modal-dialog">
          <form method="post" action="edit_task.php" class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Task</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="task_id" value="<?php echo $ut['tid']; ?>">
              <div class="mb-2"><label>Title</label><input name="title" class="form-control" value="<?php echo htmlspecialchars($ut['title']); ?>" required></div>
              <div class="mb-2"><label>Description</label><textarea name="description" class="form-control"><?php echo htmlspecialchars($ut['description']); ?></textarea></div>
              <div class="mb-2"><label>Category</label>
                <select name="category" class="form-select">
                  <option value="Security" <?php if($ut['category']=="Security") echo "selected"; ?>>Security</option>
                  <option value="Server" <?php if($ut['category']=="Server") echo "selected"; ?>>Server</option>
                  <option value="Backup" <?php if($ut['category']=="Backup") echo "selected"; ?>>Backup</option>
                  <option value="Network" <?php if($ut['category']=="Network") echo "selected"; ?>>Network</option>
                </select>
              </div>

              <!-- Sub-descriptions (each line will have its own checkbox on the dashboard) -->
              <div class="mb-2">
                <label>Sub-descriptions</label>
                <div id="descList<?php echo $ut['tid']; ?>">
                  <?php
                  if (!empty($descs_by_task[$ut['tid']])):
                    foreach($descs_by_task[$ut['tid']] as $d):
                  ?>
                    <div class="input-group mb-1">
                      <input type="hidden" name="desc_id[]" value="<?php echo (int)$d['desc_id']; ?>">
                      <input type="text" name="desc_text[]" class="form-control" value="<?php echo htmlspecialchars($d['text']); ?>" placeholder="Sub-description">
                      <button type="button" class="btn btn-outline-secondary" onclick="this.parentElement.querySelector('input[name=\'desc_text[]\']').value=''">Clear</button>
                    </div>
                  <?php
                    endforeach;
                  endif;
                  ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDescRow(<?php echo $ut['tid']; ?>)">+ Add another</button>
              </div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">Save Changes</button></div>
          </form>
        </div>
      </div>

      <!-- Delete Task Modal -->
      <div class="modal fade" id="deleteTaskModal<?php echo $ut['tid']; ?>" tabindex="-1">
        <div class="modal-dialog">
          <form method="post" action="delete_task.php" class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Delete Task</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="task_id" value="<?php echo $ut['tid']; ?>">
              <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($ut['title']); ?></strong>?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button class="btn btn-danger">Delete</button>
            </div>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Add Task Modal (unchanged) -->
<div class="modal fade" id="addTask" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="add_task.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label>Title</label>
          <input name="title" class="form-control" placeholder="Enter task title" required>
        </div>
        <div class="mb-2">
          <label>Description</label>
          <textarea name="description" class="form-control" placeholder="Enter task description"></textarea>
        </div>
        <div class="mb-2">
          <label>Category</label>
          <select name="category" class="form-select" required>
            <option value="Security">Security</option>
            <option value="Server">Server</option>
            <option value="Backup">Backup</option>
            <option value="Network">Network</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Add</button>
      </div>
    </form>
  </div>
</div>


<!-- Change Password Modal (unchanged) -->
<div class="modal fade" id="changePasswordModal">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="change_password.php" class="modal-content">
      <div class="modal-header"><h5>Change Password</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        <div class="mb-3"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
        <div class="mb-3"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
        <div class="mb-3"><label>Confirm New Password</label><input type="password" name="confirm_password" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleTask(utid, checked){
  fetch('toggle_task.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'utid='+encodeURIComponent(utid)+'&status='+(checked?'completed':'pending')
  }).then(r=>r.text()).then(()=>location.reload());
}
</script>



<script>
function toggleMainDescription(utid, checked){
  fetch('toggle_task.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'utid='+encodeURIComponent(utid)+'&status='+(checked?'completed':'pending')
  }).then(r=>r.text()).then(()=>location.reload());
}

function addDescRow(tid){
  const wrap = document.getElementById('descList'+tid);
  if(!wrap) return;
  const div = document.createElement('div');
  div.className = 'input-group mb-1';
  div.innerHTML = '<input type="hidden" name="desc_id[]" value="0">'+
                  '<input type="text" name="desc_text[]" class="form-control" placeholder="Sub-description">'+
                  '<button type="button" class="btn btn-outline-secondary" onclick="this.parentElement.remove()">Remove</button>';
  wrap.appendChild(div);
}
</script>

</body>
</html>
