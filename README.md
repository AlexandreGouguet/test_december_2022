# test_december_2022

Le projet utilise docker, il suffit donc de créer un dossier "mysql" à la racine du projet (au même niveau que php et nginx) et d'éxécuter ensuite la commande :
docker-compose up -d --build

Une fois fait, il faut faire les commandes suivantes depuis le conteneur php :
composer install
php bin/console doctrine:migration:migrate

Soit via les commandes :
docker exec -it php bash
docker exec -it php database

Ou via en lançant le terminal via Docker desktop

Il suffit ensuite de lancer l'application depuis http://localhost:8080
