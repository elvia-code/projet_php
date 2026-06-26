Projet 6 : Gestion des colis d’une coopérative
I) TABLES MANIPULEES PAR L’APPLICATION :
Itineraire(codeit : string, villedep : string, villearr : string)

VOITURE(idvoit : string, Design : String, codeit : string, frais : int)

ENVOYER(idenvoi : int, idvoit : string, colis : string, nomEnvoyeur : string, emailEnvoyeur : string, date-envoi : datetime, frais : int, nomRecepteur : string, contactRecepteur : string)

NB : le format datetime est : AAAA-MM-JJ HH:MM:SS

RECEVOIR(idrecept : int, idenvoi : int, date-recept : datetime)

II) TRAITEMENTS :
Création, Listage et Mise à jour des tables Itineraire, Voiture (CRUD) (4pts)

Création, listage et Mise à jour des tables envoyer et recevoir (6pts)

Recherche d’un colis par son code d’envoi ou sa désignation en utilisant « LIKE » (1pt)

Générer PDF un reçu de client pour un envoi de colis (3pts)

Envoyer automatiquement un mail à l’envoyeur lors d’une réception d’un colis (3pts)

Recherche des colis entre deux dates (2pts)

Recette total accumulé par la coopérative (1pt)

Exemple d’un reçu de Client pour un envoi

Reçu N°332

Date d'envoi : 20 Mai 2023

Nom de l'Envoyeur : NJIVASON Eric

Voiture N°5 / Destination = Fianarantsoa

Colis = Pièces automobiles

Frais : 30.000 Ar

Nom du Récepteur : RAKOTO Jean

Contact du Récepteur : 034 22 764 23