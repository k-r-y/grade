<?php
$_GET['action'] = 'get_class_students';
$_GET['class_id'] = 10; // Assuming class 10 from previous context
$_SESSION['user_id'] = 54; // Assuming teacher ID from previous context (Rogiemar Ramos)
$_SESSION['role'] = 'teacher';

// Mocking session for the script
session_start();
$_SESSION['user_id'] = 54;
$_SESSION['role'] = 'teacher';

require 'api.php';
?>
