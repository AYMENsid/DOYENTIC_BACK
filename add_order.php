<?php
// Connexion à la base de données
$servername = "localhost"; // Serveur MySQL
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$dbname = "mouloudia"; // Nom de la base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les données envoyées depuis Flutter
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['productName'], $data['fullName'], $data['phone'], $data['email'], $data['address'], $data['size'])) {
    $productName = $data['productName'];
    $fullName = $data['fullName'];
    $phone = $data['phone'];
    $email = $data['email'];
    $address = $data['address'];
    $size = $data['size'];

    // Requête SQL pour insérer les données
    $sql = "INSERT INTO commande (nom_produit, nom_client, telephone, email, adresse, taille) 
            VALUES ('$productName', '$fullName', '$phone', '$email', '$address', '$size')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Commande ajoutée avec succès"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erreur : " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
}

$conn->close();
?>
