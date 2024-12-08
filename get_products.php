<?php
header('Content-Type: application/json'); // La réponse sera en JSON
header('Access-Control-Allow-Origin: *'); // Permet l'accès depuis toutes les origines

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "mouloudia"); // Remplacez "ticapp" par le nom exact de votre base de données

// Vérifie si la connexion échoue
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Requête SQL pour récupérer les produits
$sql = "SELECT id, name, price, description, image_url, image2_url FROM products";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

// Prépare les résultats
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Retourne les données au format JSON
echo json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Ferme la connexion
$conn->close();
?>
