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

3. **Utiliser la bdd pour les tests** :
   ```bash
   Placer le fichier "data.db" dans le dossier var
   ```

## Identifiants de test

- **Utilisateur** : `test@test.test` / `testtest`  
  (Déjà beaucoup de données d’humeur pour tester le calendrier et les graphiques en naviguant sur "Previous Month")
- **Administrateur** : `admin@test.test` / `testtest`

## Exemple de .env
```
# In all environments, the following files are loaded if they exist,
# the latter taking precedence over the former:
#
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides
#  * .env.$APP_ENV       committed environment-specific defaults
#  * .env.$APP_ENV.local uncommitted environment-specific overrides
#
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
# https://symfony.com/doc/current/configuration/secrets.html
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
 DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
#DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
MAILER_DSN=brevo+smtp://
###< symfony/mailer ###

###> symfony/brevo-mailer ###
# MAILER_DSN=brevo+api://KEY@default
# MAILER_DSN=brevo+smtp://USERNAME:PASSWORD@default
###< symfony/brevo-mailer ###
```
