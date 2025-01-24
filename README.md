# toubeelib_ARCELIN_WALCZAK

**Dimitri WALCZAK-VELA-MENA**  
**Nino ARCELIN**  
**Romain PAILLER**

---

# Documentation API

## Introduction

Cette API permet de gérer les rendez-vous médicaux, des praticiens, des patients, etc. Elle inclut des routes publiques pour l'authentification et des routes protégées qui nécessitent un **token JWT** valide pour pouvoir y accéder.

## Routes

### Routes Publiques

Ces routes sont accessibles sans authentification.

#### 1. **Page d'accueil**

- **URL** : `/`
- **Méthode** : `GET`
- **Description** : Affiche une page d'accueil de l'API.

#### 2. **Authentification (Se connecter)**

- **URL** : `/auth/signin`
- **Méthode** : `POST`
- **Description** : Connecte un utilisateur et renvoie un token JWT.

**Exemple de requête** :

```bash
POST /auth/signin
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "your_password"
}
```

**Réponse** :

```json
{
  "access_token": "votre_access_token_jwt",
  "refresh_token": "votre_refresh_token_jwt"
}
```

### Routes Protégées (Nécessitent un Token JWT)

Ces routes nécessitent un token JWT valide dans l'en-tête `Authorization` pour accéder aux ressources.

#### 1. **Rendez-vous**

- **URL** : `/secure/rdvs/{ID-RDV}`
- **Méthodes** : `GET`, `PATCH`, `POST`, `DELETE`
- **Description** : Gère les rendez-vous médicaux.

**Exemple de requêtes** :

```bash
# Récupérer un rendez-vous
GET /secure/rdvs/1
Authorization: Bearer <votre_access_token_jwt>
```

```bash
# Créer un nouveau rendez-vous
POST /secure/rdvs
Authorization: Bearer <votre_access_token_jwt>
Content-Type: application/json

{
    "date": "2025-02-15T09:00:00",
    "praticien": "12345",
    "patient": "67890"
}
```

### 2. **Praticiens**

- **URL** : `/secure/praticiens`
- **Méthodes** : `POST`, `GET`
- **Description** : Crée et récupère des praticiens.

```bash
# Récupérer la liste des praticiens
GET /secure/praticiens
Authorization: Bearer <votre_access_token_jwt>
```

```bash
# Créer un praticien
POST /secure/praticiens
Authorization: Bearer <votre_access_token_jwt>
Content-Type: application/json

{
    "nom": "Dr. John Doe",
    "specialite": "Cardiologue"
}
```

---

## Middlewares

### AuthMiddleware

Le **`AuthMiddleware`** vérifie si le token JWT est valide. Il est appliqué aux routes protégées sous `/secure`.

- **Si le token est valide**, l'accès est autorisé.
- **Si le token est absent ou invalide**, l'accès est refusé avec une erreur `401 Unauthorized`.

### AuthzPraticienMiddleware

Le **`AuthzPraticienMiddleware`** vérifie si l'utilisateur a les autorisations nécessaires pour accéder aux informations des praticiens. Il est appliqué à la route de récupération des praticiens.

- **Si l'utilisateur n'a pas le rôle approprié**, l'accès est refusé avec une erreur `403 Forbidden`.

## CORS

L'API prend en charge **CORS (Cross-Origin Resource Sharing)**, ce qui permet à l'API d'être utilisée depuis d'autres domaines.

- La route `/options` configure les en-têtes CORS pour permettre les requêtes depuis n'importe quel domaine.

## Sécurité

L'API utilise **HTTPS** pour sécuriser les communications.

Le token JWT doit être passé dans l'en-tête `Authorization` sous la forme :

```
Authorization: Bearer <votre_token_jwt>
```

- Si le token est absent ou invalide, l'API renverra une erreur `401 Unauthorized`.

---

## Résumé des Routes

- **Public** :

  - `/auth/signin` : Authentifie un utilisateur et renvoie un token JWT.

- **Protégées** :
  - `/secure/rdvs` : Gère les rendez-vous médicaux (GET, POST, PATCH, DELETE).
  - `/secure/praticiens` : Crée et récupère des praticiens (POST, GET).
  - `/secure/praticiens/{ID-PRATICIEN}/disponibilites` : Récupère les disponibilités d'un praticien (GET).

---

### Remarques

- Le token JWT doit être ajouté dans l'en-tête `Authorization` sous la forme `Bearer <token>`.
- Pour tester les routes protégées, vous devez d'abord vous authentifier avec `/auth/signin` pour obtenir un **token JWT valide**.
- Le middleware `AuthzPraticienMiddleware` s'assure que l'utilisateur dispose du rôle nécessaire pour accéder aux informations des praticiens.
