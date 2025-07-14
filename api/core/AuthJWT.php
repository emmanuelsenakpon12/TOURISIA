<?php
require_once __DIR__ . '/../config/jwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthJWT {
    public static function generateToken($userData) {
        $payload = [
            'iss' => 'tourisia',
            'iat' => time(),
            'exp' => time() + JWT_EXPIRATION,
            'data' => $userData
        ];
        return JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);
    }

    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGORITHM));
            return (array) $decoded->data;
        } catch (ExpiredException $e) {
            throw new Exception("Token expiré", 401);
        } catch (SignatureInvalidException $e) {
            throw new Exception("Signature invalide", 401);
        } catch (Exception $e) {
            throw new Exception("Token invalide", 401);
        }
    }
}
