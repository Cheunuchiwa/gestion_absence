<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_abscence";
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer l'ID de l'étudiant depuis l'URL ou utiliser une valeur par défaut
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Fonction PHP pour calculer la durée en heures
function calculateHours($periode) {
    $times = explode("-", $periode);
    $start = intval(substr($times[0], 0, -1));
    $end = intval(substr($times[1], 0, -1));
    return $end - $start;
}

// Récupérer les informations de l'étudiant
$sql_student = "SELECT * FROM utilisateur WHERE id_etudiant = $student_id";
$result_student = $conn->query($sql_student);
$student = $result_student->fetch_assoc();

// Récupérer tous les étudiants pour le sélecteur
$sql_all_students = "SELECT id_etudiant, nom, prenom FROM utilisateur ORDER BY nom, prenom";
$result_all_students = $conn->query($sql_all_students);
$all_students = [];
while ($row = $result_all_students->fetch_assoc()) {
    $all_students[] = $row;
}

// Récupérer les absences de l'étudiant
// IMPORTANT: Nous n'utilisons plus la fonction MySQL calculateHours
$sql_absences = "SELECT a.* FROM abscences a WHERE a.id_etudiant = $student_id ORDER BY a.dates DESC, a.periode";
$result_absences = $conn->query($sql_absences);
$absences = [];
while ($row = $result_absences->fetch_assoc()) {
    // Calculer les heures avec la fonction PHP
    $row['hours'] = calculateHours($row['periode']);
    $absences[] = $row;
}

// Statistiques par matière
$sql_by_subject = "SELECT matiere, statut, COUNT(*) as count, periode FROM abscences WHERE id_etudiant = $student_id GROUP BY matiere, statut, periode";
$result_by_subject = $conn->query($sql_by_subject);

$absences_by_subject = [];
$total_hours_by_subject = [];
$justified_hours_by_subject = [];

if ($result_by_subject->num_rows > 0) {
    while($row = $result_by_subject->fetch_assoc()) {
        $subject = $row["matiere"];
        $status = $row["statut"];
        $count = $row["count"];
        // Utiliser la fonction PHP pour calculer les heures
        $hours = calculateHours($row["periode"]) * $count;
        
        if (!isset($total_hours_by_subject[$subject])) {
            $total_hours_by_subject[$subject] = 0;
            $justified_hours_by_subject[$subject] = 0;
        }
        
        $total_hours_by_subject[$subject] += $hours;
        
        if ($status == "justifier") {
            $justified_hours_by_subject[$subject] += $hours;
        }
        
        $absences_by_subject[] = [
            "matiere" => $subject,
            "statut" => $status,
            "count" => $count,
            "hours" => $hours
        ];
    }
}

// Statistiques par jour
$sql_by_date = "SELECT dates, COUNT(*) as count, statut, periode FROM abscences WHERE id_etudiant = $student_id GROUP BY dates, statut, periode ORDER BY dates";
$result_by_date = $conn->query($sql_by_date);

$absences_by_date = [];
$dates = [];

if ($result_by_date->num_rows > 0) {
    while($row = $result_by_date->fetch_assoc()) {
        $date = date('d/m/Y', strtotime($row["dates"]));
        $status = $row["statut"];
        $count = $row["count"];
        // Utiliser la fonction PHP pour calculer les heures
        $hours = calculateHours($row["periode"]) * $count;
        
        if (!in_array($date, $dates)) {
            $dates[] = $date;
        }
        
        if (!isset($absences_by_date[$date])) {
            $absences_by_date[$date] = [
                "total" => 0,
                "justified" => 0
            ];
        }
        
        $absences_by_date[$date]["total"] += $hours;
        
        if ($status == "justifier") {
            $absences_by_date[$date]["justified"] += $hours;
        }
    }
}

// Calcul des totaux
$total_absences = 0;
$total_justified = 0;

foreach ($total_hours_by_subject as $subject => $hours) {
    $total_absences += $hours;
    $total_justified += $justified_hours_by_subject[$subject];
}

$justified_percentage = ($total_absences > 0) ? round(($total_justified / $total_absences) * 100) : 0;

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Étudiant - <?php echo $student['prenom'] . ' ' . $student['nom']; ?></title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <style>
        :root {
  --primary-color: #4361ee;
  --secondary-color: #3f37c9;
  --success-color: #4cc9f0;
  --danger-color: #f72585;
  --warning-color: #f8961e;
  --light-color: #f8f9fa;
  --dark-color: #212529;
  --gray-color: #6c757d;
  --border-radius: 8px;
  --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
}

