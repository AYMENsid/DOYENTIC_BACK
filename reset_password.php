<?php
header("Content-Type: application/json");

// Inclure le fichier contenant la fonction de connexion
include 'connect.php';

// Inclure PHPMailer (si nécessaire pour envoyer des emails)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Appeler la fonction connect() pour obtenir la connexion
$conn = connect();

$data = json_decode(file_get_contents("php://input"), true);

// Vérifier que la méthode est bien POST et que les données sont présentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['email'], $data['code'], $data['new_password'])) {
    $email = $data['email'];
    $code = $data['code'];
    $newPassword = $data['new_password'];

    try {
        // Préparer la requête pour vérifier si l'email et le code de confirmation existent
        $stmt = $conn->prepare("SELECT id, code FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $storedCode = $user['code'];  // Code de réinitialisation stocké dans la base de données

            if ($storedCode === $code) {
                // Le code de confirmation est correct
                // Mise à jour du mot de passe de l'utilisateur (sans hachage)
                $updateStmt = $conn->prepare("UPDATE user SET password = :password, code = NULL WHERE email = :email");
                $updateStmt->bindParam(':password', $newPassword); // Mot de passe en clair
                $updateStmt->bindParam(':email', $email);
                $updateStmt->execute();

                // Répondre avec succès
                echo json_encode(["success" => true, "message" => "Le mot de passe a été réinitialisé avec succès."]);
            } else {
                // Le code de confirmation est incorrect
                echo json_encode(["success" => false, "message" => "Code de confirmation invalide."]);
            }
        } else {
            // L'email n'existe pas
            echo json_encode(["success" => false, "message" => "Cet email n'existe pas."]);
        }
    } catch (PDOException $e) {
        // Erreur de connexion à la base de données
        echo json_encode(["success" => false, "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    // Données invalides
    echo json_encode(["success" => false, "message" => "Requête invalide."]);
}
?>
