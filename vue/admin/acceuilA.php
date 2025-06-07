<?php
 require "../../modele/methode.php";
 session_start();
 require 'vendor/autoload.php';
 use Dompdf\Dompdf;
 use Dompdf\Options;
 use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
 $dompdf = new Dompdf();
 $utilisateurs=recupUtilByFiliere($db,$_SESSION['filiere']);
 $nbreEtudiants=countE($db);
 $nbreHeureAscence=countH($db);
 $date = date('d/m/Y H:i:s');
 function sendVerificationCode($email,$absencesTotales,$absencesJustifiees,$absencesFinales) {
    $mail = new PHPMailer(true);
    $ht = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport d\'absences</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #4a86e8;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff;
            border-left: 4px solid #4a86e8;
        }
        .warning {
            color: #e53935;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rapport d\'Absences</h2>
        <p>Date du rapport: ' . date('y-m-d') . '</p>
    </div>
    
    <div class="content">
        <p>Bonjour,</p>
        
        <p>Veuillez trouver ci-dessous le récapitulatif des absences enregistrées:</p>
        
        <div class="summary">
            <table>
                <tr>
                    <th>Type d\'absence</th>
                    <th>Heures</th>
                </tr>
                <tr>
                    <td>Absences totales</td>
                    <td>' . $absencesTotales . ' heures</td>
                </tr>
                <tr>
                    <td>Absences justifiées</td>
                    <td>' . $absencesJustifiees . ' heures</td>
                </tr>
                <tr>
                    <td>Absences non justifiées</td>
                    <td>' . $absencesFinales . ' heures</td>
                </tr>
            </table>
        </div>
        
        ' . ($absencesFinales > 0 ? '<p class="warning">Attention: ' . $absencesFinales . ' heures d\'absences ne sont pas justifiées.</p>' : '<p>Toutes les absences sont justifiées.</p>') . '
        
        <p>Pour toute question concernant ce rapport, n\'hésitez pas à contacter le service des ressources humaines.</p>
        
        <p>Cordialement,<br>
        Le Service de Gestion des Absences</p>
    </div>
    
    <div class="footer">
        <p>Ce rapport est généré automatiquement. Merci de ne pas répondre à cet e-mail.</p>
    </div>
