<?php
// Autoriser les requêtes de n'importe quelle origine (vous pouvez spécifier un domaine précis pour plus de sécurité)
header("Access-Control-Allow-Origin: *");

// Autoriser les méthodes HTTP spécifiques (ici POST)
header("Access-Control-Allow-Methods: POST");

// Autoriser les en-têtes spécifiques (pour accepter les en-têtes comme Content-Type)
header("Access-Control-Allow-Headers: Content-Type");

// Si la requête est une requête préliminaire (OPTIONS), répondre immédiatement
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Répond à la requête préliminaire CORS et arrête le traitement
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Votre code de traitement de la requête ici
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    $name = $input['name'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_BCRYPT);

    $conn = connect();
    $stmt = $conn->prepare("INSERT INTO user(name, email, password) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Échec de l\'enregistrement']);
    }
}
?>
