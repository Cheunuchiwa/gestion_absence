<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
require "../../modele/methode.php";

session_start();
// Validation des données
$students = recupEtudiantByClass($db,$_SESSION['niveau'],$_SESSION['filiere'],$_SESSION['classe']);
$matieres=recupMatiere($db);

// Variable pour stocker le code de vérification
$verificationCode = '';
$showVerificationForm = false;
$message = '';

// Fonction pour envoyer le code par email
function sendVerificationCode($email, $code) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                  
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'uchiwacheun@gmail.com';                     
        $mail->Password   = 'xiyymcoyfqjaoiku';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        $mail->Port       = 587;                                    
    
        //Recipients
        $mail->setFrom('uchiwacheun@gmail.com', 'studAscence');
        $mail->addAddress('djoumessiivan2006@gmail.com', 'Joe User');     
    
        $mail->isHTML(true);                                  
        $mail->Subject = "Code de vérification - studAbscence";
        $mail->Body    = '<p>Votre code de confirmation est : </p><br><div><strong>' . $code . '</strong></div>';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Traitement du formulaire d'appel
if (isset($_POST['submit_attendance'])) {
    // Générer un code de vérification à 4 chiffres
    $verificationCode = mt_rand(1000, 9999);
    
    // Stocker les données du formulaire en session pour les récupérer après vérification
    $_SESSION['form_data'] = $_POST;
    
    // Envoyer le code par email
    if (sendVerificationCode('djoumessiivan2006@gmail.com', $verificationCode)) {
        // Stocker le code en session
        $_SESSION['verification_code'] = $verificationCode;
        $showVerificationForm = true;
        $message = '<div class="success-message">Un code de vérification a été envoyé à votre email.</div>';
    } else {
        $message = '<div class="error-message">Erreur lors de l\'envoi du code de vérification.</div>';
    }
}

// Traitement de la vérification du code
if (isset($_POST['verify_code'])) {
    $enteredCode = $_POST['verification_code'];
    
    if ($enteredCode == $_SESSION['verification_code']) {
        // Code correct, traiter les données du formulaire original
        $formData = $_SESSION['form_data'];
        foreach ($students as $student) {
            if(isset($formData['check'.$student['id']])) {
                
            }else{
                $idEtudiant = $student['id'];
                $date = $formData['date'];
                $subject = $formData['subject'];
                $startTime = $formData['periode'];
                
                // Enregistrer la présence dans la base de données
                insertA($db, $date,$subject,$startTime, $idEtudiant);
            }
        }
        
        $message = '<div class="success-message">Code vérifié avec succès! L\'appel a été soumis.</div>';
        
        // Nettoyer les données de session
        unset($_SESSION['verification_code']);
        unset($_SESSION['form_data']);
        $showVerificationForm = false;
    } else {
        $message = '<div class="error-message">Code incorrect. Veuillez réessayer.</div>';
        $showVerificationForm = true;
    }
}

$nbreA=0;
$date = $_POST['date'] ?? '';
$subject = $_POST['subject'] ?? '';
$startTime = $_POST['periode'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Présences</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles pour le formulaire de vérification */
        .verification-form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .verification-form h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .verification-form input[type="text"] {
            font-size: 1.5rem;
            padding: 10px;
            width: 150px;
            text-align: center;
            letter-spacing: 5px;
            margin: 15px 0;
            border: 2px solid #ddd;
            border-radius: 4px;
        }
        
        .verification-form button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 0 5px;
        }
        
        .verification-form button.cancel {
            background-color: #e74c3c;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-user-check"></i> EduPresence</h1>
            <nav>
                <div class="dropdown">
                    <button class="dropbtn"><i class="fas fa-bars"></i> Menu</button>
                    <div class="dropdown-content">
                        <a href="acceuilD.php" class="active"><i class="fas fa-home"></i> Tableau de bord</a>
                        <a href="deconnexion.php" class=""><i class="fas fa-home"></i> deconnexion</a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <?php if (!empty($message)): ?>
                <?php echo $message; ?>
            <?php endif; ?>

            <?php if ($showVerificationForm): ?>
                <!-- Formulaire de vérification du code -->
                <section class="verification-form">
                    <h3><i class="fas fa-lock"></i> Vérification</h3>
                    <p>Un code de vérification a été envoyé à djoumessiivan2006@gmail.com</p>
                    <form method="POST" action="">
                        <div>
                            <input type="text" name="verification_code" placeholder="Code" maxlength="4" required>
                        </div>
                        <div>
                            <button type="submit" name="verify_code">Vérifier</button>
                            <button type="button" class="cancel" onclick="window.location.href='acceuilD.php'">Annuler</button>
                        </div>
                    </form>
                </section>
            <?php else: ?>
                <!-- Formulaire d'appel normal -->
                <section class="attendance-form">
                    <h2><i class="fas fa-clipboard-list"></i> Faire l'appel</h2>
                    <form method="POST">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="date"><i class="fas fa-calendar-alt"></i> Date:</label>
                                    <input type="date" id="date" name="date" min="<?php echo date('y-m-d') ?>" required>
                                </div>
                                <div class="form-field">
                                    <label for="subject"><i class="fas fa-book"></i> Matière:</label>
                                    <select id="subject" name="subject" required>
                                        <option value="">Sélectionner une matière</option>
                                        <?php foreach($matieres as $mat){?>
                                        <option value="<?php echo $mat['nom'] ?>"><?php echo $mat['nom'] ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="periode"><i class="fas fa-clock"></i>selectionnez une periode</label>
                                    <select id="periode" name="periode" required>
                                        <option value="8H-10H">8H-10H</option>
                                        <option value="10H-12H">10H-12H</option>
                                        <option value="13H-15H">13H-15H</option>
                                        <option value="15H-17H">15H-17H</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="student-list">
                            <h3><i class="fas fa-users"></i> Liste des étudiants</h3>
                            <div class="search-bar">
                                <input type="text" id="search-student" placeholder="Rechercher un étudiant...">
                                <i class="fas fa-search search-icon"></i>
                            </div>
                            <table id="students-table">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Présent</th>
                                    </tr>
                                </thead>
                                <tbody id="students-body">
                                    <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($student['prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($student['nom']); ?></td>
                                        <td>
                                            <label class="checkbox-container">
                                                <input type="checkbox" name="check<?php echo $student['id']; ?>"  checked>
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                                        
                        <div class="submit-section">
                            <button type="submit" name="submit_attendance" class="btn-submit">
                                <i class="fas fa-check"></i> Soumettre l'appel
                            </button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> EduPresence - Système de gestion des présences</p>
        </footer>
    </div>

    <div id="notification" class="notification">
        <p id="notification-message"></p>
    </div>
    <script src="script.js"></script>
</body>
</html>
