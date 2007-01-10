--
-- Modifications apportées à la DB depuis Esprit v2.1
--

-- Permissions

INSERT INTO Permission (NomPermis,DescrPermis) VALUES ("PERM_EDITER_ACCUEIL","Peut �diter la page d'accueil") ;
INSERT INTO Statut_Permission (IdPermission,IdStatut) VALUES ((SELECT IdPermission FROM Permission WHERE NomPermis='PERM_EDITER_ACCUEIL'),1) ;


-- Avertissement

INSERT INTO `Accueil` VALUES (6,'avert',(SELECT AvertissementLogin FROM Projet LIMIT 1),NULL,NULL,NULL,NULL,1,1,'2006-11-29','2006-11-29');
ALTER TABLE Projet DROP AvertissementLogin;

  
