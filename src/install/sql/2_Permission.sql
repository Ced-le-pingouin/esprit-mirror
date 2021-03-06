INSERT INTO Permission VALUES (1,'PERM_OUTIL_PERMISSION','Peut remplir la table des permissions');
INSERT INTO Permission VALUES (2,'PERM_OUTIL_STATUT','Peut créer le fichier des statuts');
INSERT INTO Permission VALUES (3,'PERM_DESIGNE_RESPONSABLES_SESSION','Désigne les responsables de formation potentiels');
INSERT INTO Permission VALUES (4,'PERM_DESIGNE_CONCEPTEURS','Désigne les concepteurs potentiels');
INSERT INTO Permission VALUES (5,'PERM_VOIR_CONNECTES','Voit la liste des personnes connectées');
INSERT INTO Permission VALUES (6,'PERM_AJT_SESSION','Peut créer une nouvelle session');
INSERT INTO Permission VALUES (7,'PERM_ASS_RESP_SESSION','Peut associer un nouveau responsable à \"sa\" session');
INSERT INTO Permission VALUES (8,'PERM_MOD_TOUTES_SESSIONS','Peut modifier n\'importe quelle session du projet');
INSERT INTO Permission VALUES (9,'PERM_MOD_SESSION','Peut modifier \"sa\" session');
INSERT INTO Permission VALUES (10,'PERM_SUP_TOUTES_SESSIONS','Peut supprimer n\'importe quelle session du projet');
INSERT INTO Permission VALUES (11,'PERM_SUP_SESSION','Peut supprimer \"sa\" session');
INSERT INTO Permission VALUES (12,'PERM_MOD_STATUT_TOUTES_SESSIONS','Peut changer le statut de n\'importe quelle session (ouverte,fermée,invisible)');
INSERT INTO Permission VALUES (13,'PERM_MOD_STATUT_SESSION','Peut changer le statut de \"sa\" session (ouverte,fermée,invisible)');
INSERT INTO Permission VALUES (14,'PERM_VOIR_SESSION_FERMEE','Peut voir et accéder à la session fermée');
INSERT INTO Permission VALUES (15,'PERM_VOIR_SESSION_INV','Peut voir et accéder à la session invisible en préparation (la session, les cours etc)');
INSERT INTO Permission VALUES (16,'PERM_AJT_COURS','Peut créer un nouveau cours');
INSERT INTO Permission VALUES (17,'PERM_ASS_CONCEPT_COURS','Peut associer un autre concepteur à son cours');
INSERT INTO Permission VALUES (18,'PERM_MOD_TOUS_COURS','Peut modifier n\'importe quel cours');
INSERT INTO Permission VALUES (19,'PERM_MOD_COURS','Peut modifier \"son\" cours');
INSERT INTO Permission VALUES (20,'PERM_SUP_TOUS_COURS','Peut supprimer n\'importe quel cours');
INSERT INTO Permission VALUES (21,'PERM_SUP_COURS','Peut supprimer \"son\" cours');
INSERT INTO Permission VALUES (22,'PERM_MOD_STATUT_TOUS_COURS','Peut changer le statut de n\'importe quel cours (ouverte,fermée,invisible)');
INSERT INTO Permission VALUES (23,'PERM_MOD_STATUT_COURS','Peut changer le statut de \"son\" cours (ouverte,fermée,invisible)');
INSERT INTO Permission VALUES (24,'PERM_VOIR_COURS_FERME','Peut voir et accéder au cours fermé');
INSERT INTO Permission VALUES (25,'PERM_VOIR_COURS_INV','Peut voir et accéder au cours invisible');
INSERT INTO Permission VALUES (26,'PERM_AJT_RUBRIQUE','Peut créer une nouvelle rubrique/unité');
INSERT INTO Permission VALUES (27,'PERM_MOD_RUBRIQUE','Peut modifier une rubrique/unité');
INSERT INTO Permission VALUES (28,'PERM_SUP_RUBRIQUE','Peut supprimer une rubrique/unité');
INSERT INTO Permission VALUES (29,'PERM_MOD_STATUT_RUBRIQUE','Peut changer le statut d\'une rubrique/unité (ouvert, fermée, invisible)');
INSERT INTO Permission VALUES (30,'PERM_VOIR_RUBRIQUE_FERMEE','Peut voir et accéder à la rubrique/unité fermée');
INSERT INTO Permission VALUES (31,'PERM_VOIR_RUBRIQUE_INV','Peut voir et accéder à la rubrique/unité invisible');
INSERT INTO Permission VALUES (32,'PERM_AJT_BLOC','Peut créer un nouveau bloc');
INSERT INTO Permission VALUES (33,'PERM_MOD_BLOC','Peut modifier un bloc');
INSERT INTO Permission VALUES (34,'PERM_SUP_BLOC','Peut supprimer un bloc');
INSERT INTO Permission VALUES (35,'PERM_MOD_STATUT_BLOC','Peut changer le statut d\'un bloc (ouvert, fermée, invisible)');
INSERT INTO Permission VALUES (36,'PERM_VOIR_BLOC_FERME','Peut voir et accéder au bloc fermé');
INSERT INTO Permission VALUES (37,'PERM_VOIR_BLOC_INV','Peut voir et accéder au bloc invisible');
INSERT INTO Permission VALUES (38,'PERM_AJT_ELEMENT_ACTIF','Peut créer un nouvel élément actif');
INSERT INTO Permission VALUES (39,'PERM_MOD_ELEMENT_ACTIF','Peut modifier un élément actif');
INSERT INTO Permission VALUES (40,'PERM_SUP_ELEMENT_ACTIF','Peut supprimer un élément actif');
INSERT INTO Permission VALUES (41,'PERM_MOD_STATUT_ELEMENT_ACTIF','Peut changer le statut d\'un élément actif (ouvert, fermée, invisible)');
INSERT INTO Permission VALUES (42,'PERM_VOIR_ELEMENT_ACTIF_FERME','Peut voir et accéder à l\'élément actif fermé');
INSERT INTO Permission VALUES (43,'PERM_VOIR_ELEMENT_ACTIF_INV','Peut voir et accéder à l\'élément actif invisible');
INSERT INTO Permission VALUES (44,'PERM_AJT_ETUDIANT','Peut enregistrer un nouvel étudiant');
INSERT INTO Permission VALUES (45,'PERM_ASS_ETUDIANT_COURS','Peut associer un étudiant à un cours');
INSERT INTO Permission VALUES (46,'PERM_SUP_ETUDIANT','Peut supprimer un étudiant');
INSERT INTO Permission VALUES (47,'PERM_MOD_INFOS_ETUDIANT','Peut modifier les infos relatives à un étudiant');
INSERT INTO Permission VALUES (48,'PERM_AJT_EQUIPE','Peut créer une nouvelle équipe');
INSERT INTO Permission VALUES (49,'PERM_SUP_EQUIPE','Peut supprimer une équipe');
INSERT INTO Permission VALUES (50,'PERM_ASS_ETUDIANT_EQUIPE','Peut associer un étudiant à une équipe');
INSERT INTO Permission VALUES (51,'PERM_MOD_COMPOSITION_EQUIPES','Peut modifier la composition des équipes');
INSERT INTO Permission VALUES (52,'PERM_VOIR_ETUDIANTS_GROUPE','Voit l\'ensemble des étudiants du groupe');
INSERT INTO Permission VALUES (53,'PERM_VOIR_ETUDIANTS_EQUIPES','Voit les étudiants ou les équipes co-tutoré(e)s');
INSERT INTO Permission VALUES (54,'PERM_EVALUER_ETUDIANTS','Peut évaluer les étudiants qu\'il voit');
INSERT INTO Permission VALUES (55,'PERM_AJT_FORUM','Peut créer un nouveau forum');
INSERT INTO Permission VALUES (56,'PERM_SUP_FORUM','Peut supprimer son forum');
INSERT INTO Permission VALUES (57,'PERM_AJT_SUJET_FORUM','Peut créer un nouveau sujet');
INSERT INTO Permission VALUES (58,'PERM_SUP_SUJET_FORUM','Peut supprimer son sujet');
INSERT INTO Permission VALUES (59,'PERM_AJT_MESSAGE_FORUM','Peut créer un nouveau message');
INSERT INTO Permission VALUES (60,'PERM_SUP_MESSAGE_FORUM','Peut supprimer son message');
INSERT INTO Permission VALUES (61,'PERM_MODERER_FORUMS_SESSION','Modère l\'ensemble des forum de la session');
INSERT INTO Permission VALUES (62,'PERM_MODERER_FORUM','Modère les forum de \"son\" cours');
INSERT INTO Permission VALUES (63,'PERM_ASS_TUTEUR_COURS','Peut associer un tuteur à un cours');
INSERT INTO Permission VALUES (64,'PERM_SUP_TUTEUR','Peut supprimer un tuteur');
INSERT INTO Permission VALUES (65,'PERM_MOD_ASS_TUTEUR_COURS','Peut modifier l\'association tuteur/cours');
INSERT INTO Permission VALUES (66,'PERM_ASS_COTUTEUR_EQUIPE_ETUDIANT','Peut associer un co-tuteur à une équipe ou un étudiant');
INSERT INTO Permission VALUES (67,'PERM_SUP_COTUTEUR','Peut supprimer un co-tuteur');
INSERT INTO Permission VALUES (68,'PERM_MOD_COTUTEUR_EQUIPE_ETUDIANT','Peut modifier l\'association co-tuteur/équipe/étudiant');
INSERT INTO Permission VALUES (69,'PERM_OUTIL_EXPORT_TABLE_EVENEMENT','Peut exporter la trace des connexions');
INSERT INTO Permission VALUES (70,'PERM_TELECHARGER_DOC_GALERIE','Peut télécharger les documents de la galerie');
INSERT INTO Permission VALUES (71,'PERM_OUTIL_CONSOLE','Peut voir la console des erreurs');
INSERT INTO Permission VALUES (72,'PERM_OUTIL_CORBEILLE','Peut effacer des formations qui se trouvent dans la corbeille');
INSERT INTO Permission VALUES (73,'PERM_OUTIL_EXPORT_TABLE_PERSONNE','Peut exporter la liste des personnes');
INSERT INTO Permission VALUES (74,'PERM_OUTIL_ECONCEPT','Peut utiliser l\'outil eConcept');
INSERT INTO Permission VALUES (75,'PERM_OUTIL_INSCRIPTION','Peut utiliser l\'outil inscription');
INSERT INTO Permission VALUES (76,'PERM_OUTIL_EQUIPE','Peut utiliser l\'outil pour gérer les équipes');
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
INSERT INTO Permission VALUES (88,'PERM_OUTIL_FORMULAIRE','Peut accèder à l\'outil formulaire');
INSERT INTO Permission VALUES (89,'PERM_MOD_FORMULAIRES','Peut créer et modifier ses formulaires');
INSERT INTO Permission VALUES (90,'PERM_MOD_TOUS_FORMULAIRES','Peut modifier/supprimer/... tous les formulaires');
INSERT INTO Permission VALUES (91,'PERM_EVALUER_FORMULAIRE','Peut évaluer les formulaires de son cours');
INSERT INTO Permission VALUES (92,'PERM_COPIE_COURRIEL_FORUM','Peut recevoir des copies des sujets du forum par courriel');
INSERT INTO Permission VALUES (93,'PERM_COURRIEL_FORUM_POUR_TOUS','Peut envoyer un courriel à partir d\'un forum pour tous');
INSERT INTO Permission VALUES (94,'PERM_COURRIEL_FORUM_EQUIPE_ISOLEE','Peut envoyer un courriel à partir d\'un forum par équipe isolée');
INSERT INTO Permission VALUES (95,'PERM_COURRIEL_FORUM_EQUIPE_INTERCONNECTEE','Peut envoyer un courriel à partir d\'un forum par équipe interconnectée');
INSERT INTO Permission VALUES (96,'PERM_COURRIEL_FORUM_EQUIPE_COLLABORANTE','Peut envoyer un courriel à partir d\'un forum par équipe collaborante');
INSERT INTO Permission VALUES (97,'PERM_OUTIL_ENVOI_COURRIEL','Peut envoyer un courriel à toutes les personnes inscrites à la plate-forme');
INSERT INTO Permission VALUES (98,'PERM_UTILISER_BOITE_COURRIELLE_PC','Peut envoyer un courriel à partir de sa boîte courrielle de son ordinateur (niveau d\'un forum)');
INSERT INTO Permission VALUES (99,'PERM_EVALUER_COLLECTICIEL','Peut évaluer les documents des collecticiels');
INSERT INTO Permission VALUES (100,'PERM_VOIR_TOUS_COLLECTICIELS','Peut voir tous les collecticiels d\'une formation');
INSERT INTO Permission VALUES (101,'PERM_CLASSER_FORMATIONS','Peut Classer ses formations dans des dossiers');
INSERT INTO Permission VALUES (102,'PERM_COMPOSER_GALERIE','Peut associer des documents à la galerie');
INSERT INTO Permission VALUES (103,'PERM_FORUM_EXPORTER_CSV','Peut exporter un forum vers un fichier csv');
INSERT INTO Permission VALUES (104,'PERM_OUTIL_TABLEAU_DE_BORD','Peut accèder au tableau de bord');
INSERT INTO Permission VALUES (105,'PERM_MOD_ACCUEIL','Peut modifier la page d\'accueil');
