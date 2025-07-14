<?php
class Validator {
    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Adresse email invalide", 400);
        }
    }

    public static function validatePassword($password) {
        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères", 400);
        }
    }

    public static function validateOfferData($data) {
        $required = ['title', 'description', 'price'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Le champ $field est requis", 400);
            }
        }
        
        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            throw new Exception("Le prix doit être un nombre positif", 400);
        }
    }

    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('self::sanitizeInput', $input);
        }
        return htmlspecialchars(strip_tags(trim($input)));
    }
}
