<?php

function addTask($task_name) {
    $tasks = getAllTasks();
    foreach ($tasks as $task) {
        if (strcasecmp($task['name'], $task_name) === 0) return;
    }
    $id = time();
    file_put_contents("tasks.txt", "$id|$task_name|0\n", FILE_APPEND);
}

function getAllTasks() {
    if (!file_exists("tasks.txt")) return [];
    $lines = file("tasks.txt", FILE_IGNORE_NEW_LINES);
    $tasks = [];
    foreach ($lines as $line) {
        list($id, $name, $completed) = explode("|", $line);
        $tasks[] = ['id' => $id, 'name' => $name, 'completed' => $completed];
    }
    return $tasks;
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    $out = "";
    foreach ($tasks as $task) {
        $task['completed'] = ($task['id'] == $task_id) ? $is_completed : $task['completed'];
        $out .= "{$task['id']}|{$task['name']}|{$task['completed']}\n";
    }
    file_put_contents("tasks.txt", $out);
}

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $out = "";
    foreach ($tasks as $task) {
        if ($task['id'] != $task_id)
            $out .= "{$task['id']}|{$task['name']}|{$task['completed']}\n";
    }
    file_put_contents("tasks.txt", $out);
}

function generateVerificationCode() {
    return rand(100000, 999999);
}

function subscribeEmail($email) {
    $code = generateVerificationCode();
    file_put_contents("pending_subscriptions.txt", "$email|$code\n", FILE_APPEND);

    $link = "http://yourdomain/verify.php?email=" . urlencode($email) . "&code=$code";
    $subject = "Verify subscription to Task Planner";
    $message = "
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p><a id='verification-link' href='$link'>Verify Subscription</a></p>
    ";
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html\r\n";
    mail($email, $subject, $message, $headers);
}

function verifySubscription($email, $code) {
    if (!file_exists("pending_subscriptions.txt")) return;
    $lines = file("pending_subscriptions.txt", FILE_IGNORE_NEW_LINES);
    $remaining = "";
    $found = false;

    foreach ($lines as $line) {
        list($e, $c) = explode("|", $line);
        if ($e === $email && $c == $code) {
            file_put_contents("subscribers.txt", "$email\n", FILE_APPEND);
            $found = true;
        } else {
            $remaining .= "$line\n";
        }
    }
    file_put_contents("pending_subscriptions.txt", $remaining);
}

function unsubscribeEmail($email) {
    if (!file_exists("subscribers.txt")) return;
    $lines = file("subscribers.txt", FILE_IGNORE_NEW_LINES);
    $filtered = array_filter($lines, fn($line) => trim($line) !== $email);
    file_put_contents("subscribers.txt", implode("\n", $filtered) . "\n");
}

function sendTaskReminders() {
    $tasks = array_filter(getAllTasks(), fn($t) => !$t['completed']);
    if (empty($tasks)) return;
    if (!file_exists("subscribers.txt")) return;

    $emails = file("subscribers.txt", FILE_IGNORE_NEW_LINES);
    foreach ($emails as $email) {
        sendTaskEmail(trim($email), $tasks);
    }
}

function sendTaskEmail($email, $pending_tasks) {
    $taskList = "";
    foreach ($pending_tasks as $task) {
        $taskList .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }
    $unsubscribe = "http://yourdomain/unsubscribe.php?email=" . urlencode($email);
    $subject = "Task Planner - Pending Tasks Reminder";
    $message = "
        <h2>Pending Tasks Reminder</h2>
        <p>Here are the current pending tasks:</p>
        <ul>$taskList</ul>
        <p><a id='unsubscribe-link' href='$unsubscribe'>Unsubscribe from notifications</a></p>
    ";
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html\r\n";
    mail($email, $subject, $message, $headers);
}
