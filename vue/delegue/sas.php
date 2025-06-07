<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Absences</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card h3 {
            margin-top: 0;
            color: #3498db;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }
        .chart-container {
            height: 250px;
            margin-top: 15px;
        }
        .summary {
            margin-top: 10px;
            font-size: 0.9em;
            color: #555;
        }
        .progress-bar {
            height: 20px;
            background-color: #ecf0f1;
            border-radius: 10px;
            margin-top: 15px;
            overflow: hidden;
        }
        .progress {
            height: 100%;
            background-color: #3498db;
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }
        .justified {
            background-color: #2ecc71;
        }
        .unjustified {
            background-color: #e74c3c;
        }
        .chart-section {
            margin-top: 40px;
        }
        .chart-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-group {
            margin-bottom: 15px;
        }
        .filter-group label {
            margin-right: 10px;
            font-weight: bold;
        }
        select, button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .student-detail {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tableau de Bord des Absences</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total des Heures d'Absence</h3>
                <div id="total-hours" class="chart-container"></div>
                <div class="summary">
                    <p>Nombre total d'heures: <span id="total-hours-count">0</span></p>
                </div>
            </div>
            <div class="stat-card">
                <h3>Répartition par Statut</h3>
                <div id="status-chart" class="chart-container"></div>
                <div class="summary">
                    <p>Taux de justification: <span id="justification-rate">0%</span></p>
                </div>
            </div>
            <div class="stat-card">
                <h3>Absences par Matière</h3>
                <div id="subject-chart" class="chart-container"></div>
                <div class="summary">
                    <p>Matière la plus concernée: <span id="top-subject">-</span></p>
                </div>
            </div>
        </div>

        <div class="chart-section">
            <h2>Statistiques Détaillées</h2>
            
            <div class="filters">
                <div class="filter-group">
                    <label for="filter-subject">Matière:</label>
                    <select id="filter-subject">
                        <option value="all">Toutes les matières</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-student">Étudiant:</label>
                    <select id="filter-student">
                        <option value="all">Tous les étudiants</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-date-start">Date début:</label>
                    <input type="date" id="filter-date-start">
                </div>
                <div class="filter-group">
                    <label for="filter-date-end">Date fin:</label>
                    <input type="date" id="filter-date-end">
                </div>
                <button id="apply-filters">Appliquer</button>
            </div>

            <table id="absences-table">
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Heures Totales</th>
                        <th>Heures Justifiées</th>
                        <th>Taux de Justification</th>
                        <th>Nombre d'Étudiants</th>
                    </tr>
                </thead>
                <tbody id="stats-body">
                    <!-- Les données seront injectées ici par JavaScript -->
                </tbody>
            </table>
        </div>

        <div class="student-detail" id="student-details">
            <!-- Les détails par étudiant seront injectés ici -->
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script>
        // Données simulées (à remplacer par les données PHP)
        let absenceData = <?php echo getAbsenceDataJson(); ?>;
        let studentData = <?php echo getStudentDataJson(); ?>;
        
        // Initialiser les graphiques et la table lors du chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            initializeFilters();
            initializeCharts();
            updateStatisticsTable();
        });

        function initializeFilters() {
            // Remplir les filtres avec les données disponibles
            const subjectFilter = document.getElementById('filter-subject');
            const studentFilter = document.getElementById('filter-student');
            
            // Récupérer les matières uniques
            const subjects = [...new Set(absenceData.map(item => item.matiere))];
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectFilter.appendChild(option);
            });
            
            // Récupérer les étudiants uniques
            const students = [...new Set(absenceData.map(item => item.id_etudiant))];
            students.forEach(studentId => {
                const option = document.createElement('option');
                option.value = studentId;
                option.textContent = `Étudiant ${studentId}`;
                studentFilter.appendChild(option);
            });
            
            // Initialiser les dates
            const today = new Date();
            const oneMonthAgo = new Date();
            oneMonthAgo.setMonth(today.getMonth() - 1);
            
            document.getElementById('filter-date-start').valueAsDate = oneMonthAgo;
            document.getElementById('filter-date-end').valueAsDate = today;
            
            // Ajouter les événements
            document.getElementById('apply-filters').addEventListener('click', function() {
                updateStatisticsTable();
                initializeCharts();
            });
        }

        function initializeCharts() {
            // Filtrer les données selon les filtres actuels
            const filteredData = filterData();
            
            // Calculer les données pour les graphiques
            const totalHours = filteredData.length * 2; // Supposant que chaque période est de 2 heures
            const justifiedHours = filteredData.filter(item => item.statut === 'justifier').length * 2;
            const unjustifiedHours = totalHours - justifiedHours;
            
            // Calculer les absences par matière
            const subjectData = {};
            filteredData.forEach(item => {
                if (!subjectData[item.matiere]) {
                    subjectData[item.matiere] = 0;
                }
                subjectData[item.matiere] += 2; // Supposant que chaque période est de 2 heures
            });
            
            // Mettre à jour les compteurs
            document.getElementById('total-hours-count').textContent = totalHours;
            document.getElementById('justification-rate').textContent = 
                totalHours > 0 ? Math.round((justifiedHours / totalHours) * 100) + '%' : '0%';
            
            if (Object.keys(subjectData).length > 0) {
                const topSubject = Object.entries(subjectData)
                    .sort((a, b) => b[1] - a[1])[0][0];
                document.getElementById('top-subject').textContent = topSubject;
            }
            
            // Créer les graphiques
            createTotalHoursChart(totalHours);
            createStatusChart(justifiedHours, unjustifiedHours);
            createSubjectChart(subjectData);
        }

        function createTotalHoursChart(totalHours) {
            const ctx = document.getElementById('total-hours');
            
            // Supprimer le graphique existant si présent
            if (window.totalHoursChart) {
                window.totalHoursChart.destroy();
            }
            
            window.totalHoursChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Heures d\'absence'],
                    datasets: [{
                        label: 'Nombre d\'heures',
                        data: [totalHours],
                        backgroundColor: '#3498db',
                        barThickness: 100
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function createStatusChart(justified, unjustified) {
            const ctx = document.getElementById('status-chart');
            
            // Supprimer le graphique existant si présent
            if (window.statusChart) {
                window.statusChart.destroy();
            }
            
            window.statusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Justifiées', 'Non Justifiées'],
                    datasets: [{
                        data: [justified, unjustified],
                        backgroundColor: ['#2ecc71', '#e74c3c']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function createSubjectChart(subjectData) {
            const ctx = document.getElementById('subject-chart');
            
            // Convertir l'objet en tableaux pour Chart.js
            const labels = Object.keys(subjectData);
            const data = Object.values(subjectData);
            
            // Supprimer le graphique existant si présent
            if (window.subjectChart) {
                window.subjectChart.destroy();
            }
            
            window.subjectChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Heures d\'absence',
                        data: data,
                        backgroundColor: '#f39c12'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function updateStatisticsTable() {
            const filteredData = filterData();
            const statsTable = document.getElementById('stats-body');
            statsTable.innerHTML = '';
            
            // Grouper par matière
            const subjectStats = {};
            
            filteredData.forEach(item => {
                if (!subjectStats[item.matiere]) {
                    subjectStats[item.matiere] = {
                        total: 0,
                        justified: 0,
                        students: new Set()
                    };
                }
                
                subjectStats[item.matiere].total += 2; // 2 heures par période
                if (item.statut === 'justifier') {
                    subjectStats[item.matiere].justified += 2;
                }
                subjectStats[item.matiere].students.add(item.id_etudiant);
            });
            
            // Ajouter les lignes au tableau
            for (const [subject, stats] of Object.entries(subjectStats)) {
                const row = document.createElement('tr');
                
                const subjectCell = document.createElement('td');
                subjectCell.textContent = subject;
                row.appendChild(subjectCell);
                
                const totalCell = document.createElement('td');
                totalCell.textContent = stats.total + ' h';
                row.appendChild(totalCell);
                
                const justifiedCell = document.createElement('td');
                justifiedCell.textContent = stats.justified + ' h';
                row.appendChild(justifiedCell);
                
                const rateCell = document.createElement('td');
                const justificationRate = stats.total > 0 ? Math.round((stats.justified / stats.total) * 100) : 0;
                rateCell.textContent = justificationRate + '%';
                row.appendChild(rateCell);
                
                const studentsCell = document.createElement('td');
                studentsCell.textContent = stats.students.size;
                row.appendChild(studentsCell);
                
                statsTable.appendChild(row);
            }
            
            // Si aucune donnée
            if (Object.keys(subjectStats).length === 0) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 5;
                cell.textContent = 'Aucune donnée disponible pour les filtres sélectionnés';
                cell.style.textAlign = 'center';
                row.appendChild(cell);
                statsTable.appendChild(row);
            }
            
            // Mettre à jour les détails par étudiant si un étudiant spécifique est sélectionné
            updateStudentDetails();
        }

        function updateStudentDetails() {
            const studentId = document.getElementById('filter-student').value;
            const detailsContainer = document.getElementById('student-details');
            
            // N'afficher les détails que si un étudiant spécifique est sélectionné
            if (studentId === 'all') {
                detailsContainer.innerHTML = '';
                return;
            }
            
            const filteredData = filterData().filter(item => item.id_etudiant == studentId);
            
            // Créer l'en-tête des détails de l'étudiant
            detailsContainer.innerHTML = `
                <h2>Détails pour l'Étudiant ${studentId}</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Période</th>
                            <th>Matière</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="student-details-body">
                    </tbody>
                </table>
            `;
            
            const detailsBody = document.getElementById('student-details-body');
            
            // Trier par date
            filteredData.sort((a, b) => new Date(a.dates) - new Date(b.dates));
            
            // Ajouter chaque absence
            filteredData.forEach(item => {
                const row = document.createElement('tr');
                
                const dateCell = document.createElement('td');
                dateCell.textContent = formatDate(item.dates);
                row.appendChild(dateCell);
                
                const periodCell = document.createElement('td');
                periodCell.textContent = item.periode;
                row.appendChild(periodCell);
                
                const subjectCell = document.createElement('td');
                subjectCell.textContent = item.matiere;
                row.appendChild(subjectCell);
                
                const statusCell = document.createElement('td');
                statusCell.textContent = item.statut === 'justifier' ? 'Justifiée' : 'Non justifiée';
                statusCell.style.color = item.statut === 'justifier' ? '#2ecc71' : '#e74c3c';
                row.appendChild(statusCell);
                
                detailsBody.appendChild(row);
            });
            
            // Si aucune donnée
            if (filteredData.length === 0) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 4;
                cell.textContent = 'Aucune absence pour cet étudiant selon les filtres sélectionnés';
                cell.style.textAlign = 'center';
                row.appendChild(cell);
                detailsBody.appendChild(row);
            }
        }

        function filterData() {
            const subjectFilter = document.getElementById('filter-subject').value;
            const studentFilter = document.getElementById('filter-student').value;
            const dateStart = document.getElementById('filter-date-start').valueAsDate;
            const dateEnd = document.getElementById('filter-date-end').valueAsDate;
            
            return absenceData.filter(item => {
                const itemDate = new Date(item.dates);
                
                return (subjectFilter === 'all' || item.matiere === subjectFilter) &&
                       (studentFilter === 'all' || item.id_etudiant == studentFilter) &&
                       (!dateStart || itemDate >= dateStart) &&
                       (!dateEnd || itemDate <= dateEnd);
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }
    </script>
</body>
</html>

<?php
// Fonction pour récupérer les données d'absence au format JSON
function getAbsenceDataJson() {
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_abscence";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Requête SQL pour récupérer les absences
        $stmt = $conn->prepare("SELECT * FROM absences");
        $stmt->execute();
        
        // Récupérer les résultats
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si pas de base de données disponible, utiliser ces données fictives basées sur votre exemple
    } catch(PDOException $e) {
        // En cas d'erreur, utiliser des données fictives basées sur l'exemple
        $result = [
            ["id_absence" => 1, "dates" => "2025-05-07", "periode" => "10H-12H", "matiere" => "Anglais", "statut" => "justifier", "id_etudiant" => 1],
            ["id_absence" => 2, "dates" => "2025-05-07", "periode" => "13H-15H", "matiere" => "Mathématiques", "statut" => "justifier", "id_etudiant" => 1],
            ["id_absence" => 3, "dates" => "2025-05-09", "periode" => "8H-10H", "matiere" => "Anglais", "statut" => "justifier", "id_etudiant" => 1],
            ["id_absence" => 4, "dates" => "2025-05-09", "periode" => "8H-10H", "matiere" => "Anglais", "statut" => "justifier", "id_etudiant" => 2],
            ["id_absence" => 5, "dates" => "2025-05-09", "periode" => "8H-10H", "matiere" => "Anglais", "statut" => "non_justifier", "id_etudiant" => 3],
            ["id_absence" => 6, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 7, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 8, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 9, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 10, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 11, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 12, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 13, "dates" => "2025-05-09", "periode" => "15H-17H", "matiere" => "Informatique", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 14, "dates" => "2025-05-06", "periode" => "8H-10H", "matiere" => "deep learning", "statut" => "non_justifier", "id_etudiant" => 2],
            ["id_absence" => 15, "dates" => "2025-05-06", "periode" => "8H-10H", "matiere" => "deep learning", "statut" => "non_justifier", "id_etudiant" => 3]
        ];
    }
    
    return json_encode($result);
}

// Fonction pour récupérer les données des étudiants au format JSON
function getStudentDataJson() {
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_abscence";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Requête SQL pour récupérer les étudiants
        $stmt = $conn->prepare("SELECT * FROM etudiants");
        $stmt->execute();
        
        // Récupérer les résultats
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        // En cas d'erreur, utiliser des données fictives
        $result = [
            ["id_etudiant" => 1, "nom" => "Dupont", "prenom" => "Marie"],
            ["id_etudiant" => 2, "nom" => "Martin", "prenom" => "Jean"],
            ["id_etudiant" => 3, "nom" => "Durand", "prenom" => "Sophie"]
        ];
    }
    
    return json_encode($result);
}
?>