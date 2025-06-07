<?php
require "../../modele/methode.php";
session_start();

// Simuler une base de données de justificatifs
$excuses = recupJustificatif($db,$_SESSION['id']);
// Filtrer les justificatifs par statut
$status = $_GET['status'] ?? 'attente';
// $filteredExcuses = array_filter($excuses, function($excuse) use ($status) {
//     return $excuse['statut'] === $status;
// });
if(isset($_GET['status'])){
    if($_GET['status']=="attente"){
        $excuses=recupJustificatifByStatut($db,"attente",$_SESSION['id']);
    }else if($_GET['status']=="rejete"){
        $excuses=recupJustificatifByStatut($db,"rejete",$_SESSION['id']);
    }else{
        $excuses=recupJustificatifByStatut($db,"valider",$_SESSION['id']);
    }
}
// Traitement du formulaire de création de justificatif
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_excuse'])) {
    $date=date("Y/m/d");
    $reason = $_POST['excuse_reason'] ?? '';
    $comment = $_POST['excuse_comment'] ?? '';
    $periodes=$_POST['periodes'];
    $chaine='';
    $date_A=$_POST['date_d_abscence'];
    foreach($periodes as $periode){
        $chaine=$chaine.",".$periode;
    }
    if (empty($date) || empty($reason)) {
        $message = '<div class="alert alert-error">Veuillez remplir tous les champs obligatoires.</div>';
    } else {
         // Dossier où seront stockées les images
        $dossier_upload = "uploads/";
    
    // Créer le dossier s'il n'existe pas
        if (!file_exists($dossier_upload)) {
        mkdir($dossier_upload, 0777, true);
         }
    
    $fichier = $_FILES["excuse_file"];
    
    // Vérifications sur le fichier
    if ($fichier["error"] === UPLOAD_ERR_OK) {
        $nom_fichier = basename($fichier["name"]);
        $type_fichier = $fichier["type"];
        $taille_fichier = $fichier["size"];
        $tmp_fichier = $fichier["tmp_name"];
        
        // Vérifier si c'est bien une image
        $extensions_valides = array("jpg", "jpeg", "png", "gif");
        $extension_upload = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
        
        if (in_array($extension_upload, $extensions_valides)) {
            // Générer un nom unique pour éviter les conflits
            $nom_unique = uniqid() . "_" . $nom_fichier;
            $chemin_destination = $dossier_upload . $nom_unique;
            
            // Déplacer le fichier du dossier temporaire vers le dossier de destination
            if (move_uploaded_file($tmp_fichier, $chemin_destination)) {
                // Insérer les informations dans la base de données
               insertJ($db,$_SESSION['id'],$date,$reason,$comment,'attente',$nom_unique,$chaine,$date_A);
               header("location:excuses.php");
            } else {
                echo "<div class='error'>Erreur lors du déplacement du fichier.</div>";
            }
        } else {
            echo "<div class='error'>Format de fichier non autorisé. Seuls les formats JPG, JPEG, PNG et GIF sont acceptés.</div>";
        }
    } else {
        echo "<div class='error'>Erreur lors de l'upload du fichier: " . $fichier["error"] . "</div>";
    }
    }
}
//uploader l image
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    // // Dossier où seront stockées les images
    // $dossier_upload = "uploads/";
    
    // // Vérifications sur le fichier
    // if ($fichier["error"] === UPLOAD_ERR_OK) {
    //     $nom_fichier = basename($fichier["name"]);
    //     $type_fichier = $fichier["type"];
    //     $taille_fichier = $fichier["size"];
    //     $tmp_fichier = $fichier["tmp_name"];
        
    //     // Vérifier si c'est bien une image
    //     $extensions_valides = array("jpg", "jpeg", "png", "gif");
    //     $extension_upload = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
        
    //     if (in_array($extension_upload, $extensions_valides)) {
    //         // Générer un nom unique pour éviter les conflits
    //         $nom_unique = uniqid() . "_" . $nom_fichier;
    //         $chemin_destination = $dossier_upload . $nom_unique;
            
    //         // Déplacer le fichier du dossier temporaire vers le dossier de destination
    //         if (move_uploaded_file($tmp_fichier, $chemin_destination)) {
    //             // Insérer les informations dans la base de données
    //            insertJ($db,$_SESSION['id'],$date,$comment,$status,$nom_fichier);
    //         } else {
    //             echo "<div class='error'>Erreur lors du déplacement du fichier.</div>";
    //         }
    //     } else {
    //         echo "<div class='error'>Format de fichier non autorisé. Seuls les formats JPG, JPEG, PNG et GIF sont acceptés.</div>";
    //     }
    // } else {
    //     echo "<div class='error'>Erreur lors de l'upload du fichier: " . $fichier["error"] . "</div>";
    // }   // // Créer le dossier s'il n'existe pas
    // if (!file_exists($dossier_upload)) {
    //     mkdir($dossier_upload, 0777, true);
    // }
    
