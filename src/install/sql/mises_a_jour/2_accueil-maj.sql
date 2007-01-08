--
-- Modifications apportées à la DB depuis Esprit v2.1
--

-- Permissions

INSERT INTO Permission VALUES (105,'PERM_MOD_ACCUEIL','Peut modifier la page d\'accueil');

INSERT INTO Statut_Permission VALUES (105,1);

-- Avertissement

INSERT INTO `Accueil` VALUES (6,'avert',(SELECT AvertissementLogin FROM Projet LIMIT 1),NULL,NULL,NULL,NULL,1,1,'2006-11-29','2006-11-29');
ALTER TABLE Projet DROP AvertissementLogin;

  
