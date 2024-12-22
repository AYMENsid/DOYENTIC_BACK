<?php
include "connect.php";
$conn = connect();
// Récupérer les données envoyées depuis Flutter
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['nom'], $data['email'], $data['date_naissance'], $data['event'])) {
    $nom = $data['nom'];
    $email = $data['email'];
    $date = $data['date_naissance'];
    $num = $data['event'];
   

    // Requête SQL pour insérer les données
    $sql = "INSERT INTO tickets (nom, email, date_naissance, num) 
            VALUES ('$nom', '$email', '$date', '$num')";
    $sql2 = "UPDATE mes_match SET ticnbr = ticnbr - 1";

    if ($conn->query($sql) == TRUE && $conn->query($sql2) == TRUE) {
        echo json_encode(["success" => true, "message" => "Ticket ajouté avec succès"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erreur : " . $conn->error]);
    }
    
} else {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
}


?>
