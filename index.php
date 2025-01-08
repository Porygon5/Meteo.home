<?php
$serverName = 'localhost';
$userName = 'meteo_user';
$password = 'randompass';
$dbName = 'meteo';

// Créer une connexion
$conn = new mysqli($serverName, $userName, $password, $dbName);

// Vérifier la connexion
if ($conn->connect_error) {
    die('La connexion a échoué : ' . $conn->connect_error);
}

// Récupérer les données de la table "mesures"
$sql = 'SELECT * FROM mesures ORDER BY id DESC';
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesures Météo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
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
    </div>
</body>

</html>

<?php
$conn->close();
?>
