
<p align="center">
  <img src="public/img/logoOur.png" alt="OUR BATIMA Logo" width="200"/>
</p>

# OUR BATIMA - Gestion Intelligente des Projets de Construction

## Overview

OUR BATIMA est une application web développée dans le cadre du projet PIDEV 3A à **Esprit School of Engineering** (Année universitaire 2024-2025).  
Elle vise à simplifier et centraliser la gestion des projets de construction en Tunisie, en couvrant toutes les étapes de la planification à la remise des clés.

Ce projet met l'accent sur la **transparence**, l'**efficacité**, et le **suivi en temps réel**, tout en facilitant la communication entre tous les acteurs impliqués (clients, artisans, constructeurs, gestionnaires, etc.).

---

## Features

- ✅ **Gestion complète des utilisateurs** (clients, artisans, constructeurs, gestionnaires, administrateurs)
- ✅ **Planification et suivi des tâches et étapes de projet**
- ✅ **Gestion des projets et des étapes associées**
- ✅ **Suivi de terrain, visites et rapports**
- ✅ **Réclamations et réponses structurées**
- ✅ **Suivi financier (contrats, paiements)**
- ✅ **Gestion de stock et des matériaux**

---

## Tech Stack

### Frontend
- ✅ Twig (Symfony templating engine)
- ✅ HTML5 / CSS3 / Bootstrap

### Backend
- ✅ Symfony 6.4
- ✅ PHP 8.3
- ✅ MySQL 8+

### Other Tools
- ✅ Composer
- ✅ Doctrine ORM
- ✅ Git & GitHub

---

## Project Structure

```
/src
    /Controller
    /Entity
    /Repository
    /Form
/templates
    /base.html.twig
    /user/
    /project/
    ...
/migrations
/public
/config
```

---

## Modules et Entités

### 1. Gestion Utilisateur
- **Utilisateur**: id, nom, prénom, email, téléphone, rôle, adresse, mot de passe, statut, isConfirmed
- **Client / Artisan / Constructeur / GestionnaireStock / Admin**: héritent de Utilisateur
  - Spécialité, salaire/h pour Artisan et Constructeur

### 2. Gestion des Tâches
- **Taches**: id, description, statut, dateDébut, dateFin  
- **Planification**: id, dateDébut, dateFin, heureDebut, heureFin, description

### 3. Gestion de Projet
- **Projet**: id, type, styleArchitectural, budget, état, dateCréation  
- **EtapeProjet**: id, nomEtape, description, dateDébut, dateFin, status, montant  
- **Terrain**: id, emplacement, caractéristiques, superficie, détailsGéographiques  
- **Visite**: id, date, observations  
- **Rapport**: id, titre, contenu, dateCréation

### 4. Réclamations
- **Réclamation**: id, description, statut, date  
- **Réponse**: id, description, statut, date

### 5. Gestion Financière
- **Paiement**: id, datePaiement, montantTotal, modePaiement, statutPaiement  
- **Contrat**: id, dateSignature, montantTotal, typeContrat, étatContrat

### 6. Stock et Articles
- **Stock**: id, nom, emplacement, dateCréation  
- **Article**: id, nom, description, prixUnitaire, photo  
- **Matériaux**: id, quantité, prixTotal, dateLivraison, état

---

## Getting Started

1. **Clonez le dépôt GitHub**  
   ```bash
   git clone https://github.com/lmahdy/OurBatima
   cd OurBatima
   ```

2. **Installez les dépendances**  
   ```bash
   composer install
   ```

3. **Configurez `.env`**  
   Mettez à jour votre fichier `.env` avec les identifiants MySQL :
   ```env
   DATABASE_URL="mysql://user:@127.0.0.1:3306/ourbatimapi"
   ```

4. **Créez la base de données**  
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Lancez le serveur Symfony**  
   ```bash
   symfony server:start
   ```

---

## Topics (GitHub Tags)

`web-development` `construction` `project-management` `symfony` `twig` `mysql` `php` `pidev` `esprit-school-of-engineering`

---

## Acknowledgments

Ce projet a été réalisé sous la supervision de le proffesseur Mr khemiri akram dans le cadre du module **PIDEV** à **Esprit School of Engineering**.

---

## License

Ce projet est uniquement destiné à un usage pédagogique dans le cadre de l’enseignement supérieur à Esprit. Tout usage commercial est interdit sans autorisation.

---

## Contact

Développé par : [Groupe numéro 6]  
