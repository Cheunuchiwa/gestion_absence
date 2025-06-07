<?php
// Configuration de la variable $affiche selon votre logique métier
$affiche = false; // Mettez à true pour afficher l'alerte, false pour la cacher

// Initialisation de la variable pour stocker le message soumis
$message_soumis = "";

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["alert_text"])) {
    $message_soumis = htmlspecialchars($_POST["alert_text"]);
    
    // Vous pouvez maintenant utiliser $message_soumis dans votre logique PHP
    // Par exemple: l'enregistrer dans une base de données, l'envoyer par email, etc.
    
    // Exemple d'utilisation:
    // saveMessage($message_soumis);
    // sendEmail($message_soumis);
    
    echo "<div class='success-message'>Message reçu: " . $message_soumis . "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Designer</title>
    <style>
        /* Styles généraux */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .success-message {
            background-color: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        /* Style de l'alerte */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 350px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 16px;
            transform: translateX(400px);
            opacity: 0;
            transition: transform 0.4s ease-out, opacity 0.3s ease;
            z-index: 1000;
            border-left: 4px solid #4CAF50;
            visibility: hidden; /* Caché par défaut */
        }

        /* Classe qui contrôle la visibilité */
        .alert-container.visible {
            transform: translateX(0);
            opacity: 1;
            visibility: visible;
        }

        .alert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .alert-title {
            font-size: 18px;
            font-weight: 600;
            color: #333333;
            margin: 0;
        }

        .alert-close {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
            line-height: 1;
        }

        .alert-close:hover {
            color: #333;
        }

        .alert-content {
            color: #666666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .alert-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .alert-input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }

        .alert-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .alert-button:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <h1>Page avec Alerte Designer</h1>
    
    <!-- Alerte avec la classe conditionnelle basée sur la variable PHP $affiche -->
    <div class="alert-container <?php echo ($affiche ? 'visible' : ''); ?>">
        <div class="alert-header">
            <h3 class="alert-title">Notification</h3>
            <button type="button" class="alert-close">&times;</button>
        </div>
        <div class="alert-content">
            Veuillez saisir votre message dans le champ ci-dessous:
        </div>
        <form method="POST" action="">
            <input type="text" name="alert_text" class="alert-input" placeholder="Votre message...">
            <button type="submit" class="alert-button">Envoyer</button>
        </form>
    </div>

    <script>
        // JavaScript pour permettre la fermeture de l'alerte
        document.querySelector('.alert-close').addEventListener('click', function() {
            document.querySelector('.alert-container').classList.remove('visible');
        });
    </script>
</body>
</html>