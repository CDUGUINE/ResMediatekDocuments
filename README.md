<h1>Présentation de l'API</h1>
L'API d'origine est disponible dans le dépôt suivant :<br>
https://github.com/CNED-SLAM/rest_mediatekdocuments.git<br>
Le readme de ce dépôt ne présente que les modifications qui ont été apportées.<br>
Reportez-vous au readme du dépôt d'origine pour en savoir plus sur l'API d'origine.<br>
Le fichier '.env' n'a pas été mis à jour sur ce dépôt. Il contient les données sensibles d'authentification et d'accès à la BDD de l'API d'origine mais pas celle de cette API, pour des raisons de sécurité.<br>
Dans le fichier 'MyAccessBDD.php', de nouvelles fonctions ont été ajoutées pour répondre aux nouvelles demandes de l'application.<br>
Cette API permet d'exécuter des requêtes SQL sur la BDD Mediatek86 créée avec le SGBDR MySQL.<br>
Elle est accessible via une authentification dont les données ont été transmises aux professeurs chargés de la correction.<br>
Sa vocation est de répondre aux demandes de l'application MediaTekDocuments, mise en ligne sur le dépôt :<br>
https://github.com/CDUGUINE/MediaTekDocuments.git

<h1>Installation de l'API en local</h1>
Si besoin, reportez-vous aux indications du dépôt d'origine.<br>
Seuls les identifiants d'authentification ont été modifiés et fournis aux professeurs correcteurs.

<h1>Exploitation de l'API</h1>
Adresse de l'API : http://otsomediatekdocuments.com/rest_mediatekdocuments/ <br>
On utilise les mêmes opérateurs (GET, POST, PUT, DELETE) avec les paramètres décrits dans le dépôt d'origine.<br>

<h1>Les fonctionnalités ajoutées</h1>
Dans MyAccessBDD, plusieurs fonctions ont été ajoutées pour répondre aux nouvelles demandes  de l'application C# MediaTekDocuments :<br>
<ul>
   <li><strong>selectAllCommandesDocument : </strong>récupère la liste des commandes de livres et de DVD(table commandedocument et les tables associées).</li>
   <li><strong>selectAllAbonnement : </strong>récupère la liste des abonnements et les tables associées.</li>
   <li><strong>insertOneCommandeDocument : </strong>insère une commande dans la table commande puis une commandeDocument(livre ou DVD) dans la table comandedocument.</li>
   <li><strong>insertOneAbonnement : </strong>insère une commande dans la table commande puis un abonnement dans la table abonnement.</li>
</ul>
