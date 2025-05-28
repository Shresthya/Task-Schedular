<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        addTask(trim($_POST['task-name']));
    }

    if (isset($_POST['email'])) {
        subscribeEmail(trim($_POST['email']));
    }
}

if (isset($_GET['delete'])) {
    deleteTask($_GET['delete']);
}

if (isset($_GET['toggle'])) {
    markTaskAsCompleted($_GET['toggle'], $_GET['status'] === '1' ? 1 : 0);
}

$tasks = getAllTasks();
?>

<h2>Task Manager</h2>
<form method="POST">
    <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
    <button type="submit" id="add-task">Add Task</button>
</form>

<ul class="tasks-list">
    <?php foreach ($tasks as $task): ?>
        <li class="task-item <?= $task['completed'] ? 'completed' : '' ?>">
            <input type="checkbox" class="task-status" 
                onchange="location.href='?toggle=<?= $task['id'] ?>&status=<?= $task['completed'] ? '0' : '1' ?>'" 
                <?= $task['completed'] ? 'checked' : '' ?>>
            <?= htmlspecialchars($task['name']) ?>
            <a href="?delete=<?= $task['id'] ?>"><button class="delete-task">Delete</button></a>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Email Subscription</h2>
<form method="POST">
    <input type="email" name="email" required />
    <button id="submit-email">Submit</button>
</form>
