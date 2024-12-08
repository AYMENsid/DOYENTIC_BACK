<?php
header('Content-Type: application/json');
include('connect.php');  // Connexion à la base de données avec PDO
$conn = connect();

// Paramètres de pagination
$limit = 10;  // Nombre d'articles par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);  // S'assurer que la page est au moins 1
$offset = ($page - 1) * $limit;

// Requête pour récupérer les articles de Basketball
$query = "SELECT * FROM articles WHERE sport='Handball' LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);

// Lier les paramètres
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Exécution de la requête
$stmt->execute();

// Vérification des résultats
if ($stmt->rowCount() > 0) {
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération des articles
    echo json_encode($articles);  // Renvoie les articles sous forme de JSON
} else {
    echo json_encode(["message" => "Aucun article trouvé"]);
}

$conn = null; // Fermeture de la connexion PDO
?>
