<?php
header('Content-Type: application/json');
include('connect.php');  // Connexion à la base de données avec PDO
$conn = connect();

// Vérification que l'ID a été passé en paramètre dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = (int)$_GET['id'];  // On récupère l'ID de l'article passé en paramètre

    // Préparation de la requête SQL pour récupérer les détails de l'article
    $query = "SELECT * FROM articles WHERE id = :id";
    $stmt = $conn->prepare($query);

    // Lier le paramètre
    $stmt->bindParam(':id', $article_id, PDO::PARAM_INT);

    // Exécution de la requête
    $stmt->execute();

    // Vérification des résultats
    if ($stmt->rowCount() > 0) {
        $article = $stmt->fetch(PDO::FETCH_ASSOC); // Récupération des données de l'article
        echo json_encode($article);  // Renvoie les détails de l'article sous forme de JSON
    } else {
        // Si aucun article n'a été trouvé avec cet ID
        echo json_encode(["error" => "Article non trouvé"]);
    }
} else {
    // Si l'ID n'a pas été passé ou est invalide
    echo json_encode(["error" => "Aucun ID d'article fourni ou ID invalide"]);
}

$conn = null; // Fermeture de la connexion PDO
?>