/* Reset et base */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: var(--dark-color);
  background-color: #f5f7fb;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* En-tête */
header {
  margin-bottom: 30px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

header h1 {
  color: var(--primary-color);
  font-size: 2.5rem;
  margin-bottom: 10px;
}

.student-selector {
  background-color: white;
  padding: 15px;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  width: 100%;
  max-width: 500px;
}

.student-selector select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  margin-top: 5px;
}

/* Profil étudiant */
.student-profile {
  margin-bottom: 30px;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 20px;
}

.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background-color: var(--primary-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: bold;
}

.profile-info h2 {
  color: var(--secondary-color);
  margin-bottom: 10px;
  border-bottom: none;
  padding-bottom: 0;
}

.profile-info p {
  margin-bottom: 5px;
}

/* Cartes */
.card {
  background-color: white;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  padding: 20px;
  margin-bottom: 20px;
  transition: var(--transition);
}

.card:hover {
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
  transform: translateY(-5px);
}

.card h2 {
  color: var(--secondary-color);
  margin-bottom: 20px;
  font-size: 1.5rem;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 10px;
}

/* Statistiques résumées */
.summary-stats {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  gap: 20px;
}

.stat-item {
  text-align: center;
  flex: 1;
  min-width: 150px;
  padding: 15px;
  border-radius: var(--border-radius);
  background-color: #f8f9fa;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-value {
  display: block;
  font-size: 2.5rem;
  font-weight: bold;
  color: var(--primary-color);
  margin-bottom: 5px;
}

.stat-label {
  color: var(--gray-color);
  font-size: 0.9rem;
}

/* Disposition des graphiques */
.stats-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.charts-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.chart-card {
  flex: 1;
  min-width: 300px;
}

/* Tableau */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

table th,
table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

table th {
  background-color: #f8f9fa;
  color: var(--secondary-color);
  font-weight: 600;
}

table tr:hover {
  background-color: #f5f5f5;
}

/* Statut des absences */
.status-badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.justifier {
  background-color: rgba(75, 192, 192, 0.2);
  color: rgb(40, 160, 160);
}

.status-badge.non_justifier {
  background-color: rgba(255, 99, 132, 0.2);
  color: rgb(255, 70, 110);
}

tr.justified {
  background-color: rgba(75, 192, 192, 0.05);
}

tr.unjustified {
  background-color: rgba(255, 99, 132, 0.05);
}

/* Barre de progression */
.progress-bar {
  width: 100%;
  height: 20px;
  background-color: #e9ecef;
  border-radius: 10px;
  position: relative;
  overflow: hidden;
}

.progress {
  height: 100%;
  background-color: var(--success-color);
  border-radius: 10px;
  transition: width 0.5s ease;
}

