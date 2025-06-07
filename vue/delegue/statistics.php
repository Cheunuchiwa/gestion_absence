<?php
// Simuler des données de statistiques
$presenceRate = 85;
$totalAbsences = 42;
$justifiedAbsences = 28;

// Données pour les graphiques
$weeklyData = [85, 78, 92, 65, 88];
$weekDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

$subjectData = [
    'labels' => ['Mathématiques', 'Physique', 'Informatique', 'Anglais', 'Histoire'],
    'absences' => [15, 8, 22, 12, 18],
    'colors' => ['#4caf50', '#4caf50', '#c62828', '#4caf50', '#ff9800']
];

$monthlyTrend = [
    'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
    'presence' => [82, 84, 80, 85, 88, 90],
    'absences' => [18, 16, 20, 15, 12, 10]
];

// Données des étudiants avec le plus d'absences
$absenceStats = [
    ['name' => 'Dupont', 'firstName' => 'Jean', 'absences' => 8, 'justified' => 5],
    ['name' => 'Martin', 'firstName' => 'Marie', 'absences' => 6, 'justified' => 4],
    ['name' => 'Durand', 'firstName' => 'Pierre', 'absences' => 12, 'justified' => 3],
    ['name' => 'Lefebvre', 'firstName' => 'Sophie', 'absences' => 4, 'justified' => 4],
    ['name' => 'Moreau', 'firstName' => 'Thomas', 'absences' => 10, 'justified' => 7]
];

