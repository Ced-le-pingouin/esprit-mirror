# MySQL dump 7.1
#
# Host: localhost    Database: ipfhainaut_dev
#--------------------------------------------------------
# Server version	3.23.58-log

#
# Dumping data for table 'Permission'
#

INSERT INTO Permission VALUES (1,'PERM_OUTIL_PERMISSION','Peut remplir la table des permissions');
INSERT INTO Permission VALUES (2,'PERM_OUTIL_STATUT','Peut cr�er le fichier des statuts');
INSERT INTO Permission VALUES (3,'PERM_DESIGNE_RESPONSABLES_SESSION','Peut d�signe les responsables de formation potentiels');
INSERT INTO Permission VALUES (4,'PERM_DESIGNE_CONCEPTEURS','Peut d�signer les concepteurs potentiels');
INSERT INTO Permission VALUES (5,'PERM_VOIR_CONNECTES','Voit la liste des personnes connect�es');
INSERT INTO Permission VALUES (6,'PERM_AJT_SESSION','Peut cr�er une nouvelle session');
INSERT INTO Permission VALUES (7,'PERM_ASS_RESP_SESSION','Peut associer un nouveau responsable � \"sa\" session');
INSERT INTO Permission VALUES (8,'PERM_MOD_TOUTES_SESSIONS','Peut modifier n\'importe quelle session du projet');
INSERT INTO Permission VALUES (9,'PERM_MOD_SESSION','Peut modifier \"sa\" session');
INSERT INTO Permission VALUES (10,'PERM_SUP_TOUTES_SESSIONS','Peut supprimer n\'importe quelle session du projet');
INSERT INTO Permission VALUES (11,'PERM_SUP_SESSION','Peut supprimer \"sa\" session');
INSERT INTO Permission VALUES (12,'PERM_MOD_STATUT_TOUTES_SESSIONS','Peut changer le statut de n\'importe quelle session (ouverte,ferm�e,invisible)');
INSERT INTO Permission VALUES (13,'PERM_MOD_STATUT_SESSION','Peut changer le statut de \"sa\" session (ouverte,ferm�e,invisible)');
INSERT INTO Permission VALUES (14,'PERM_VOIR_SESSION_FERMEE','Peut voir et acc�der � la session ferm�e');
INSERT INTO Permission VALUES (15,'PERM_VOIR_SESSION_INV','Peut voir et acc�der � la session invisible en pr�paration (la session, les cours etc)');
INSERT INTO Permission VALUES (16,'PERM_AJT_COURS','Peut cr�er un nouveau cours');
INSERT INTO Permission VALUES (17,'PERM_ASS_CONCEPT_COURS','Peut associer un autre concepteur � son cours');
INSERT INTO Permission VALUES (18,'PERM_MOD_TOUS_COURS','Peut modifier n\'importe quel cours');
INSERT INTO Permission VALUES (19,'PERM_MOD_COURS','Peut modifier \"son\" cours');
INSERT INTO Permission VALUES (20,'PERM_SUP_TOUS_COURS','Peut supprimer n\'importe quel cours');
INSERT INTO Permission VALUES (21,'PERM_SUP_COURS','Peut supprimer \"son\" cours');
INSERT INTO Permission VALUES (22,'PERM_MOD_STATUT_TOUS_COURS','Peut changer le statut de n\'importe quel cours (ouverte,ferm�e,invisible)');
INSERT INTO Permission VALUES (23,'PERM_MOD_STATUT_COURS','Peut changer le statut de \"son\" cours (ouverte,ferm�e,invisible)');
INSERT INTO Permission VALUES (24,'PERM_VOIR_COURS_FERME','Peut voir et acc�der au cours ferm�');
INSERT INTO Permission VALUES (25,'PERM_VOIR_COURS_INV','Peut voir et acc�der au cours invisible');
INSERT INTO Permission VALUES (26,'PERM_AJT_RUBRIQUE','Peut cr�er une nouvelle rubrique/unit�');
INSERT INTO Permission VALUES (27,'PERM_MOD_RUBRIQUE','Peut modifier une rubrique/unit�');
INSERT INTO Permission VALUES (28,'PERM_SUP_RUBRIQUE','Peut supprimer une rubrique/unit�');
INSERT INTO Permission VALUES (29,'PERM_MOD_STATUT_RUBRIQUE','Peut changer le statut d\'une rubrique/unit� (ouvert, ferm�e, invisible)');
INSERT INTO Permission VALUES (30,'PERM_VOIR_RUBRIQUE_FERMEE','Peut voir et acc�der � la rubrique/unit� ferm�e');
INSERT INTO Permission VALUES (31,'PERM_VOIR_RUBRIQUE_INV','Peut voir et acc�der � la rubrique/unit� invisible');
INSERT INTO Permission VALUES (32,'PERM_AJT_BLOC','Peut cr�er un nouveau bloc');
INSERT INTO Permission VALUES (33,'PERM_MOD_BLOC','Peut modifier un bloc');
INSERT INTO Permission VALUES (34,'PERM_SUP_BLOC','Peut supprimer un bloc');
INSERT INTO Permission VALUES (35,'PERM_MOD_STATUT_BLOC','Peut changer le statut d\'un bloc (ouvert, ferm�e, invisible)');
INSERT INTO Permission VALUES (36,'PERM_VOIR_BLOC_FERME','Peut voir et acc�der au bloc ferm�');
INSERT INTO Permission VALUES (37,'PERM_VOIR_BLOC_INV','Peut voir et acc�der au bloc invisible');
INSERT INTO Permission VALUES (38,'PERM_AJT_ELEMENT_ACTIF','Peut cr�er un nouvel �l�ment actif');
INSERT INTO Permission VALUES (39,'PERM_MOD_ELEMENT_ACTIF','Peut modifier un �l�ment actif');
INSERT INTO Permission VALUES (40,'PERM_SUP_ELEMENT_ACTIF','Peut supprimer un �l�ment actif');
INSERT INTO Permission VALUES (41,'PERM_MOD_STATUT_ELEMENT_ACTIF','Peut changer le statut d\'un �l�ment actif (ouvert, ferm�e, invisible)');
INSERT INTO Permission VALUES (42,'PERM_VOIR_ELEMENT_ACTIF_FERME','Peut voir et acc�der � l\'�l�ment actif ferm�');
INSERT INTO Permission VALUES (43,'PERM_VOIR_ELEMENT_ACTIF_INV','Peut voir et acc�der � l\'�l�ment actif invisible');
INSERT INTO Permission VALUES (44,'PERM_AJT_ETUDIANT','Peut enregistrer un nouvel �tudiant');
INSERT INTO Permission VALUES (45,'PERM_ASS_ETUDIANT_COURS','Peut associer un �tudiant � un cours');
INSERT INTO Permission VALUES (46,'PERM_SUP_ETUDIANT','Peut supprimer un �tudiant');
INSERT INTO Permission VALUES (47,'PERM_MOD_INFOS_ETUDIANT','Peut modifier les infos relatives � un �tudiant');
INSERT INTO Permission VALUES (48,'PERM_AJT_EQUIPE','Peut cr�er une nouvelle �quipe');
INSERT INTO Permission VALUES (49,'PERM_SUP_EQUIPE','Peut supprimer une �quipe');
INSERT INTO Permission VALUES (50,'PERM_ASS_ETUDIANT_EQUIPE','Peut associer un �tudiant � une �quipe');
INSERT INTO Permission VALUES (51,'PERM_MOD_COMPOSITION_EQUIPES','Peut modifier la composition des �quipes');
INSERT INTO Permission VALUES (52,'PERM_VOIR_ETUDIANTS_GROUPE','Voit l\'ensemble des �tudiants du groupe');
INSERT INTO Permission VALUES (53,'PERM_VOIR_ETUDIANTS_EQUIPES','Voit les �tudiants ou les �quipes co-tutor�(e)s');
INSERT INTO Permission VALUES (54,'PERM_EVALUER_ETUDIANTS','Peut �valuer les �tudiants qu\'il voit');
INSERT INTO Permission VALUES (55,'PERM_AJT_FORUM','Peut cr�er un nouveau forum');
INSERT INTO Permission VALUES (56,'PERM_SUP_FORUM','Peut supprimer son forum');
INSERT INTO Permission VALUES (57,'PERM_AJT_SUJET_FORUM','Peut cr�er un nouveau sujet');
INSERT INTO Permission VALUES (58,'PERM_SUP_SUJET_FORUM','Peut supprimer son sujet');
INSERT INTO Permission VALUES (59,'PERM_AJT_MESSAGE_FORUM','Peut cr�er un nouveau message');
INSERT INTO Permission VALUES (60,'PERM_SUP_MESSAGE_FORUM','Peut supprimer son message');
INSERT INTO Permission VALUES (61,'PERM_MODERER_FORUMS_SESSION','Mod�re l\'ensemble des forum de la session');
INSERT INTO Permission VALUES (62,'PERM_MODERER_FORUM','Mod�re les forum de \"son\" cours');
INSERT INTO Permission VALUES (63,'PERM_ASS_TUTEUR_COURS','Peut associer un tuteur � un cours');
INSERT INTO Permission VALUES (64,'PERM_SUP_TUTEUR','Peut supprimer un tuteur');
INSERT INTO Permission VALUES (65,'PERM_MOD_ASS_TUTEUR_COURS','Peut modifier l\'association tuteur/cours');
INSERT INTO Permission VALUES (66,'PERM_ASS_COTUTEUR_EQUIPE_ETUDIANT','Peut associer un co-tuteur � une �quipe ou un �tudiant');
INSERT INTO Permission VALUES (67,'PERM_SUP_COTUTEUR','Peut supprimer un co-tuteur');
INSERT INTO Permission VALUES (68,'PERM_MOD_COTUTEUR_EQUIPE_ETUDIANT','Peut modifier l\'association co-tuteur/�quipe/�tudiant');
INSERT INTO Permission VALUES (69,'PERM_OUTIL_EXPORT_TABLE_EVENEMENT','Peut exporter la trace des connexions');
INSERT INTO Permission VALUES (70,'PERM_TELECHARGER_DOC_GALERIE','Peut t�l�charger les documents de la galerie');
INSERT INTO Permission VALUES (71,'PERM_OUTIL_CONSOLE','Peut voir la console des erreurs');
INSERT INTO Permission VALUES (72,'PERM_OUTIL_CORBEILLE','Peut effacer des formations qui se trouvent dans la corbeille');
INSERT INTO Permission VALUES (73,'PERM_OUTIL_EXPORT_TABLE_PERSONNE','Peut exporter la liste des personnes');
INSERT INTO Permission VALUES (74,'PERM_OUTIL_ECONCEPT','Peut utiliser l\'outil eConcept');
INSERT INTO Permission VALUES (75,'PERM_OUTIL_INSCRIPTION','Peut utiliser l\'outil inscription');
INSERT INTO Permission VALUES (76,'PERM_OUTIL_EQUIPE','Peut utiliser l\'outil pour g�rer les �quipes');
INSERT INTO Permission VALUES (77,'PERM_MOD_FORUM','Peut modifier son forum');
INSERT INTO Permission VALUES (78,'PERM_MOD_FORUMS','Peut modifier tous les forums');
INSERT INTO Permission VALUES (79,'PERM_SUP_FORUMS','Peut supprimer tous les forums');
INSERT INTO Permission VALUES (80,'PERM_MOD_MESSAGE_FORUM','Peut modifier son message');
INSERT INTO Permission VALUES (81,'PERM_MOD_MESSAGES_FORUM','Peut modifier tous les messages de son forum');
INSERT INTO Permission VALUES (82,'PERM_MOD_MESSAGES_FORUMS','Peut ajouter/modifier/supprimer n\'importe quel message de n\'importe quel forum de la plate-forme');
INSERT INTO Permission VALUES (83,'PERM_MOD_SUJET_FORUM','Peut modifier son sujet');
INSERT INTO Permission VALUES (84,'PERM_MOD_SUJETS_FORUM','Peut modifier tous les sujets de son forum');
INSERT INTO Permission VALUES (85,'PERM_MOD_SUJETS_FORUMS','Peut modifier tous les sujets de tous les forums');
INSERT INTO Permission VALUES (86,'PERM_SUP_SUJETS_FORUM','Peut supprimer tous les sujets de son forum');
INSERT INTO Permission VALUES (87,'PERM_SUP_SUJETS_FORUMS','Peut supprimer tous les sujets de tous les forums');
INSERT INTO Permission VALUES (88,'PERM_OUTIL_FORMULAIRE','Peut acc�der � l\'outil formulaire');
INSERT INTO Permission VALUES (89,'PERM_MOD_FORMULAIRES','Peut cr�er et modifier ses formulaires');
INSERT INTO Permission VALUES (90,'PERM_MOD_TOUS_FORMULAIRES','Peut modifier/supprimer/... tous les formulaires');
INSERT INTO Permission VALUES (91,'PERM_EVALUER_FORMULAIRE','Peut �valuer les formulaires de son cours');
INSERT INTO Permission VALUES (92,'PERM_COPIE_COURRIEL_FORUM','Peut recevoir des copies des sujets du forum par courriel');
INSERT INTO Permission VALUES (93,'PERM_COURRIEL_FORUM_POUR_TOUS','Peut envoyer un courriel � partir d\'un forum pour tous');
INSERT INTO Permission VALUES (94,'PERM_COURRIEL_FORUM_EQUIPE_ISOLEE','Peut envoyer un courriel � partir d\'un forum par �quipe isol�e');
INSERT INTO Permission VALUES (95,'PERM_COURRIEL_FORUM_EQUIPE_INTERCONNECTEE','Peut envoyer un courriel � partir d\'un forum par �quipe interconnect�e');
INSERT INTO Permission VALUES (96,'PERM_COURRIEL_FORUM_EQUIPE_COLLABORANTE','Peut envoyer un courriel � partir d\'un forum par �quipe collaborante');
INSERT INTO Permission VALUES (97,'PERM_OUTIL_ENVOI_COURRIEL','Peut envoyer un courriel � toutes les personnes inscrites � la plate-forme');
INSERT INTO Permission VALUES (98,'PERM_UTILISER_BOITE_COURRIELLE_PC','Peut envoyer un courriel � partir de sa bo�te courrielle de son ordinateur (niveau d\'un forum)');
INSERT INTO Permission VALUES (99,'PERM_EVALUER_COLLECTICIEL','Peut �valuer les documents des collecticiels');
INSERT INTO Permission VALUES (100,'PERM_VOIR_TOUS_COLLECTICIELS','Peut voir tous les collecticiels d\'une formation');
INSERT INTO Permission VALUES (101,'PERM_CLASSER_FORMATIONS','Peut Classer ses formations dans des dossiers');
INSERT INTO Permission VALUES (102,'PERM_COMPOSER_GALERIE','Peut associer des documents � la galerie');
INSERT INTO Permission VALUES (103,'PERM_FORUM_EXPORTER_CSV','Peut exporter un forum vers un fichier csv');

