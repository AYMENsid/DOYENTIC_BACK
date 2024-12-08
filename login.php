<?php
session_start();
include "connect.php";

// Récupérer les données de la requête POST
$postData = file_get_contents('php://input');
$data = json_decode($postData, true); // Récupérer les données sous forme de tableau associatif
$email = $data['email'];
$password = $data['password'];

// Connexion à la base de données
$conn = connect();

// Vérifier si l'email existe
$countQuery = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
$countQuery->bindParam(':email', $email);
$countQuery->execute();
$count = $countQuery->fetchColumn();

if ($count == 0) {
    // Email non trouvé, renvoyer une erreur 404
    http_response_code(404);
    echo json_encode(array("message" => "Email incorrect"));
} else {
    // Récupérer les données de l'utilisateur
    $selectQuery = $conn->prepare("SELECT * FROM user WHERE email = :email");
    $selectQuery->bindParam(':email', $email);
    $selectQuery->execute();
    $user = $selectQuery->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe
    if ($password == $user['password']) { // Si les mots de passe sont identiques
        // Connexion réussie
        http_response_code(200);
        echo json_encode(array("user_id" => $user['id'], "success" => true));
    } else {
        // Mot de passe incorrect
        http_response_code(401);
        echo json_encode(array("message" => "Mot de passe incorrect", "success" => false));
    }
}
?>
