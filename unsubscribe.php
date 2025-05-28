<?php
require_once 'functions.php';

if (isset($_GET['email'])) {
    unsubscribeEmail($_GET['email']);
    echo "You have been unsubscribed.";
} else {
    echo "Invalid request.";
}
