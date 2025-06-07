<?php
require "modele/methode.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialisation des variables pour stocker les messages d'erreur et le type d'alerte
    $alertMessage = "";
    $alertTitle = "";
    $alertType = ""; // success, error, warning, info

    // Récupération des données du formulaire
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation des données
    $errors = false;

    // Vérification si les champs obligatoires sont remplis
    if (empty($email)||empty($password)) {
        $alertTitle = "Champs Manquants";
        $alertMessage = "Veuillez remplir tous les champs obligatoires.";
        $alertType = "error";
        $errors = true;
    }
    // Vérification si l'email a un format valide
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alertTitle = "Format Email Invalide";
        $alertMessage = "Veuillez entrer une adresse email valide.";
        $alertType = "error";
        $errors = true;
    }
    // Vérification si les mots de passe correspondent

    // Vérification de la complexité du mot de passe
    elseif (strlen($password) < 8) {
        $alertTitle = "Mot de Passe Trop Court";
        $alertMessage = "Votre mot de passe doit contenir au moins 8 caractères.";
        $alertType = "warning";
        $errors = true;
    }
    // Autres validations selon vos besoins...

    // Si tout est valide, tenter d'enregistrer l'utilisateur dans la base de données
    if (!$errors) {
        try {
            if(connexionE($db,$_POST['email'],$_POST['password'])){
                $utilisateur=recupUtilByMail($db,$_POST['email']);
                session_start();
                $_SESSION['id']=$utilisateur['id'];
                $_SESSION['nom']=$utilisateur['nom'];
                $_SESSION['prenom']=$utilisateur['prenom'];
                $_SESSION['filiere']=$utilisateur['filiere'];
                $_SESSION['classe']=$utilisateur['classe'];
                $_SESSION['niveau']=$utilisateur['niveau'];
                $_SESSION['role']=$utilisateur['role'];
                header("location:vue/delegue/traitement.php");
                exit();
            }
            // Ici, ajoutez votre code pour insérer l'utilisateur dans la base de données
            // Par exemple, avec PDO:
            /*
            $pdo = new PDO("mysql:host=localhost;dbname=votre_base", "utilisateur", "mot_de_passe");
            $stmt = $pdo->prepare("INSERT INTO etudiants (nom, prenom, email, niveau, filiere, classe, mot_de_passe) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$nom, $prenom, $email, $niveau, $filiere, $classe, $hashed_password]);
            */
            
            // Si l'insertion réussit, définir l'alerte de succès
            $alertTitle = "Connexion invalide";
            $alertMessage = "l un de vos parametre est erronnes";
            $alertType = "error";
            
        } catch (Exception $e) {
            // En cas d'erreur lors de l'insertion dans la base de données
            $alertTitle = "Erreur Système";
            $alertMessage = "Une erreur est survenue lors de la création de votre compte. Veuillez réessayer plus tard.";
            $alertType = "error";
            
            // Pour le débogage (à retirer en production)
            // error_log("Erreur d'inscription: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <style>
        /* Styles du formulaire d'inscription - Ajoutez ici le CSS de votre formulaire */
        
        /* Styles pour l'alerte */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(3px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s linear 0.25s, opacity 0.25s 0s;
        }

        .overlay.show {
            visibility: visible;
            opacity: 1;
            transition-delay: 0s;
        }

        .alert-container {
            width: 100%;
            max-width: 380px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideDown 0.4s ease-out forwards;
            position: relative;
            opacity: 0;
            transform: translateY(-20px);
        }

        .overlay.show .alert-container {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes slideDown {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-header {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            padding: 20px;
            position: relative;
            text-align: center;
        }

        .alert-error .alert-header {
            background: linear-gradient(135deg, #E53935, #C62828);
        }

        .alert-warning .alert-header {
            background: linear-gradient(135deg, #FF9800, #F57C00);
        }

        .alert-info .alert-header {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }

        .alert-header::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 9px;
            background: #E53935;
            transform: skewY(-1deg);
        }

        .alert-error .alert-header::after {
            background: #4CAF50;
        }

        .alert-warning .alert-header::after,
        .alert-info .alert-header::after {
            background: #757575;
        }

        .alert-icon {
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .alert-icon svg {
            width: 30px;
            height: 30px;
        }

        .alert-success .alert-icon svg {
            fill: #4CAF50;
        }

        .alert-error .alert-icon svg {
            fill: #E53935;
        }

        .alert-warning .alert-icon svg {
            fill: #FF9800;
        }

        .alert-info .alert-icon svg {
            fill: #2196F3;
        }

        .alert-body {
            padding: 25px 20px;
            text-align: center;
        }

        .alert-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }

        .alert-message {
            color: #666;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .alert-footer {
            padding: 0 20px 20px;
            text-align: center;
        }

        .btn-ok {
            background: #E53935;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 8px rgba(229, 57, 53, 0.3);
            width: 100%;
        }

        .alert-error .btn-ok {
            background: #4CAF50;
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }

        .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(229, 57, 53, 0.4);
        }

        .alert-error .btn-ok:hover {
            box-shadow: 0 6px 12px rgba(76, 175, 80, 0.4);
        }

        .pulse {
            position: absolute;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(76, 175, 80, 0.3);
            animation: pulse 1.5s infinite;
        }

        .alert-error .pulse {
            background: rgba(229, 57, 53, 0.3);
        }

        .alert-warning .pulse {
            background: rgba(255, 152, 0, 0.3);
        }

        .alert-info .pulse {
            background: rgba(33, 150, 243, 0.3);
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.7;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.4;
            }
            100% {
                transform: scale(0.95);
                opacity: 0.7;
            }
        }

        /* Ajoutez ici les styles pour votre formulaire */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        label{
            margin-left:30px;
        }
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 100%;
            height: 16px;
            background: #E53935;
            transform: skewY(-1deg);
        }

        form {
            padding: 35px 25px 25px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input, 
        .form-group select {
            width: 90%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-left:20px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #fafafa;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
            outline: none;
        }

        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            margin-right: 5px;
        }

        .radio-option input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #E53935;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 8px rgba(229, 57, 53, 0.3);
        }

        button:hover {
            background: #D32F2F;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }

        .footer a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Style visuel pour les sélecteurs */
        select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath d='M8 10.5l4-4H4l4 4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }

        /* Style pour rendre le formulaire plus moderne */
        .avatar-placeholder {
            width: 80px;
            height: 80px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -50px auto 15px;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .avatar-icon {
            width: 40px;
            height: 40px;
            fill: #888;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            animation: fadeIn 0.5s ease-out;
        }
    </style
    </style>
</head>
<body>
    <!-- Votre formulaire d'inscription ici -->
    <div class="container">
        <div class="header">
            <h1>Inscription Étudiant</h1>
            <p>Créez votre compte académique</p>
        </div>
        
        <div class="avatar-placeholder">
            <svg class="avatar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Académique</label>
                <input type="email" id="email" name="email" placeholder="votre.nom@etudiant.edu" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" >
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password">
            </div> 
            <button type="submit" name="submit">Connexion</button>
            
            <div class="footer">
                <p>Vous avez déjà un compte? <a href="vue/inscription.php">Connectez-vous ici</a></p>
            </div>
        </form>
    </div>

    <!-- Overlay pour l'alerte -->
    <?php if (isset($alertType) && !empty($alertType)): ?>
    <div class="overlay show" id="alertOverlay">
        <div class="alert-container alert-<?php echo $alertType; ?>">
            <div class="alert-header">
                <div class="pulse"></div>
                <div class="alert-icon">
                    <?php if ($alertType == 'success'): ?>
                    <!-- Icône de succès (check) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <?php elseif ($alertType == 'error'): ?>
                    <!-- Icône d'erreur (croix) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                    <?php elseif ($alertType == 'warning'): ?>
                    <!-- Icône d'avertissement (triangle) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                    <?php elseif ($alertType == 'info'): ?>
                    <!-- Icône d'information -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="alert-body">
                <h3 class="alert-title"><?php echo htmlspecialchars($alertTitle); ?></h3>
                <p class="alert-message"><?php echo htmlspecialchars($alertMessage); ?></p>
            </div>
            
            <div class="alert-footer">
                <button class="btn-ok" onclick="closeAlert()">OK</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Fonction pour fermer l'alerte
        function closeAlert() {
            document.getElementById('alertOverlay').classList.remove('show');
        }
    </script>
</body>
</html>
</body>
</html>