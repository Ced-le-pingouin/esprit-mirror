--
-- Modifications apportées à la DB depuis Esprit v2.1
--

-- Permissions

INSERT INTO Permission (NomPermis,DescrPermis) VALUES ("PERM_MOD_ACCUEIL","Peut �diter la page d'accueil") ;
INSERT INTO Statut_Permission (IdPermission,IdStatut) VALUES ((SELECT IdPermission FROM Permission WHERE NomPermis='PERM_MOD_ACCUEIL'),1) ;


-- Avertissement

INSERT INTO `Accueil` (TypeContenu,Texte,DateCreation) VALUES ('avert',(SELECT AvertissementLogin FROM Projet LIMIT 1),CURRENT_DATE());
ALTER TABLE Projet DROP AvertissementLogin;

  