// Trier par nombre d'absences décroissant
usort($absenceStats, function($a, $b) {
    return $b['absences'] - $a['absences'];
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de Présence</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js pour les graphiques animés -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- CountUp.js pour les compteurs animés -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.8/countUp.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-user-check"></i> EduPresence</h1>
            <nav>
                <div class="dropdown">
                    <button class="dropbtn"><i class="fas fa-bars"></i> Menu</button>
                    <div class="dropdown-content">
                        <a href="acceuilD.php"><i class="fas fa-home"></i> Tableau de bord</a>
                        <a href="statistics.php" class="active"><i class="fas fa-chart-bar"></i> Statistiques</a>
                        <a href="excuses.php"><i class="fas fa-file-alt"></i> Gestion des justificatifs</a>
                        <a href="deconnexion.php" class="activ"><i class="fas fa-file-alt"></i> deconnexion</a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <section class="statistics-section">
                <h2><i class="fas fa-chart-line"></i> Statistiques de Présence</h2>
                
                <div class="filters">
                    <div class="form-field">
                        <label for="stat-period"><i class="fas fa-calendar"></i> Période:</label>
                        <select id="stat-period">
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                            <option value="semester">Ce semestre</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="stat-subject"><i class="fas fa-book"></i> Matière:</label>
                        <select id="stat-subject">
                            <option value="all">Toutes les matières</option>
                            <option value="math">Mathématiques</option>
                            <option value="physics">Physique</option>
                            <option value="cs">Informatique</option>
                            <option value="english">Anglais</option>
                            <option value="history">Histoire</option>
                        </select>
                    </div>
                </div>

                <div class="stats-cards">
                    <div class="stat-card animate-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Taux de présence</h3>
                            <div class="stat-value">
                                <span id="presence-rate">0</span>%
                            </div>
                            <div class="stat-progress">
                                <div class="progress-bar" style="width: <?php echo $presenceRate; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card animate-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Absences totales</h3>
                            <div class="stat-value" id="total-absences">0</div>
                            <div class="stat-trend">
                                <i class="fas fa-arrow-down"></i> 5% depuis la semaine dernière
                            </div>
                        </div>
                    </div>
                    <div class="stat-card animate-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Absences justifiées</h3>
                            <div class="stat-value" id="justified-absences">0</div>
                            <div class="stat-percentage">
                                <span id="justified-percentage">0</span>% des absences
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-container animate-card">
                    <h3><i class="fas fa-chart-line"></i> Évolution des présences</h3>
                    <div class="chart-wrapper">
                        <canvas id="attendance-chart"></canvas>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-container animate-card">
                        <h3><i class="fas fa-chart-pie"></i> Absences par matière</h3>
                        <div class="chart-wrapper">
                            <canvas id="subjects-chart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container animate-card">
                        <h3><i class="fas fa-chart-area"></i> Tendance mensuelle</h3>
                        <div class="chart-wrapper">
                            <canvas id="monthly-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="student-stats animate-card">
                    <h3><i class="fas fa-user-times"></i> Étudiants avec le plus d'absences</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Absences</th>
                                <th>Justifiées</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        <tbody id="absence-stats-body">
                            <?php foreach ($absenceStats as $stat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['name']); ?></td>
                                <td><?php echo htmlspecialchars($stat['firstName']); ?></td>
                                <td><?php echo $stat['absences']; ?></td>
                                <td><?php echo $stat['justified']; ?></td>
                                <td>
                                    <div class="mini-progress">
                                        <div class="mini-progress-bar" style="width: <?php echo ($stat['justified'] / $stat['absences']) * 100; ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> EduPresence - Système de gestion des présences</p>
        </footer>
    </div>

    <script>
        // Données pour les graphiques
        const weeklyData = <?php echo json_encode($weeklyData); ?>;
        const weekDays = <?php echo json_encode($weekDays); ?>;
        const subjectData = <?php echo json_encode($subjectData); ?>;
        const monthlyTrend = <?php echo json_encode($monthlyTrend); ?>;
        const presenceRate = <?php echo $presenceRate; ?>;
        const totalAbsences = <?php echo $totalAbsences; ?>;
        const justifiedAbsences = <?php echo $justifiedAbsences; ?>;
        const justifiedPercentage = Math.round((justifiedAbsences / totalAbsences) * 100);

        // Fonction pour animer les entrées lors du défilement
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            elements.forEach(element => {
                observer.observe(element);
            });
        }

        // Initialiser les compteurs animés
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des compteurs
            const presenceRateCounter = new CountUp('presence-rate', 0, presenceRate, 0, 2, {
                useEasing: true,
                useGrouping: true,
                separator: ' '
            });
            
            const totalAbsencesCounter = new CountUp('total-absences', 0, totalAbsences, 0, 2, {
                useEasing: true,
                useGrouping: true,
                separator: ' '
            });
            
            const justifiedAbsencesCounter = new CountUp('justified-absences', 0, justifiedAbsences, 0, 2, {
                useEasing: true,
                useGrouping: true,
                separator: ' '
            });
            
            const justifiedPercentageCounter = new CountUp('justified-percentage', 0, justifiedPercentage, 0, 2, {
                useEasing: true,
                useGrouping: true,
                separator: ' '
            });
            
            presenceRateCounter.start();
            totalAbsencesCounter.start();
            justifiedAbsencesCounter.start();
            justifiedPercentageCounter.start();
            
            // Graphique d'évolution des présences
            const attendanceCtx = document.getElementById('attendance-chart').getContext('2d');
            const attendanceChart = new Chart(attendanceCtx, {
                type: 'bar',
                data: {
                    labels: weekDays,
                    datasets: [{
                        label: 'Taux de présence (%)',
                        data: weeklyData,
                        backgroundColor: weeklyData.map(value => value < 70 ? '#c62828' : value < 80 ? '#ff9800' : '#4caf50'),
                        borderColor: weeklyData.map(value => value < 70 ? '#b71c1c' : value < 80 ? '#f57c00' : '#2e7d32'),
                        borderWidth: 1,
                        borderRadius: 5,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.2)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // Graphique des absences par matière
            const subjectsCtx = document.getElementById('subjects-chart').getContext('2d');
            const subjectsChart = new Chart(subjectsCtx, {
                type: 'doughnut',
                data: {
                    labels: subjectData.labels,
                    datasets: [{
                        data: subjectData.absences,
                        backgroundColor: subjectData.colors,
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });
            
            // Graphique de tendance mensuelle
            const monthlyCtx = document.getElementById('monthly-chart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyTrend.labels,
                    datasets: [
                        {
                            label: 'Présences (%)',
                            data: monthlyTrend.presence,
                            backgroundColor: 'rgba(76, 175, 80, 0.2)',
                            borderColor: '#4caf50',
                            borderWidth: 3,
                            pointBackgroundColor: '#4caf50',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Absences (%)',
                            data: monthlyTrend.absences,
                            backgroundColor: 'rgba(198, 40, 40, 0.2)',
                            borderColor: '#c62828',
                            borderWidth: 3,
                            pointBackgroundColor: '#c62828',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.2)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
            
            // Animation au défilement
            animateOnScroll();
            
            // Événements de changement de filtre
            document.getElementById('stat-period').addEventListener('change', updateStats);
            document.getElementById('stat-subject').addEventListener('change', updateStats);
        });

        // Fonction pour mettre à jour les statistiques
        function updateStats() {
            const period = document.getElementById('stat-period').value;
            const subject = document.getElementById('stat-subject').value;
            
            // Simuler des données différentes selon les filtres
            let newPresenceRate, newTotalAbsences, newJustifiedAbsences;
            
            if (period === 'week') {
                newPresenceRate = 85;
                newTotalAbsences = 42;
                newJustifiedAbsences = 28;
            } else if (period === 'month') {
                newPresenceRate = 82;
                newTotalAbsences = 156;
                newJustifiedAbsences = 98;
            } else {
                newPresenceRate = 80;
                newTotalAbsences = 320;
                newJustifiedAbsences = 210;
            }
            
            if (subject !== 'all') {
                newPresenceRate = subject === 'math' ? 78 : subject === 'physics' ? 83 : subject === 'cs' ? 88 : subject === 'english' ? 85 : 82;
                newTotalAbsences = subject === 'math' ? Math.round(newTotalAbsences * 1.2) : subject === 'physics' ? Math.round(newTotalAbsences * 0.9) : Math.round(newTotalAbsences * 0.7);
                newJustifiedAbsences = Math.round(newTotalAbsences * 0.65);
            }
            
            const newJustifiedPercentage = Math.round((newJustifiedAbsences / newTotalAbsences) * 100);
            
            // Mettre à jour les compteurs avec animation
            const presenceRateCounter = new CountUp('presence-rate', parseInt(document.getElementById('presence-rate').textContent), newPresenceRate, 0, 1.5);
            const totalAbsencesCounter = new CountUp('total-absences', parseInt(document.getElementById('total-absences').textContent), newTotalAbsences, 0, 1.5);
            const justifiedAbsencesCounter = new CountUp('justified-absences', parseInt(document.getElementById('justified-absences').textContent), newJustifiedAbsences, 0, 1.5);
            const justifiedPercentageCounter = new CountUp('justified-percentage', parseInt(document.getElementById('justified-percentage').textContent), newJustifiedPercentage, 0, 1.5);
            
            presenceRateCounter.start();
            totalAbsencesCounter.start();
            justifiedAbsencesCounter.start();
            justifiedPercentageCounter.start();
            
            // Mettre à jour la barre de progression
            document.querySelector('.progress-bar').style.width = newPresenceRate + '%';
            
            // Mettre à jour les graphiques (simuler des données différentes)
            updateCharts(period, subject);
        }

        // Fonction pour mettre à jour les graphiques
        function updateCharts(period, subject) {
            // Récupérer les instances de graphiques
            const charts = Chart.instances;
            
            // Générer de nouvelles données aléatoires pour les graphiques
            const newWeeklyData = Array.from({length: 5}, () => Math.floor(Math.random() * 30) + 65);
            const newSubjectData = Array.from({length: 5}, () => Math.floor(Math.random() * 25) + 5);
            const newMonthlyPresence = Array.from({length: 6}, () => Math.floor(Math.random() * 15) + 75);
            const newMonthlyAbsences = newMonthlyPresence.map(val => 100 - val);
            
            // Mettre à jour chaque graphique
            charts.forEach(chart => {
                if (chart.canvas.id === 'attendance-chart') {
                    chart.data.datasets[0].data = newWeeklyData;
                    chart.data.datasets[0].backgroundColor = newWeeklyData.map(value => value < 70 ? '#c62828' : value < 80 ? '#ff9800' : '#4caf50');
                    chart.data.datasets[0].borderColor = newWeeklyData.map(value => value < 70 ? '#b71c1c' : value < 80 ? '#f57c00' : '#2e7d32');
                } else if (chart.canvas.id === 'subjects-chart') {
                    chart.data.datasets[0].data = newSubjectData;
                } else if (chart.canvas.id === 'monthly-chart') {
                    chart.data.datasets[0].data = newMonthlyPresence;
                    chart.data.datasets[1].data = newMonthlyAbsences;
                }
                
                // Animer la mise à jour
                chart.update('active');
            });
        }
    </script>
    <script src="script.js"></script>
</body>
</html>