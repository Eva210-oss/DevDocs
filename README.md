DevDocs – Plateforme collaborative de tutoriels techniques
DevDocs est une application web développée avec Symfony 7.4. Elle permet aux développeurs de consulter, publier et gérer des tutoriels techniques, de les classer par catégories et tags et de commenter les publications.

Fonctionnalités:
- Consultation de la page d'accueil et des tutoriels.
- Affichage détaillé d'un tutoriel et de ses commentaires.
- Filtrage des tutoriels par catégorie et par tag.
- Inscription, connexion et déconnexion.
- Publication et modification de tutoriels par les utilisateurs authentifiés.
- Ajout de commentaires par les utilisateurs authentifiés.
- Contrôle des droits de modification et de suppression à l'aide d'un Voter : l'auteur est autorisé à gérer son tutoriel.
- Validation des données des formulaires.
- Notifications par e-mail (bienvenue et notification à l'administration lors de la publication d'un tutoriel), selon la configuration du projet.
- Interface basée sur un template Bootstrap 5.3.

Technologies utilisées:
- PHP
- Symfony 7.4
- Doctrine ORM et Doctrine Migrations
- Twig
- Bootstrap 5.3
- Symfony Security
- Symfony Validator
- Symfony Mailer

Prérequis:
Avant de commencer, installer les éléments suivants :
- PHP compatible avec Symfony 7.4 et les extensions PHP nécessaires au projet.
- Composer.
- Git.
- Le serveur de base de données utilisé par le projet (par exemple MySQL ou MariaDB), si la configuration repose sur ce serveur.
- Un navigateur web.

Vérifier les installations :
php -v
composer -V
git --version

1. Cloner le dépôt
Dans un terminal, exécuter :
git clone https://github.com/Eva210-oss/DevDocs.git
cd DevDocs
Si le dépôt ou son URL a changé, remplacer l'adresse ci-dessus par l'URL actuelle du dépôt GitHub.

2. Installer les dépendances
À la racine du projet, exécuter :
composer install
Composer installe les dépendances PHP définies dans le projet.

3. Configurer les variables d'environnement
Créer ou modifier le fichier .env.local à la racine du projet. Ce fichier contient les paramètres propres à votre machine et ne doit pas être publié sur GitHub.
Configuration de la base de données
Adapter DATABASE_URL à votre moteur de base de données, au nom de la base, à l'utilisateur et au mot de passe. Exemple pour MySQL/MariaDB en local :
DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@127.0.0.1:3306/devdocs?serverVersion=8.0.32&charset=utf8mb4"
Remplacer les valeurs d'exemple par celles de votre installation. La valeur serverVersion doit correspondre à la version réelle du serveur de base de données et au format attendu par Doctrine.
Configuration du Mailer
Pour tester les e-mails en local, configurer MAILER_DSN en fonction de votre outil de capture ou de votre service SMTP. Exemple avec un serveur local Mailpit (si celui-ci est installé et configuré pour écouter sur le port SMTP 1025) :
MAILER_DSN=smtp://127.0.0.1:1025
Adapter cette valeur à la configuration de votre outil. Ne pas utiliser de véritables identifiants SMTP privés dans un fichier versionné.

4. Créer la base de données
Après avoir configuré DATABASE_URL, créer la base de données :
php bin/console doctrine:database:create
Si la base existe déjà, cette commande peut indiquer qu'elle a déjà été créée.

5. Exécuter les migrations
Appliquer les migrations pour créer les tables et les relations définies dans les entités :
php bin/console doctrine:migrations:migrate
Confirmer l'exécution lorsqu'on vous le demande.

