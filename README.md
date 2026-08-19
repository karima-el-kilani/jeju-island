# Jeju Island

Portail des activités nature et patrimoine de l'île de Jeju.

Projet WordPress — parcours ENI « Le développement d'applications à l'aide d'un CMS », niveau D2WM.

## Contenu du dépôt

- `wp-content/themes/jeju-nature/` : le thème du site
- `wp-content/plugins/jeju-nature-reservations/` : l'extension qui gère les activités et les demandes de réservation
- `wp-content/uploads/` : les médias du site
- `documentation/` : documentation technique et export SQL

Le dépôt est initialisé à la racine du site et ne versionne que le code écrit pour ce projet. Le noyau de WordPress et le fichier `wp-config.php` en sont exclus.

## Environnement

- WampServer (Windows) : Apache, MySQL, PHP
- Site local : http://www.jeju-nature.local
- Base de données : `eni-jejunature`, préfixe des tables `jn_`