.progress-bar span {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: var(--dark-color);
  font-size: 0.8rem;
  font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
  .charts-row {
    flex-direction: column;
  }

  .chart-card {
    width: 100%;
  }

  .summary-stats {
    flex-direction: column;
  }

  .stat-item {
    width: 100%;
  }

  table {
    font-size: 0.9rem;
  }

  table th,
  table td {
    padding: 8px 10px;
  }

  .profile-header {
    flex-direction: column;
    text-align: center;
  }
}

    </style>
    <div class="container">
        <header>
            <h1>Détails des Absences de l'Étudiant</h1>
            <div class="student-selector">
                <form action="" method="get">
                    <label for="student-select">Sélectionner un étudiant:</label>
                    <select id="student-select" name="id" onchange="this.form.submit()">
        
                    </select>
                </form>
            </div>
        </header>
        
        <div class="student-profile card">
            <div class="profile-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($student['prenom'], 0, 1) . substr($student['nom'], 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo $student['prenom'] . ' ' . $student['nom']; ?></h2>
                    <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
                    <p><strong>Filière:</strong> <?php echo $student['filiere']; ?></p>
                    <p><strong>Niveau:</strong> <?php echo $student['niveau']; ?></p>
                    <p><strong>Classe:</strong> <?php echo $student['classe']; ?></p>
                    <p><strong>Rôle:</strong> <?php echo $student['role']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="card summary-card">
                <h2>Résumé des Absences</h2>
                <div class="summary-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $total_absences; ?></span>
                        <span class="stat-label">Heures d'absences totales</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $total_justified; ?></span>
                        <span class="stat-label">Heures justifiées</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $justified_percentage; ?>%</span>
                        <span class="stat-label">Taux de justification</span>
                    </div>
                </div>
            </div>
            
            <div class="charts-row">
                <div class="card chart-card">
                    <h2>Absences par Matière</h2>
                    <canvas id="subjectChart"></canvas>
                </div>
                
                <div class="card chart-card">
                    <h2>Taux de Justification par Matière</h2>
                    <canvas id="justificationChart"></canvas>
                </div>
            </div>
            
            <div class="card chart-card">
                <h2>Évolution des Absences</h2>
                <canvas id="timelineChart"></canvas>
            </div>
            
            <div class="card table-card">
                <h2>Détails par Matière</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Heures d'absences</th>
                            <th>Heures justifiées</th>
                            <th>Taux de justification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($total_hours_by_subject as $subject => $hours): ?>
                            <?php 
                            $justified = $justified_hours_by_subject[$subject];
                            $percentage = ($hours > 0) ? round(($justified / $hours) * 100) : 0;
                            ?>
                            <tr>
                                <td><?php echo $subject; ?></td>
                                <td><?php echo $hours; ?> heures</td>
                                <td><?php echo $justified; ?> heures</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
                                        <span><?php echo $percentage; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card table-card">
                <h2>Liste des Absences</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Période</th>
                            <th>Matière</th>
                            <th>Statut</th>
                            <th>Heures</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absences as $absence): ?>
                            <tr class="<?php echo ($absence['statut'] == 'justifier') ? 'justified' : 'unjustified'; ?>">
                                <td><?php echo date('d/m/Y', strtotime($absence['dates'])); ?></td>
                                <td><?php echo $absence['periode']; ?></td>
                                <td><?php echo $absence['matiere']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $absence['statut']; ?>">
                                        <?php echo ($absence['statut'] == 'justifier') ? 'Justifiée' : 'Non justifiée'; ?>
                                    </span>
                                </td>
                                <td><?php echo $absence['hours']; ?> h</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    // Données pour les graphiques
    const subjectData = {
        labels: [<?php 
            $labels = array_keys($total_hours_by_subject);
            echo '"' . implode('", "', $labels) . '"';
        ?>],
        datasets: [
            {
                label: 'Heures justifiées',
                data: [<?php 
                    $justified_data = [];
                    foreach ($labels as $subject) {
                        $justified_data[] = $justified_hours_by_subject[$subject];
                    }
                    echo implode(', ', $justified_data);
                ?>],
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            {
                label: 'Heures non justifiées',
                data: [<?php 
                    $unjustified_data = [];
                    foreach ($labels as $subject) {
                        $unjustified_data[] = $total_hours_by_subject[$subject] - $justified_hours_by_subject[$subject];
                    }
                    echo implode(', ', $unjustified_data);
                ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ]
    };

    const justificationData = {
        labels: [<?php 
            echo '"' . implode('", "', $labels) . '"';
        ?>],
        datasets: [{
            label: 'Taux de justification (%)',
            data: [<?php 
                $percentage_data = [];
                foreach ($labels as $subject) {
                    $hours = $total_hours_by_subject[$subject];
                    $justified = $justified_hours_by_subject[$subject];
                    $percentage_data[] = ($hours > 0) ? round(($justified / $hours) * 100) : 0;
                }
                echo implode(', ', $percentage_data);
            ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    };

    const timelineData = {
        labels: [<?php echo '"' . implode('", "', $dates) . '"'; ?>],
        datasets: [
            {
                label: 'Heures totales',
                data: [<?php 
                    $total_data = [];
                    foreach ($dates as $date) {
                        $total_data[] = $absences_by_date[$date]['total'];
                    }
                    echo implode(', ', $total_data);
                ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: true
            },
            {
                label: 'Heures justifiées',
                data: [<?php 
                    $justified_data = [];
                    foreach ($dates as $date) {
                        $justified_data[] = $absences_by_date[$date]['justified'];
                    }
                    echo implode(', ', $justified_data);
                ?>],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: true
            }
        ]
    };

    // Initialiser les graphiques
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des absences par matière
        const subjectCtx = document.getElementById('subjectChart').getContext('2d');
        new Chart(subjectCtx, {
            type: 'bar',
            data: subjectData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Heures d\'absences'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Heures d\'absences par matière'
                    }
                }
            }
        });

        // Graphique du taux de justification
        const justificationCtx = document.getElementById('justificationChart').getContext('2d');
        new Chart(justificationCtx, {
            type: 'doughnut',
            data: justificationData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Taux de justification par matière (%)'
                    }
                }
            }
        });

        // Graphique de l'évolution des absences
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'line',
            data: timelineData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Heures d\'absences'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des absences dans le temps'
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
