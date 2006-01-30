<?php

/*
** Fichier ................: infos-menu.php
** Description ............: 
** Date de création .......: 10/05/2005
** Dernière modification ..: 10/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// ---------------------
$iNbAdmins = $oProjet->initAdministrateurs();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("infos.tpl");

$oTpl->remplacer("{plateforme.nom}",htmlentities($oProjet->retNom()));
$oTpl->remplacer("{plateforme.courriel}",htmlentities($oProjet->retEmail()));
$oTpl->remplacer("{chat.port}",$oProjet->retNumPortChat());
$oTpl->remplacer("{awareness.port}",$oProjet->retNumPortAwareness());

// {{{ Liste des administrateurs
$oBlocAdmin = new Tpl_Block("BLOCK_ADMINISTRATEUR",$oTpl);

if ($iNbAdmins > 0)
{
	$oBlocAdmin->beginLoop();
	
	foreach ($oProjet->aoAdmins as $oAdmin)
	{
		$oBlocAdmin->nextLoop();
		$oBlocAdmin->remplacer("{personne.nom}",$oAdmin->retNom());
		$oBlocAdmin->remplacer("{personne.prenom}",$oAdmin->retPrenom());
		$oBlocAdmin->remplacer("{personne.courriel}",$oAdmin->retEmail());
	}
	
	$oBlocAdmin->afficher();
}
else
	$oBlocAdmin->effacer();
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

