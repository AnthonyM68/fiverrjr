# Bienvenue sur notre plateforme d'échange de services !

### Sommaire
    Introduction
*	Objectif du Projet
*	Fonctionnalités Principales
*	Technologies Utilisées
*	Organisation du Projet
*	Installation et Utilisation
*	Contributions
*	Contact

### Introduction
Bienvenue sur ma plateforme d'échange de services dédiée aux entrepreneurs et développeurs de la région Alsace (Haut-Rhin, Bas-Rhin). Mon application offre une expérience simplifiée et efficace pour la mise en relation de clients à la recherche de services spécifiques et de développeurs prêts à les offrir.

### Objectif du Projet
L'objectif principal de mon projet, réalisé dans le cadre de mon examen de fin de formation de développeur web, est de créer une plateforme intuitive et accessible, inspirée des meilleures pratiques d'UI/UX, tout en maintenant un design sobre et clair. Ma mission est de favoriser l'échange de compétences et de services au sein de notre communauté locale.

### Fonctionnalités Principales

*	Catégorisation avancée des services : Thèmes, Catégories, Sous-Catégories jusqu'au niveau de service spécifique.
*	Inscription et Profils Utilisateur : Gestion complète des profils pour développeurs juniors et entrepreneurs.
*	Interface d'Administration : Outil de gestion de contenu pour les administrateurs.
*	Dispositif de Facturation : Système intégré pour la gestion des transactions entre utilisateurs.
*	Accessibilité et Design Responsif : Couleurs simples et claires conformes aux normes d'accessibilité.

### Technologies Utilisées

*	Symfony PHP Framework
*	Doctrine ORM
*	Twig Template Engine
*	Semantic/Semantic-thêmes/Jquery/Jquery-ui/Uikit/Bootstrap CSS Framework
*	MySQL Database

## Organisation du Projet

### Le projet est structuré en plusieurs modules principaux :

*	Backend : Gestion des données et logique métier.
*	Frontend : Interface utilisateur basée sur des templates Twig.
*	Administration : Interface d'administration sécurisée.

## Installation et Utilisation

### Pour installer et utiliser notre application, suivez les instructions suivantes :

* Clonez le repository depuis GitHub.
* Installez les dépendances avec Composer.
* Configurez votre base de données MySQL.
* Lancez le serveur Webpack.
* Lancez le serveur Symfony.

### Contributions
S’agissant d’un projet d’examen la contribution n’est pas envisageable.
### Contact
Pour toute question ou commentaire, n'hésitez pas à nous contacter via notre page GitHub.

### Schémas :

> Réalisation des schémas conceptuels de données :
> ![MCD](https://github.com/AnthonyM68/fiverrjr/blob/master/MCD.jpg)
> ![UML](https://github.com/AnthonyM68/fiverrjr/blob/master/UML.jpg)
> ![MLD](https://github.com/AnthonyM68/fiverrjr/blob/master/MLD.jpg)

### NOTE :
```php
$ git clone https://github.com/AnthonyM68/fiverrjr.git
```
```php
$ composer update 
$ npm install
```
Utilisez la base de données fournie dans le dépot et modifiez le fichier .env si besoin

> DATABASE_URL="mysql://root@127.0.0.1:3306/fiverrjr"

> MAILER_DSN=smtp://localhost:1025

```php
$ npm run dev-server 
```
et depuis une autre console...
```php
$ symfony serve -d
```

<h3 align="center">Languages and Tools:</h3>
<div align="center">
<a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" width="40" height="40"/> </a>
<a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://github.com/AwesomeLogos/logomono/blob/gh-pages/logos/symfony.svg" alt="mysql" width="40" height="40"/> </a>
<a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://github.com/AnthonyM68/fiverrjr/blob/master/js.svg" alt="javascript" width="40" height="40"/> </a>
<a href="https://www.php.net" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="40" height="40"/> </a>
</div>