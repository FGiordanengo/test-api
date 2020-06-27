## Etapes pour utiliser le projet
Cloner le projet  
Faire un composer install

Créer un fichier .env.local avec les identifiants de connexion à la bdd

Commandes à effectuer :  
php bin/console doctrine:database:create  
php bin/console make:migration  
php bin/console doctrine:migrations:migrate  
php bin/console doctrine:fixtures:load

Lancer un serveur local : php -S localhost:8000 -t public

## Liste des URL, et méthodes de l'API :
- Ajouter un élève 

/api/eleve/ajouter méthode POST  
body en format json 
{
    "nom": "TestNom",
    "prenom": "TestPrenom",
    "dateDeNaissance": " 1994-04-20T00:00:01+0000"
}

- Modifier les informations d'un élève (nom, prénom, date de naissance)

/api/eleve/modifier/{id} (id correspond à l'id de l'élève) méthode PUT  
body en format json 
{
    "nom": "TestNom2",
    "prenom": "TestPrenom2",
    "dateDeNaissance": " 1990-04-20T00:00:01+0000"
}

- Supprimer un élève

/api/eleve/supprimer/{id} (id correspond à l'id de l'élève) méthode DELETE

- Ajouter une note à un élève

/api/note/add/{id} (id correspond à l'id de l'élève) méthode POST  
body en format json 
{
    "valeur": 17,
    "matiere": "Informatique"
}

- Récupérer la moyenne de toutes les notes d'un élève

/api/note/moyenne/{id} (id correspond à l'id de l'élève) méthode GET

- Récupérer la moyenne générale de la classe

/api/note/moyenne-generale méthode GET
