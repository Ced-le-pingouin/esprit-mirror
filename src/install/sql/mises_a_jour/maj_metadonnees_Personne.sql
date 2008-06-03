ALTER TABLE Personne
ADD CreaSrc ENUM( 'aucun', 'esprit', 'agalan' ) NOT NULL DEFAULT 'aucun' 
    COMMENT 'référentiel source : esprit=reprise valeurs antérieures, agalan=annuaire Grenoble',
ADD CreaDate DATETIME NULL 
    COMMENT 'date de création',
ADD CreaResp INT UNSIGNED NULL 
    COMMENT 'id utilisateur responsable de la création',
ADD ModifDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    COMMENT 'date de dernière modification',
ADD ModifResp INT UNSIGNED NULL 
    COMMENT 'id utilisateur responsable de la modification';
