INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_MOD_SESSION_ORDRE_FORMATION', 'Peut modifier le numéro d''ordre des formations');
INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_VALIDER_FORMULAIRE', 'Peut valider les formulaires');
INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_SESSION_AUTH_VISITEUR', 'Peut permettre aux visiteurs de visiter une formation');

INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_DEPOSER_FICHIER_COLLECTICIEL', 'Peut déposer des fichier dans le collecticiel d''une autre personne');
INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_PEUT_EVALUER_TOUS_DOCS_COLLECTICIEL', 'Peut évaluer tous les documents quel que soit leur état (soumis ou non soumis)');

INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_MOD_SESSION_ORDRE_FORMATION'), '1');

INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '1');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '2');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '3');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '4');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '5');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '6');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '7');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '8');
INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_VALIDER_FORMULAIRE'), '9');

INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ((SELECT `IdPermission` FROM `Permission` WHERE `NomPermis` = 'PERM_SESSION_AUTH_VISITEUR'), '1');
