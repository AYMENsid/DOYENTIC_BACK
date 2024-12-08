<?php
header("Content-Type: application/json");

// Inclure le fichier contenant la fonction de connexion
include 'connect.php';

// Inclure PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Appeler la fonction connect() pour obtenir la connexion
$conn = connect();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['email'])) {
    $email = $data['email'];

    try {
        // Préparer la requête pour vérifier si l'email existe
        $stmt = $conn->prepare("SELECT id FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // L'email existe, générer un code de réinitialisation
            $resetCode = mt_rand(100000, 999999);  // Génère un code de réinitialisation de 6 chiffres

            // Mettre à jour le code de réinitialisation dans la base de données
            $stmt = $conn->prepare("UPDATE user SET code = :code WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':code', $resetCode);
            $stmt->execute();

            // Configuration de PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Configuration du serveur SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.zoho.com';  // Serveur SMTP de Zoho
                $mail->SMTPAuth = true;  // Activer l'authentification SMTP
                $mail->Username = 'doyentic@zohomail.com';  // Ton adresse Zoho Mail
                $mail->Password = 'AYMENSIDALI';  // Ton mot de passe ou mot de passe d'application
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // StartTLS pour une connexion sécurisée
                $mail->Port = 587;  // Port pour STARTTLS

                // Destinataire et expéditeur
                $mail->setFrom('doyentic@zohomail.com', 'Support');
                $mail->addAddress($email); // Ajoute l'email de l'utilisateur

                // Contenu de l'email
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body    = "Bonjour,<br><br>Voici votre code de réinitialisation : <b>$resetCode</b><br>Veuillez utiliser ce code pour réinitialiser votre mot de passe. Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.";

                // Envoi de l'email
                $mail->send();
                echo json_encode(["success" => true, "message" => "Un email a été envoyé avec votre code de réinitialisation."]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Cet email n'existe pas."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Requête invalide."]);
}