</body>
</html>
';

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
        $mail->Body    = $ht;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

 $filiere=$_SESSION['filiere'];
    if(isset($_POST['send'])){
        foreach($utilisateurs as $utilisateur){
             $count=recupHeure($db,$utilisateur['id']);
            $countJ=recupHeureJ($db,$utilisateur['id']);
            $countT=$count['count']-$countJ['count'];
            sendVerificationCode( $utilisateur['email'],$count['count'],$countJ['count'],$countT);
            header('location:acceuilA.php');
        }
    }
 $html= $html = '
 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="UTF-8">
     <title>Dashboard Utilisateurs</title>
     <style>
         body {
             font-family: "DejaVu Sans", sans-serif;
             margin: 0;
             padding: 0;
             color: #333;
         }
         .dashboard {
             width: 100%;
         }
         .header {
             background-color:rgb(1, 70, 243);
             color: white;
             padding: 20px;
             text-align: center;
             margin-bottom: 20px;
         }
         .date {
             font-size: 12px;
             text-align: right;
             margin-bottom: 10px;
             padding-right: 20px;
         }
         .content {
             padding: 0 20px;
         }
         .card {
             border: 1px solid #ddd;
             border-radius: 8px;
             padding: 15px;
             margin-bottom: 15px;
             background-color: #f9f9f9;
         }
         .card-header {
             border-bottom: 1px solid #eee;
             padding-bottom: 10px;
             margin-bottom: 10px;
             font-weight: bold;
             color:rgb(68, 15, 227);
         }
         .user-table {
             width: 100%;
             border-collapse: collapse;
             margin-top: 20px;
         }
         .user-table th {
             background-color:rgb(69, 11, 215);
             color: white;
             text-align: left;
             padding: 10px;
         }
         .user-table td {
             border: 1px solid #ddd;
             padding: 8px;
         }
         .user-table tr:nth-child(even) {
             background-color: #f2f2f2;
         }
         .footer {
             text-align: center;
             margin-top: 30px;
             font-size: 12px;
             color: #777;
             border-top: 1px solid #eee;
             padding-top: 10px;
         }
         .stats {
             display: flex;
             justify-content: space-between;
             margin-bottom: 20px;
         }
         .stat-box {
             width: 30%;
             background-color: #f0f4ff;
             border-radius: 8px;
             padding: 15px;
             text-align: center;
             box-shadow: 0 2px 4px rgba(0,0,0,0.1);
         }
         .stat-number {
             font-size: 24px;
             font-weight: bold;
             color:rgb(43, 15, 223);
         }
         .stat-label {
             font-size: 14px;
             color: #666;
         }
     </style>
 </head>
 <body>
     <div class="dashboard">
         <div class="header">
             <h1>Dashboard Utilisateurs</h1>
         </div>
         
         <div class="date">
             Généré le: ' . $date . ' pour la filiere '.$filiere.''.$utilisateurs[0]['classe'].'
         </div>
         
         <div class="content">
             <div class="stats">
                 <div class="stat-box">
                     <div class="stat-number">' . count($utilisateurs) . '</div>
                     <div class="stat-label">Utilisateurs</div>
                 </div>
                 <div class="stat-box">
                     <div class="stat-number">' . date('Y') . '</div>
                     <div class="stat-label">Année</div>
                 </div>
                 <div class="stat-box">
                     <div class="stat-number">100%</div>
                     <div class="stat-label">Complété</div>
                 </div>
             </div>
             
             <div class="card">
                 <div class="card-header">Liste des Utilisateurs</div>
                 <table class="user-table">
                     <thead>
                         <tr>
                             
                             <th>Nom</th>
                             <th>Prénom</th>
                             <th>classe</th>
                             <th>Heure totale</th>
                             <th>Heure justifiée</th>
                             <th>heure finale</th>
                         </tr>
                     </thead>
                     <tbody>';
 
 // Ajouter chaque utilisateur au tableau
        foreach ($utilisateurs as $utilisateur) {
                            $count=recupHeure($db,$utilisateur['id']);
                            $countJ=recupHeureJ($db,$utilisateur['id']);
                            $countT=$count['count']-$countJ['count'];
     $html .= '
                         <tr>
                             <td>' . htmlspecialchars($utilisateur['nom']) . '</td>
                             <td>' . htmlspecialchars($utilisateur['prenom']) . '</td>
                               <td>' . $utilisateur['classe'] . '</td>
                              <td> '.$count['count'].'</td>
                            <td> '.$countJ['count'].'</td>
                            <td> '.$countT.'</td>
                         </tr>';
 }
 
 $html .= '
                     </tbody>
                 </table>
             </div>
         </div>
         
         <div class="footer">
             <p>Ce document est généré automatiquement. © ' . date('Y') . 'INSTITUT UNIVERSITAIRE LA COTE</p>
         </div>
     </div>
 </body>
 </html>';
 if(isset($_POST['detaille'])){
    header('location:statistique.php');
 }

    if(isset($_POST['sbt'])){
        $utilisateurs=filtrerUtilisateur($db,$_POST['classe'],$_POST['niveau'],$_SESSION['filiere']); 
        //header('location:acceuilA.php');
        //echo"".$utilisateur[0]['nom'];
    }
    if(isset($_POST['imprimer'])){
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("ficheAppel".$_SESSION['filiere'].".pdf", array("Attachment" => false));
        //header("location:acceuilA.php");
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Gestion des Absences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --green: #28a745;
            --light-green: #e3f7e9;
            --red: #dc3545;
            --light-red: #fbecec;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: var(--dark-gray);
        }
        
        .container {
            display: flex;
            height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--dark-gray);
            color: white;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .sidebar-header h2 i {
            margin-right: 10px;
            color: var(--green);
        }
        
        .sidebar-menu {
            flex: 1;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu a.active {
            background-color: var(--green);
            border-radius: 0 30px 30px 0;
        }
        
        .sidebar-menu i {
            width: 30px;
            font-size: 1.1rem;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header-title h1 {
            font-size: 1.8rem;
            color: var(--dark-gray);
        }
        
        .header-title p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
        }
        
        .header-actions .search-box {
            position: relative;
            margin-right: 15px;
        }
        
        .header-actions .search-box input {
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 8px 15px 8px 35px;
            width: 250px;
            outline: none;
        }
        
        .header-actions .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }
        
        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .stat-card-header h3 {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
        }
        
        .green-icon {
            background-color: var(--light-green);
            color: var(--green);
        }
        
        .red-icon {
            background-color: var(--light-red);
            color: var(--red);
        }
        
        .gray-icon {
            background-color: var(--light-gray);
            color: var(--gray);
        }
        
        .stat-card-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-card-change {
            font-size: 0.8rem;
        }
        
        .positive-change {
            color: var(--green);
        }
        
        .negative-change {
            color: var(--red);
        }
        
        .filter-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .filter-container h2 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--dark-gray);
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .form-group select,
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        
        button.btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-green {
            background-color: var(--green);
            color: white;
            border-radius:12px;
            height:42px;
            padding-top:8px;;
            padding-left:45px;
        }
        
        .btn-green:hover {
            background-color: #218838;
        }
        
        .table-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .table-header h2 {
            font-size: 1.2rem;
            color: var(--dark-gray);
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #ddd;
            color: var(--gray);
        }
        
        .btn-outline:hover {
            background-color: #f5f5f5;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead th {
            text-align: left;
            padding: 15px;
            background-color: var(--light-gray);
            color: var(--dark-gray);
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }
        
        tbody td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tbody tr:hover {
            background-color: #f9f9f9;
        }
        
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
        
        .badge-gray {
            background-color: var(--light-gray);
            color: var(--gray);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-view {
            background-color: var(--light-green);
            color: var(--green);
        }
        
        .btn-edit {
            background-color: var(--light-gray);
            color: var(--gray);
        }
        
        .btn-delete {
            background-color: var(--light-red);
            color: var(--red);
        }
        
        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .pagination-item {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ddd;
            margin-right: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .pagination-item:hover,
        .pagination-item.active {
            background-color: var(--green);
            color: white;
            border-color: var(--green);
        }
        
        .pagination-item i {
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .filter-form {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                padding: 10px 0;
            }
            
            .sidebar-menu a {
                padding: 8px 20px;
            }
            
            .main-content {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                margin-top: 15px;
                width: 100%;
            }
            
            .header-actions .search-box {
                flex: 1;
            }
            
            .header-actions .search-box input {
                width: 100%;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-graduation-cap"></i> EduAdmin</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="etudiant.php"><i class="fas fa-user-graduate"></i> Étudiants</a></li>
                    <li><a href="justificatif.php"><i class="fas fa-chalkboard-teacher"></i> justificatifs</a></li>
                    <li><a href="statistique.php"><i class="fas fa-chart-bar"></i> statistique</a></li>
                    <li><a href="historique.php"><i class="fas fa-calendar-alt"></i>historique</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
                </ul>
            </div>
            <div class="sidebar-footer">
                <a href="#" style="color: white; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">
                    <h1>Dashboard Justificatifs</h1>
                    <p>Bienvenue dans votre espace administrateur</p>
                </div>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher un étudiant...">
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Total Étudiants</h3>
                        <div class="stat-card-icon green-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo $nbreEtudiants ?></div>
                    <div class="stat-card-change positive-change">
                        <i class="fas fa-arrow-up"></i> +5.3% depuis le mois dernier
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Heures d'absence</h3>
                        <div class="stat-card-icon red-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo $nbreHeureAscence['count']*2?></div>
                    <div class="stat-card-change negative-change">
                        <i class="fas fa-arrow-up"></i> +12.7% depuis le mois dernier
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Absences justifiées</h3>
                        <div class="stat-card-icon gray-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">1,287</div>
                    <div class="stat-card-change positive-change">
                        <i class="fas fa-arrow-up"></i> +3.2% depuis le mois dernier
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Taux d'assiduité</h3>
                        <div class="stat-card-icon green-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">85.7%</div>
                    <div class="stat-card-change positive-change">
                        <i class="fas fa-arrow-up"></i> +1.4% depuis le mois dernier
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filter-container">
                <h2>Filtres</h2>
                <form method="POST">
                <div class="filter-form">
                    <div class="form-group">
                        <label for="classe">Classe</label>
                        <select id="classe" name="classe">
                            <option value="">Toutes les classes</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="niveau">Niveau</label>
                        <select id="niveau" name="niveau">
                            <option value="">Tous les niveaux</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date">
                    </div>
                    <div class="form-group" style="justify-content: flex-end;">
                        <button class="btn btn-green" style="margin-top: 24px;" name="sbt">Appliquer les filtres</a>
                    </div>
                </div>
            </form>
            </div>
            
            <!-- Data Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Liste des étudiants</h2>
                    <div class="table-actions">
                        <form action="" method="post">
                        <button class="btn btn-outline" name="imprimer"><i class="fas fa-print"></i> Imprimer</button>
                        <button class="btn btn-outline" name="detaille"><i class="fas fa-chart-bar"></i>detaille</button>
                        <button class="btn btn-outline" name="send">envoyer</button>
                        </form>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Classe</th>
                            <th>Heures totales</th>
                            <th>Heures justifiées</th>
                            <th>Heures finales</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($utilisateurs as $utilisateur){?>
                        <tr>
                            <td><?php 
                            $count=recupHeure($db,$utilisateur['id']);
                            $countJ=recupHeureJ($db,$utilisateur['id']);
                            $countT=$count['count']-$countJ['count'];
                            echo $utilisateur['nom']?>
                        
                        </td>
                            <td><?php echo $utilisateur['prenom']?></td>
                            <td><?php echo $utilisateur['classe']?></td>
                            <td><?php echo $count['count']*2 ?></td>
                            <td><?php echo $countJ['count']*2?></td>
                            <td><?php echo $countT*2 ?></td>
                            <td><?php if($countT==0){?><span class="badge badge-green">Régulier</span><?php }else{ ?><span class="badge badge-red">Irrégulier<?php }?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detaille.php?id=<?php echo $utilisateur['id'] ?>" class="action-btn btn-view"><i class="fas fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td>Martin</td>
                            <td>Sophie</td>
                            <td>L3 Droit</td>a
                            <td>32</td>
                            <td>12</td>
                            <td>20</td>
                            <td><span class="badge badge-red">Irrégulier</span></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="action-btn btn-view"><i class="fas fa-eye"></i></div>
                                    <div class="action-btn btn-edit"><i class="fas fa-edit"></i></div>
                                    <div class="action-btn btn-delete"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr> -->
                        <?php }?>
                       
                    </tbody>
                </table>
                <div class="pagination">
                    <div class="pagination-item"><i class="fas fa-chevron-left"></i></div>
                    <div class="pagination-item active">1</div>
                    <div class="pagination-item">2</div>
                    <div class="pagination-item">3</div>
                    <div class="pagination-item">4</div>
                    <div class="pagination-item"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript pour la fonctionnalité du dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour filtrer les données
            function applyFilters() {
                const classe = document.getElementById('classe').value;
                const niveau = document.getElementById('niveau').value;
                const date = document.getElementById('date').value;
                
                // Dans une application réelle, on enverrait ces données au serveur via AJAX
                console.log('Filtres appliqués:', { classe, niveau, date });
                
                // Simulation d'une requête AJAX
                // En production, vous utiliseriez fetch() ou axios pour appeler votre API
                
                // Exemple de code fetch qui serait utilisé en production:
                /*
                fetch('api/students/filter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ classe, niveau, date }),
                })
                .then(response => response.json())
                .then(data => {
                    updateTable(data);
                })
                .catch(error => console.error('Erreur:', error));
                */
                
                // Simulons un effet de chargement
                const tableContainer = document.querySelector('.table-container');
                tableContainer.style.opacity = '0.5';
                
                setTimeout(() => {
                    tableContainer.style.opacity = '1';
                    // Ici, on mettrait à jour le tableau avec les données filtrées
                }, 500);
            }
            
            // Fonction pour mettre à jour le tableau (serait utilisée avec les données réelles)
            function updateTable(data) {
                const tbody = document.querySelector('tbody');
                tbody.innerHTML = '';
                
                data.forEach(student => {
                    const row = document.createElement('tr');
                    
                    // Construction de la ligne du tableau avec les données de l'étudiant
                    // ...
                    
                    tbody.appendChild(row);
                });
            }
            
            // Ajout des événements
            const filterButton = document.querySelector('.btn-green');
            filterButton.addEventListener('click', applyFilters);
            
            // Gestion des boutons d'action
            const actionButtons = document.querySelectorAll('.action-btn');
            actionButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const action = this.classList.contains('btn-view') ? 'view' : 
                                 this.classList.contains('btn-edit') ? 'edit' : 'delete';
                    
                    const row = this.closest('tr');
                    const name = row.cells[0].textContent;
                    const firstName = row.cells[1].textContent;
                    
                    console.log(`Action ${action} sur l'étudiant ${firstName} ${name}`);
                    
                    // Dans une application réelle, vous redirigeriez vers la page appropriée
                    // ou afficheriez une modal pour éditer/voir/supprimer
                    if (action === 'delete') {
                        if (confirm(`Êtes-vous sûr de vouloir supprimer l'étudiant ${firstName} ${name} ?`)) {
                            // Simulation de suppression
                            row.style.opacity = '0.2';
                            setTimeout(() => row.remove(), 500);
                        }
                    }
                });
            });
            
            // Gestion de la pagination
            const paginationItems = document.querySelectorAll('.pagination-item');
            paginationItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Retirer la classe active de tous les éléments
                    paginationItems.forEach(i => i.classList.remove('active'));
                    // Ajouter la classe active à l'élément cliqué
                    if (!this.classList.contains('active') && 
                        !this.querySelector('i.fa-chevron-left') && 
                        !this.querySelector('i.fa-chevron-right')) {
                        this.classList.add('active');
                    }
                    
                    // Dans une application réelle, vous chargeriez la page correspondante
                    console.log('Page sélectionnée:', this.textContent || 'navigation');
                });
            });
        });
        
 </script>
</body>
