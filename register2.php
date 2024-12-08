<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "connect.php";

// Récupérer les données de la requête POST
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

// Vérifier si les données nécessaires sont présentes
if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    $response = array("message" => "Veuillez remplir tous les champs.");
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit;
}

// Vérifier que le mot de passe fait plus de 6 caractères
if (strlen($data['password']) < 6) {
    $response = array("message" => "Le mot de passe doit contenir plus de 6 caractères.");
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit;
}

// Récupérer les informations du formulaire
$name = $data['name'];
$email = $data['email'];
$password = $data['password']; // Ne plus hacher le mot de passe

// Connexion à la base de données
$conn = connect();

// Vérifier si l'email existe déjà
$countQuery = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
$countQuery->bindParam(':email', $email);
$countQuery->execute();
$count = $countQuery->fetchColumn();

if ($count > 0) {
    // L'email est déjà utilisé
    $response = array("message" => "Email déjà utilisé");
    http_response_code(409); // Code de statut 409 (Conflit)
} else {
    // Insérer l'utilisateur dans la base de données
    try {
        $insertQuery = $conn->prepare("INSERT INTO user (email, password, name) VALUES (:email, :password, :name)");
        $insertQuery->bindParam(':email', $email);
        $insertQuery->bindParam(':password', $password); // Ne plus hacher le mot de passe
        $insertQuery->bindParam(':name', $name);

        if ($insertQuery->execute()) {
            $userId = $conn->lastInsertId();
            $response = array("user_id" => $userId, "user_email" => $email, "message" => "Inscription réussie");
            http_response_code(201); // Code de statut 201 (Créé)
        } else {
            $response = array("message" => "Échec de la création de l'utilisateur");
            http_response_code(500); // Code de statut 500 (Erreur interne du serveur)
        }
    } catch (PDOException $e) {
        $response = array("message" => "Erreur de base de données : " . $e->getMessage());
        http_response_code(500); // Code de statut 500
    }
}

// Retourner la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);
