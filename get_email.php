<?php
include_once 'core/sessionController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $controller = new sessionController();
    $response = $controller->getEmailByUsername($username);
    echo json_encode($response);
}
?>