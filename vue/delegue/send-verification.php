<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
require "../../modele/methode.php";

session_start();

// Get the verification code from the POST request
$code = $_POST['code'] ?? '';

if (!empty($code)) {
    // Call the existing sendMail function with the verification code
    sendMail('djoumessiivan2006@gmail.com', $code);
    echo "Code envoyé avec succès";
} else {
    http_response_code(400);
    echo "Erreur: Code manquant";
}

// The sendMail function is already defined in your original file
?>
