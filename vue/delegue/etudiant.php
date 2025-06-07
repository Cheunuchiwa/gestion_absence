<?php
    require '../../modele/methode.php';
    session_start();
    $absences =recupUtilInfo($db,$_SESSION['id']);
    if(isset($_POST['detaille'])){
        header('Location: detaille?id='.$_SESSION['id']);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Présences</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <style>
             .badge {
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-green {
            background-color: var(--light-green);
            color: var(--green);
        }
        
        .badge-red {
            background-color: var(--light-red);
            color: var(--red);
        }
    </style>
    <div class="container">
        <header>
            <h1><i class="fas fa-user-check"></i> STU-ABSENCE</h1>
            <nav>
                <div class="dropdown">
                    <button class="dropbtn"><i class="fas fa-bars"></i> Menu</button>
                    <div class="dropdown-content">
                        <a href="etudiant.php" class="active"><i class="fas fa-home"></i> Tableau de bord</a>
                        <a href="tes.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
                        <a href="excuses.php"><i class="fas fa-file-alt"></i> Gestion des justificatifs</a>
                        <a href="deconnexion.php" class="activ"><i class="fas fa-file-alt"></i> deconnexion</a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <?php if (!empty($message)): ?>
                <?php echo $message; ?>
            <?php endif; ?>

            <section class="attendance-form">
                <form action="" method="post">
                <button class="btn-details" name="detaille">Détails</button>
                </form>
                <style>
                    .btn-details {
  background-color: #2563eb; /* bleu */
  color: white;
  font-size: 0.85rem;
  font-weight: 600;
  padding: 0.4rem 1rem;
  border: none;
  border-radius: 9999px; /* bouton arrondi */
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.1s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.btn-details:hover {
  background-color: #1d4ed8;
  transform: scale(1.03);
}

.btn-details:active {
  background-color: #1e40af;
  transform: scale(0.98);
}

                </style>
                 <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Matière</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="absences-list">
                    <?php foreach ($absences as $absence): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($absence['date']); ?></td>
                            <td><?php echo htmlspecialchars($absence['matiere']); ?></td>
                            <td><?php echo htmlspecialchars($absence['periode']); ?></td>
                           <td><?php if($absence['statut']=='justifier'){?><span class="badge badge-green">justifier</span><?php }else{ ?><span class="badge badge-red">non justifier<?php }?></td>
                            <td>
                                <button class="btn btn-primary" onclick="window.location.href='excuses.php?id=<?php echo $absence['date']; ?>'">Justifier</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
               
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> EduPresence - Système de gestion des présences</p>
        </footer>
    </div>

    <div id="notification" class="notification">
        <p id="notification-message"></p>
    </div>
    <style>
        /* Style de l'alerte */
        .alerte-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .alerte-contenu {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
            border: 2px solid #6c757d;
        }

        .alerte-contenu h2 {
            margin-top: 0;
            color: #343a40;
        }

        .alerte-contenu input[type="text"] {
            width: 90%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .alerte-contenu button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .alerte-contenu button:hover {
            background-color: #0056b3;
        }
    </style>
<!-- Div pour l'alerte -->
    <div id="alerte" class="alerte-overlay">
        <canvas>
            
        </canvas>
    </div>

    <script>
        const afficherAlerte = <?php echo json_encode($affiche); ?>;

// Affiche l'alerte si nécessaire
if (afficherAlerte) {
    document.addEventListener('DOMContentLoaded', function() {
        const alerte = document.getElementById('alerte');
        alerte.style.display = 'flex';
    });
}

// Gestion du formulaire (optionnel - pour éviter le rechargement si AJAX est préféré)
document.getElementById('formAlerte')?.addEventListener('submit', function(e) {
    // Ici vous pourriez faire une requête AJAX à la place
    // Pour cet exemple, on laisse le formulaire se soumettre normalement
});
    </script>
   
    <script src="script.js"></script>
</body>