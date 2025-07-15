# TOURISIA – Backend PHP (API REST)

Backend officiel du projet **TOURISIA**, une plateforme de réservation touristique haut de gamme.  
Ces APIs sécurisées en **PHP natif** sert d’interface entre la base de données et le frontend (React ou autre).

##  Sommaire

- Présentation
- Technologies
- Architecture
- Installation avec XAMPP
- Configuration
- Démarrage
- Structure des dossiers
-  Principaux Endpoints API
- Authentification JWT
- Tester les APIs
- Contribuer
- Licence


##  Présentation

Le backend TOURISIA prend en charge :

- L’inscription et l’authentification des utilisateurs
- La gestion des offres (hébergement, transport, etc.)
- Les réservations et les paiements
- L’administration (utilisateurs, modération, statistiques)


## Technologies

- PHP 8+
- MySQL
- JWT (JSON Web Token)
- Composer
- Postman / curl (pour tester l’API)


##  Architecture

| Dossier/Fichier        | Description                             |
|------------------------|-----------------------------------------|
| `/api/config/`         | Connexion à la base de données          |
| `/api/controllers/`    | Logique métier (User, Admin, etc.)      |
| `/api/models/`         | Accès aux données (modèles PHP)         |
| `/api/core/`           | JWT, validation, réponses formatées     |
| `/api/public/`         | Point d’entrée de l’API (`index.php`)   |


## ⚙ Installation avec XAMPP

### 1. Prérequis

- **XAMPP** installé
- PHP 8+ activé
- MySQL activé
- Git installé

### 2. Cloner le projet

git clone https://github.com/emmanuelsenakpon12/TOURISIA.git

### 3. Créer la base de données
Aller sur http://localhost/phpmyadmin

Créer une base de données nommée tourisia

Importer le fichier tourisia.sql 

4. Configurer la connexion à la base
-Dans le fichier /api/config/database.php :

private $host = "localhost";
private $db_name = "tourisia";
private $username = "root";
private $password = "";

-Démarrage
Lancer Apache et MySQL via le panneau XAMPP

-Accéder à l’API à l’adresse :

http://localhost/TOURISIA/api/public/index.php/api/

### Principaux Endpoints API
Méthode	Endpoint	.........................Description	.....................Authentification
POST	/api/register........................Inscription utilisateur	.........Non
POST	/api/login	.........................Connexion utilisateur (JWT).......Non	
GET	/api/offers	...........................Lister les offres................	Non
POST	/api/offers........................	Créer une offre (prestataire)	.....OUI 
POST	/api/reservations	..................Créer une réservation	.............OUI 
GET	/api/user/reservations	..............Voir mes réservations	.............OUI 
POST	/api/payments	......................Effectuer un paiement	.............OUI 
GET	/api/admin/users	.................Lister les utilisateurs (admin)	.....OUI  (admin)
POST	/api/admin/users/toggle	........Activer/Désactiver un utilisateur	.....OUI  (admin)

## Authentification JWT
Lors de la connexion (/api/login), un token JWT est retourné.

Ce token doit être envoyé dans l’en-tête Authorization pour accéder aux routes protégées.

Exemple d’en-tête :
Authorization: Bearer VOTRE_TOKEN_JWT

# Tester les APIs
#Connexion (obtenir un token)
 POST http://localhost/TOURISIA/api/public/index.php/api/login \
        -H "Content-Type: application/json" \
        -d '{"email": "admin@example.com", "password": "motdepasse"}'
        Postman :

Méthode : POST

### URL : http://localhost/TOURISIA/api/public/index.php/api/login

Body (JSON) :

{
  "email": "admin@example.com",
  "password": "motdepasse"
}
# Lister les utilisateurs (admin)

### GET http://localhost/TOURISIA/api/public/index.php/api/admin/users \
-H "Authorization: Bearer VOTRE_TOKEN_ADMIN"
#  Créer une réservation

## POST http://localhost/TOURISIA/api/public/index.php/api/reservations \
-H "Authorization: Bearer VOTRE_TOKEN" \
-H "Content-Type: application/json" \
-d '{"offer_id": 1, "date": "2025-07-20"}'
# Paiement

### POST http://localhost/TOURISIA/api/public/index.php/api/payments \
-H "Authorization: Bearer VOTRE_TOKEN" \
-H "Content-Type: application/json" \
-d '{"amount": 500, "method": "credit_card"}'

### Licence
Projet sous licence MIT.
Vous êtes libre de l’utiliser, le modifier et le partager.

