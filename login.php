<?php
include_once './core/sessionController.php';

header('Content-Type: application/json');

$sessionController = new sessionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['otp'])) {
        $otp = $_POST['otp'];
        $result = $sessionController->verifyOtp($otp);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'getOtp') {
        $result = $sessionController->getOtp();
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        // Verify CAPTCHA
        $captchaSecret = '6LdjcIMqAAAAANy0BDCF1IRmzY6bBoKjUD-93xAd';
        $captchaVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$captchaSecret&response=$recaptchaResponse";
        $captchaResponse = file_get_contents($captchaVerifyUrl);
        $captchaData = json_decode($captchaResponse);

        if ($captchaData->success) {
            $result = $sessionController->login($username, $password);
        } else {
            $result = ['status' => 'error', 'message' => 'CAPTCHA verification failed'];
        }
    }

    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}