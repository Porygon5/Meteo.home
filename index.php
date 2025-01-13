<?php
$serverName = 'localhost';
$userName = 'root';
$password = '';
$dbName = 'meteo';

// Créer une connexion
$conn = new mysqli($serverName, $userName, $password, $dbName);

// Vérifier la connexion
if ($conn->connect_error) {
    die('La connexion a échoué : ' . $conn->connect_error);
}

// Récupérer le nombre total de mesures
$totalResultsSql = 'SELECT COUNT(*) AS total FROM mesures WHERE temperature > -20 AND temperature < 40';
$totalResultsResult = $conn->query($totalResultsSql);
$totalResultsRow = $totalResultsResult->fetch_assoc();
$totalResults = (int)$totalResultsRow['total'];

// Paramètres pour la pagination
$resultsPerPage = 20;
$totalPages = ceil($totalResults / $resultsPerPage);
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); // S'assurer que la page est valide
$offset = ($currentPage - 1) * $resultsPerPage;

// Récupérer les données de la page courante
$sql = "SELECT * FROM mesures WHERE temperature > -20 AND temperature < 40 ORDER BY id DESC LIMIT $offset, $resultsPerPage";
$result = $conn->query($sql);

// Appel de l'API OpenWeatherMap
$url = 'http://api.openweathermap.org/data/2.5/forecast?id=524901&appid=5fb375cb6fe70bb81d9c5b46d3808de8';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retourne la réponse au lieu de l'afficher
$response = curl_exec($curl);
curl_close($curl);

// Décoder la réponse JSON
$weatherData = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteo.home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Données de l'API</h1>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Température (°C)</th>
                    <th scope="col">Humidité (%)</th>
                    <th scope="col">Pression (hPa)</th>
                    <th scope="col">Horodatage</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($weatherData['list'])): ?>
                    <?php foreach ($weatherData['list'] as $index => $data): ?>
                        <tr>
                            <td><?= htmlspecialchars($index + 1) ?></td>
                            <td><?= number_format((float)$data['main']['temp'] - 273.15, 2) ?></td>
                            <td><?= htmlspecialchars($data['main']['humidity']) ?></td>
                            <td><?= htmlspecialchars($data['main']['pressure']) ?></td>
                            <td><?= htmlspecialchars($data['dt_txt']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucune donnée disponible</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h1 class="text-center mb-4">Données du Capteur Météo</h1>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Température (°C)</th>
                    <th scope="col">Humidité (%)</th>
                    <th scope="col">Pression (hPa)</th>
                    <th scope="col">Horodatage</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= number_format((float)$row['temperature'], 2) ?></td>
                            <td><?= number_format((float)$row['humidite'], 2) ?></td>
                            <td><?= number_format((float)$row['pression'], 2) ?></td>
                            <td><?= htmlspecialchars($row['horodatage']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucune donnée disponible</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination avec flèches -->
        <div class="d-flex justify-content-between my-4">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-primary">
                    &laquo; Page précédente
                </a>
            <?php endif; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-primary ms-auto">
                    Page suivante &raquo;
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>