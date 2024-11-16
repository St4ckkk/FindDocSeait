<?php
include_once './core/sessionController.php';

$sessionController = new sessionController();
$sessionController->logout();

header('Location: ../index.php');
exit();