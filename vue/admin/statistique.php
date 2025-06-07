<?php
// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_abscence";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Fonction pour calculer la durée en heures
function calculateHours($periode) {
    $times = explode("-", $periode);
    $start = intval(substr($times[0], 0, -1));
    $end = intval(substr($times[1], 0, -1));
    return $end - $start;
}

// Requête pour obtenir les absences par matière
$sql_by_subject = "SELECT matiere, statut, COUNT(*) as count, periode FROM abscences GROUP BY matiere, statut";
$result_by_subject = $conn->query($sql_by_subject);

$absences_by_subject = [];
$total_hours_by_subject = [];
$justified_hours_by_subject = [];

if ($result_by_subject->num_rows > 0) {
    while($row = $result_by_subject->fetch_assoc()) {
        $subject = $row["matiere"];
        $status = $row["statut"];
        $count = $row["count"];
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

// Requête pour obtenir les absences par étudiant
$sql_by_student = "SELECT a.id_etudiant, COUNT(*) as count, a.statut, a.periode 
                  FROM abscences a
                  GROUP BY a.id_etudiant, a.statut";
$result_by_student = $conn->query($sql_by_student);

$absences_by_student = [];

if ($result_by_student->num_rows > 0) {
    while($row = $result_by_student->fetch_assoc()) {
        $student_id = $row["id_etudiant"];
        $status = $row["statut"];
        $count = $row["count"];
        $hours = calculateHours($row["periode"]) * $count;
        
        if (!isset($absences_by_student[$student_id])) {
            $absences_by_student[$student_id] = [
                "total" => 0,
                "justified" => 0
            ];
        }
        
        $absences_by_student[$student_id]["total"] += $hours;
        
        if ($status == "justifier") {
            $absences_by_student[$student_id]["justified"] += $hours;
        }
    }
}

// Fermer la connexion
$conn->close();
if(isset($_POST['back'])) {
    header('Location:acceuilA.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques d'Absences</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <style>
        /* Variables */
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
}
  button {
            padding: 20px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            background-color: var(--primary-color);
            color: white;
            left: 20px;
            position: absolute;
            
        }

header h1 {
  color: var(--primary-color);
  font-size: 2.5rem;
  margin-bottom: 10px;
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
}

    </style>
    <div class="container">
        <header>
            <form action="" method="POST">
            <button name="back" class="fas fa-arrow-left">back</button>
            </form>
            <h1>Tableau de Bord des Statistiques d'Absences</h1>
        </header>
            <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-graduation-cap"></i> EduAdmin</h2>
            </div>
        
        <div class="stats-container">
            <div class="card summary-card">
                <h2>Résumé des Absences</h2>
                <div class="summary-stats">
                    <?php
                    $total_absences = 0;
                    $total_justified = 0;
                    
                    foreach ($total_hours_by_subject as $subject => $hours) {
                        $total_absences += $hours;
                        $total_justified += $justified_hours_by_subject[$subject];
                    }
                    
                    $justified_percentage = ($total_absences > 0) ? round(($total_justified / $total_absences) * 100) : 0;
                    ?>
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
            
            <div class="card chart-card">
                <h2>Répartition des Absences par Jour</h2>
                <canvas id="dailyChart"></canvas>
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

        // Graphique des absences par jour
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: ['07/05/2025', '08/05/2025', '09/05/2025', '10/05/2025'],
                datasets: [{
                    label: 'Nombre d\'heures d\'absences',
                    data: [6, 0, 30, 0],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
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
                        text: 'Évolution des absences par jour'
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
