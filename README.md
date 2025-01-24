# toubeelib_ARCELIN_WALCZAK

**Dimitri WALCZAK-VELA-MENA**  
**Nino ARCELIN**  
**Romain PAILLER**

---

# 👥 Contribution au projet

Légende :

✅ Créateur
🔶 Soutien

## TD1 : Analyse et conception de la couche Métier

| Exercices                                     | Nino | Romain | Dimitri |
| --------------------------------------------- | :--: | :----: | :-----: |
| architecture                                  |  ✅  |   ✅   |   ✅    |
| conception du service de prise de rendez-vous |  ✅  |   ✅   |   ✅    |

## TD2 : construction du composant métier de gestion des RDV

| Exercices                              | Nino | Romain | Dimitri |
| -------------------------------------- | :--: | :----: | :-----: |
| consulter un rendez-vous               |  ✅  |        |         |
| créer un rendez-vous                   |  ✅  |        |         |
| annuler un rendez-vous                 |  ✅  |        |         |
| lister les disponibilités du praticien |  ✅  |        |         |
| modifier un rendez-vous                |  ✅  |        |         |
| gérer le cycle de vie des rendez-vous  |  ✅  |        |         |

## TD3 : API Restful

| Exercices                | Nino | Romain | Dimitri |
| ------------------------ | :--: | :----: | :-----: |
| API v1                   |      |   ✅   |         |
| API v2                   |      |   ✅   |         |
| API et données échangées |      |   ✅   |         |
| accéder à un rendez-vous |      |   ✅   |         |
| modifier un rendez-vous  |      |   ✅   |         |
| créer un rendez-vous     |      |   ✅   |         |
| compléter l’API          |  🔶  |   ✅   |         |
| CORS                     |      |   ✅   |         |

## TD4 : Cors

| Exercices    | Nino | Romain | Dimitri |
| ------------ | :--: | :----: | :-----: |
| headers CORS |      |   ✅   |         |

## TD5 : JWT, Authn/Authz

| Exercices                                           | Nino | Romain | Dimitri |
| --------------------------------------------------- | :--: | :----: | :-----: |
| Authentification                                    |  ✅  |        |         |
| Route Signin                                        |  ✅  |        |         |
| Middleware de contrôle d’authentification           |  ✅  |        |         |
| Authz pour les praticiens                           |  ✅  |        |         |
| Contrôle d’autorisation pour accéder à un praticien |  ✅  |        |         |

## TD6 : architecure micro-services

| Exercices | Nino | Romain | Dimitri |
| --------- | :--: | :----: | :-----: |

## TD7 : Authn/Authz dans l'architecture microservices

## TD8 : communication asynchrones avec RabbitMQ

## Fonctionnalités globales

### Toubeelib, architecture générale (noté sur 10 points)

| Fonctionnalités                                                | Nino | Romain | Dimitri |
| -------------------------------------------------------------- | :--: | :----: | :-----: |
| API respectant les principes RESTful                           |      |   ✅   |         |
| architecture basée sur les principes d’architecture Hexagonale |  ✅  |   ✅   |         |
| utilisation d’un conteneur d’injection de dépendances          |  ✅  |   ✅   |         |
| traitement des erreurs et exceptions                           |  ✅  |   ✅   |         |
| traitement des headers CORS                                    |      |   ✅   |         |
| authentification à l’aide de tokens JWT                        |  ✅  |        |         |
| middlewares                                                    |  ✅  |   ✅   |         |
| validation et filtrage des données reçues au travers de l’API  |  ✅  |   ✅   |         |
| utilisation de bases de données distinctes                     |      |        |   ✅    |

### Les fonctionnalités minimales attendues (notées sur 6 points)

| Fonctionnalités                                                                              | Nino | Romain | Dimitri |
| -------------------------------------------------------------------------------------------- | :--: | :----: | :-----: |
| lister/rechercher des praticiens                                                             |  ✅  |        |         |
| lister les disponibilités d’un praticien sur une période donnée (date de début, date de fin) |  ✅  |        |         |
| réserver un rendez-vous pour un praticien à une date/heure donnée                            |  ✅  |        |         |
| annuler un rendez-vous, à la demande d’un patient ou d’un praticien                          |  ✅  |        |         |
| gérer le cycle de vie des rendez-vous (honoré, non honoré, payé)                             |      |        |   ✅    |
| afficher le planning d’un praticien sur une période donnée (date de début, date de fin)      |  ✅  |        |         |
| afficher les rendez-vous d’un patient                                                        |  ✅  |        |         |
| s’authentifier en tant que patient ou praticien                                              |  ✅  |        |         |

### Les fonctionnalités additionnelles attendues (notées sur 4 points)

| Fonctionnalités                                                                                            | Nino | Romain | Dimitri |
| ---------------------------------------------------------------------------------------------------------- | :--: | :----: | :-----: |
| créer un praticien                                                                                         |  ✅  |        |         |
| s’inscrire en tant que patient                                                                             |  ✅  |        |         |
| gérer les indisponibilités d’un praticien : périodes ponctuelles sur lesquelles il ne peut accepter de RDV |  ✅  |        |         |
| gérer les disponibilités d’un praticien : jours, horaires et durée des RDV pour chaquepraticien            |  ✅  |        |         |

---

# ▶️ Lancer le projet

Pour lancer le projet il suffit simplement de rentrer cette commande à la racine du projet

```bash
docker compose up -d -- build
```

---

# 📝 Documentation API

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
