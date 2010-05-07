CREATE TABLE `SousActiv_Formulaire_Options` (
    `idFormOption` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `idSousActiv` INT( 10 ) UNSIGNED NOT NULL UNIQUE KEY,
    `AffichageEtudiant` ENUM( 'inline', 'popup' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'popup',
    `AffichageTuteur` ENUM( 'inline', 'popup' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'popup',
    `ModeEvaluation` ENUM( 'autotuteur', 'auto', 'tuteur', 'sans' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'auto'
) ;
