<?php
require_once 'functions.php';

if (isset($_GET['email']) && isset($_GET['code'])) {
    verifySubscription($_GET['email'], $_GET['code']);
    echo "Subscription verified!";
} else {
    echo "Invalid request.";
}