// Fonction pour formater la date
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d F Y');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Justificatifs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-user-check"></i> EduPresence</h1>
            <nav>
                <div class="dropdown">
                    <button class="dropbtn"><i class="fas fa-bars"></i> Menu</button>
                    <div class="dropdown-content">
                        <a href="etudiant.php"><i class="fas fa-home"></i> Tableau de bord</a>
                        <a href="tes.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
                        <a href="excuses.php" class="active"><i class="fas fa-file-alt"></i> Gestion des justificatifs</a>
                        <a href="deconnexion.php" class="activ"><i class="fas fa-file-alt"></i> deconnexion</a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <?php if (!empty($message)): ?>
                <?php echo $message; ?>
            <?php endif; ?>

            <section class="excuses-section">
                <h2><i class="fas fa-file-alt"></i> Gestion des Justificatifs</h2>
                
                <div class="excuses-actions">
                    <button id="new-excuse-btn" class="btn-primary">
                        <i class="fas fa-plus"></i> Créer un nouveau justificatif
                    </button>
                </div>

                <div class="excuses-tabs">
                    <a href="?status=attente" class="tab-btn <?php echo $status === 'attente' ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> En attente
                    </a>
                    <a href="?status=valider" class="tab-btn <?php echo $status === 'valider' ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Validés
                    </a>
                    <a href="?status=rejete" class="tab-btn <?php echo $status === 'rejete' ? 'active' : ''; ?>">
                        <i class="fas fa-times-circle"></i> Rejetés
                    </a>
                </div>

                <div class="excuses-list" id="excuses-container">
                    <?php if (empty($excuses)): ?>
                        <p class="no-data">Aucun justificatif <?php echo $status === 'attente' ? 'attente' : ($status === 'valider' ? 'validé' : 'rejeté'); ?></p>
                    <?php else: ?>
                        <?php foreach ($excuses as $excuse): ?>
                            <div class="excuse-card">
                                <div class="excuse-header">
                                    <div class="excuse-date"><?php echo $excuse['date']; ?></div>
                                    <div class="excuse-status status-<?php echo $excuse['statut']; ?>">
                                        <?php if ($excuse['statut'] === 'attente'): ?>
                                            <i class="fas fa-clock"></i> En attente
                                        <?php elseif ($excuse['statut'] === 'valider'): ?>
                                            <i class="fas fa-check-circle"></i> Validé
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i> Rejeté
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="excuse-student"><?php echo htmlspecialchars($excuse['date']); ?></div>
                                <div class="excuse-details">
                                    <p><strong>Motif:</strong> <?php echo htmlspecialchars($excuse['motif']); ?></p>
                                    <p><strong>Commentaire:</strong> <?php echo htmlspecialchars($excuse['commentaire']); ?></p>
                                    <p><strong>Document:</strong> <a href=<?php echo"uploads/". htmlspecialchars($excuse['file']); ?> class="file-link"><?php echo"uploads/". htmlspecialchars($excuse['file']); ?></a></p>
                                </div>
                                <?php if ($excuse['statut'] === 'attente'||$excuse['statut'] === 'valider'||$excuse['statut'] === 'rejete'): ?>
                                    <div class="excuse-actions">
                                        <button class="btn-success view-btn">
                                            <i class="fas fa-eye"></i> Voir le document
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> EduPresence - Système de gestion des présences</p>
        </footer>
    </div>

    <!-- Modal pour créer un nouveau justificatif -->
    <div id="excuse-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-file-medical"></i> Nouveau Justificatif</h2>
            <form id="excuse-form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-field">
                    <label for="absence_date"><i class="fas fa-calendar-alt"></i> entrez la periode:</label>
                    <input name="date_d_abscence" id="absence_date" type="date">
                </div>
                <div class="form-field">
                    <label for="absence_date"><i class="fas fa-calendar-alt"></i> entrez la periode:</label>
                    <select name="periodes[]" id="absence_date" multiple>
                        <option value="8H-10H">8H-10H</option>
                        <option value="10H-12H">10H-12H</option>
                        <option value="13H-15H">13H-15H</option>
                        <option value="15H-17H">15H-17H</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="excuse_reason"><i class="fas fa-question-circle"></i> Motif:</label>
                    <select id="excuse_reason" name="excuse_reason" required>
                        <option value="">Sélectionner un motif</option>
                        <option value="medical">Raison médicale</option>
                        <option value="family">Raison familiale</option>
                        <option value="transport">Problème de transport</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="excuse_comment"><i class="fas fa-comment"></i> Commentaire:</label>
                    <textarea id="excuse_comment" name="excuse_comment" rows="4"></textarea>
                </div>
                <div class="form-field">
                    <label for="excuse_file"><i class="fas fa-file-upload"></i> Document justificatif:</label>
                    <div class="file-upload">
                        <input type="file" id="excuse_file" name="excuse_file">
                        <label for="excuse_file" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choisir un fichier
                        </label>
                        <span id="file-name">Aucun fichier sélectionné</span>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" id="cancel-btn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" name="submit_excuse" class="btn-submit">
                        <i class="fas fa-check"></i> Soumettre
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="notification" class="notification">
        <p id="notification-message"></p>
    </div>

    <script src="script.js"></script>
</body>
</html>