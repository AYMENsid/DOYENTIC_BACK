<?php
header('Content-Type: application/json');
include('connect.php');  // Connexion à la base de données avec PDO
$conn = connect();

try {
    // Requête pour récupérer les valeurs des colonnes `ticbr` et `TotalTicket`
    $query = "SELECT ticnbr, TotalTicket FROM mes_match";
    $stmt = $conn->prepare($query);

    // Exécution de la requête
    $stmt->execute();

    // Récupération des résultats sous la forme d'un tableau associatif
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    // Renvoie des résultats en JSON si des lignes sont trouvées
    if ($data) {
        echo json_encode(["data" => $data]);
    } else {
        echo json_encode(["message" => "Aucun enregistrement trouvé"]);
    }
} catch (PDOException $e) {
    // Gestion des erreurs PDO
    echo json_encode(["error" => $e->getMessage()]);
    http_response_code(500);  // Retourne le code HTTP 500 pour les erreurs serveur
}

// Fermeture de la connexion PDO
$conn = null;
?>
