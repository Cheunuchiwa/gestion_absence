<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
require "../../modele/methode.php";

// Fonction de test d'envoi d'email
function testSendMail() {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;    // Activer le débogage détaillé
        $mail->isSMTP();                          // Utiliser SMTP
        $mail->Host       = 'smtp.gmail.com';     // Serveur SMTP
        $mail->SMTPAuth   = true;                 // Activer l'authentification SMTP
        $mail->Username   = 'uchiwacheun@gmail.com'; // Nom d'utilisateur SMTP
        $mail->Password   = 'xiyymcoyfqjaoiku';   // Mot de passe SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le chiffrement TLS
        $mail->Port       = 587;                  // Port TCP à connecter

        // Destinataires
        $mail->setFrom('uchiwacheun@gmail.com', 'studAscence');
        $mail->addAddress('djoumessiivan2006@gmail.com', 'Ivan');

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = "Test d'envoi d'email - studAbscence";
        $mail->Body    = '<p>Ceci est un test d\'envoi d\'email.</p><p>Code de test: 1234</p>';

        $mail->send();
        echo "Message envoyé avec succès!";
    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
    }
}

// Exécuter le test
testSendMail();
?>
