# Mood Tracker - Projet Symfony

> Killian HERZER - M1 Gobelins Annecy

## Description

Mood Tracker est une application Symfony qui permet aux utilisateurs de suivre et d'analyser leur humeur quotidienne avec des émojis et des notes, et de visualiser les données sous forme de calendrier et de graphiques.

## Fonctionnalités

### Utilisateur

- **Saisie de l'humeur** : Enregistrement de l’humeur quotidienne (émoji cliquable de 1 à 5) et ajout d'une note optionnelle.
  - Envoi d'un mail à l'utilisateur avec un conseil en fonction de son humeur du jour.
- **Calendrier** : Vue mensuelle des humeurs par jour avec émojis.
- **Statistiques (Insights)** :
    - **Graphique linéaire** : Suivi de l’évolution de l’humeur au quotidien.
    - **Histogramme** : Fréquence de chaque niveau d’humeur.
    - **Radar / Toile d'araignée** : Distribution globale des humeurs.
    - **Camembert** : Visualisation de la répartition des niveaux d'humeur.
    - **Périodes** : Affichage des statistiques par semaine, mois, trimestre, semestre ou année.
- **Export des données (CSV)** : Export des données d’humeur sur une période sélectionnée.

### Administrateur

- **Tableau de bord** : Vue d'ensemble des utilisateurs et statistiques globales d'humeur.
- **Statistiques agrégées** : Les mêmes graphiques sont disponibles pour afficher les données de tous les utilisateurs.
- **CRUD** : Gestion des "advices" et "users" en back-office.

### Technique

- **Notifications (Symfony Notifier)** : Exemple d'envoi d'email de rappel d'enregistrement d'humeur dans `NotificationController`.
- **Webpack Encore** : Gestion des assets (Chart.js pour les graphiques, Tailwind CSS pour le style).
- **Base de données de test** : Prête avec des exemples d'humeurs pour visualiser les fonctionnalités dans le dossier "var".

## Utilisation

1. **Installation des dépendances** :
   ```bash
   composer install
   npm install
   npm run dev
   ```

2. **Lancer le serveur local** :
   ```bash
   symfony serve
   ```

## Identifiants de test

- **Utilisateur** : `test@test.test` / `testtest`  
  (Déjà beaucoup de données d’humeur pour tester le calendrier et les graphiques en naviguant sur "Previous Month")
- **Administrateur** : `admin@test.test` / `testtest`