6. Charger les données de test (Fixtures)
Si le projet contient des Fixtures, installer le bundle si nécessaire (uniquement si celui-ci n'est pas déjà présent) :
composer require --dev orm-fixtures
Puis charger les données :
php bin/console doctrine:fixtures:load
Attention : le chargement des Fixtures remplace généralement les données existantes de la base. Utiliser cette commande uniquement sur une base de test ou après avoir sauvegardé les données importantes.

7. Vérifier la configuration Symfony (facultatif)
Pour afficher les routes disponibles :
php bin/console debug:router
Pour vérifier les migrations :
php bin/console doctrine:migrations:status
Pour vérifier la configuration du Mailer :
php bin/console debug:config framework mailer

8. Lancer l'application
Lancer le serveur de développement Symfony :
symfony server:start
Si la commande Symfony CLI n'est pas installée, utiliser le serveur PHP intégré :
php -S 127.0.0.1:8000 -t public
Puis ouvrir dans le navigateur :

http://127.0.0.1:8000

Pour arrêter le serveur PHP intégré, appuyer sur Ctrl + C dans le terminal.
9. Tester les fonctionnalités principales
1. Ouvrir la page d'accueil et consulter les tutoriels.
2. Filtrer les tutoriels par catégorie et par tag.
3. Ouvrir un tutoriel et vérifier l'affichage de ses commentaires.
4. Créer un compte, puis se connecter.
5. En étant connecté, publier un tutoriel et ajouter un commentaire.
6. Modifier ou supprimer son propre tutoriel.
7. Vérifier qu'un autre utilisateur ne peut pas modifier ou supprimer un tutoriel dont il n'est pas l'auteur.
8. Vérifier les validations des formulaires.
9. Si le Mailer est configuré, vérifier la réception ou la capture des e-mails de bienvenue et de notification d'administration.
10. Commandes utiles
Commande	Utilité
composer install	Installer les dépendances
php bin/console doctrine:database:create	Créer la base de données
php bin/console doctrine:migrations:migrate	Appliquer les migrations
php bin/console doctrine:fixtures:load	Charger les données de test (remplace généralement les données existantes)
php bin/console debug:router	Afficher les routes
php bin/console cache:clear	Vider le cache Symfony
symfony server:start	Démarrer le serveur de développement Symfony
php -S 127.0.0.1:8000 -t public	Démarrer le serveur PHP intégré si Symfony CLI n'est pas disponible


11. Dépannage
Erreur de connexion à la base de données
- Vérifier que MySQL/MariaDB est démarré.
- Vérifier les paramètres DATABASE_URL dans .env.local.
- Vérifier que la base de données et l'utilisateur existent et disposent des permissions nécessaires.
Tables manquantes
Exécuter :
php bin/console doctrine:migrations:migrate
Les Fixtures ne sont pas disponibles
Vérifier que Doctrine Fixtures Bundle est installé :
composer show orm-fixtures
Au besoin, l'installer avec :
composer require --dev orm-fixtures
Problème de cache ou de configuration
Exécuter :
php bin/console cache:clear
Les e-mails ne sont pas capturés
- Vérifier que l'outil de capture d'e-mails est lancé.
- Vérifier le port SMTP et la valeur MAILER_DSN dans .env.local.
- Vérifier la configuration du Mailer et les logs Symfony.
12. Structure générale du projet
DevDocs/
├── assets/             # Assets gérés par le projet, selon sa configuration
├── config/             # Configuration Symfony
├── migrations/         # Migrations Doctrine
├── public/             # Point d'entrée web et assets publics
├── src/
│   ├── Controller/     # Contrôleurs
│   ├── Entity/         # Entités Doctrine
│   ├── Form/           # Formulaires Symfony
│   ├── Repository/     # Repositories Doctrine
│   └── Security/       # Voters et logique de sécurité
├── templates/          # Templates Twig
├── tests/              # Tests, si présents
├── var/                # Cache et logs
├── vendor/             # Dépendances Composer (générées)
├── .env                # Variables d'environnement par défaut
├── .env.local          # Configuration locale (non versionnée)
└── README.md
Cette arborescence est indicative : les dossiers présents dépendent de la configuration et du contenu du projet.
Auteur
Projet réalisé dans le cadre de l'évaluation pratique du framework Symfony – plateforme collaborative DevDocs.
