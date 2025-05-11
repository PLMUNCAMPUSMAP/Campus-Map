<?php
session_start();
include 'db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = bin2hex(random_bytes(32));
}
?>