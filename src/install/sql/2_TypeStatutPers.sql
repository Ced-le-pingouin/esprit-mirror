--
-- TypeStatutPers
--
-- Dernière modification ..: 20/01/2005
-- Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
--                           Filippo PORCO <filippo.porco@umh.ac.be>
--                           Jérôme TOUZE
--
-- Unité de Technologie de l'Education
-- 18, Place du Parc
-- 7000 MONS
--

-- 
-- Contenu de la table `TypeStatutPers`
-- 
INSERT INTO `TypeStatutPers` (`IdStatut`, `NomMasculinStatut`, `NomFemininStatut`, `TxtStatut`) VALUES (1, 'Responsable de plateforme', 'Responsable de plateforme', 'STATUT_PERS_ADMIN'),
(2, 'Responsable de formation', 'Responsable de formation', 'STATUT_PERS_RESPONSABLE_POTENTIEL'),
(3, 'Responsable associé', 'Responsable associée', 'STATUT_PERS_RESPONSABLE'),
(4, 'Concepteur', 'Conceptrice', 'STATUT_PERS_CONCEPTEUR_POTENTIEL'),
(5, 'Concepteur associé', 'Conceptrice associée', 'STATUT_PERS_CONCEPTEUR'),
(6, 'Chercheur', 'Chercheur', 'STATUT_PERS_CHERCHEUR'),
(7, 'Tuteur', 'Tutrice', 'STATUT_PERS_TUTEUR'),
(8, 'Cotuteur', 'Cotutrice', 'STATUT_PERS_COTUTEUR'),
(9, 'Etudiant', 'Etudiante', 'STATUT_PERS_ETUDIANT'),
(10, 'Visiteur', 'Visiteur', 'STATUT_PERS_VISITEUR');
