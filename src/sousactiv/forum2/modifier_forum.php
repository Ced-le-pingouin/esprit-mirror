<?php

/*
** Fichier ................: modifier_forum.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 08/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Gestion
// ---------------------
if (isset($HTTP_POST_VARS["modaliteFenetre"]))
{
	$url_sModaliteFenetre = $HTTP_POST_VARS["modaliteFenetre"];
	$url_iIdForum       = $HTTP_POST_VARS["idForum"];
	$url_iIdForumParent = $HTTP_POST_VARS["idForumParent"];
	
	if ($url_sModaliteFenetre == "ajouter" || $url_sModaliteFenetre == "modifier")
	{
		$url_sNom = trim($HTTP_POST_VARS["nom_forum"]);
		$url_iModalite = $HTTP_POST_VARS["modalite_forum"];
		$url_iStatut = $HTTP_POST_VARS["statut_forum"];
		$url_sAccessibleVisiteurs = ($HTTP_POST_VARS["accessible_visiteurs"] == "on" ? "1" : "0");
		
		// L'auteur du forum
		$iIdPers = $oProjet->oUtilisateur->retId();
		
		if (strlen($url_sNom) > 0)
		{
			if ($url_sModaliteFenetre == "ajouter")
			{
				// Ajouter un sous-forum
				$oForum = new CForum($oProjet->oBdd);
				$url_iIdForumParent = $oForum->ajouter($url_sNom
					,$url_iModalite
					,$url_iStatut
					,$url_sAccessibleVisiteurs
					,0
					,0
					,0
					,$url_iIdForumParent
					,$iIdPers);
			}
			else
			{
				// Modifier le sous-forum
				$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
				$oForum->defNom($url_sNom);
				$oForum->defModalite($url_iModalite);
				$oForum->defStatut($url_iStatut);
				$oForum->defAccessibleVisiteurs($url_sAccessibleVisiteurs);
				$oForum->enregistrer();
			}
		}
	}
	else if ($url_sModaliteFenetre == "supprimer")
	{
		$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
		$oForum->verrouillerTables();
		$oForum->supprimer();
		$oForum->verrouillerTables(FALSE);
		$oForum = NULL;
		
		// Lorsqu'on supprime un sujet nous devons afficher le premier sujet
		// de la liste
		$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
		$oSujetForum = $oForum->retPremierSujet();
		if (is_object($oSujetForum))
			$url_iIdForumParent = $oSujetForum->retId();
		else
			$url_iIdForumParent = 0;
	}
	
	echo "<html>\n"
		."<head>\n"
		."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
		."function fermer() { top.opener.rafraichir_liste_forums(); top.close(); }\n"
		."//--></script>\n"
		."</head>\n"
		."<body onload=\"fermer()\"></body>\n"
		."</html>\n";
	
	exit();
}

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($HTTP_GET_VARS["modaliteFenetre"]) ? NULL : $HTTP_GET_VARS["modaliteFenetre"]);
$url_iIdForum       = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);
$url_iIdForumParent = (empty($HTTP_GET_VARS["idForumParent"]) ? 0 : $HTTP_GET_VARS["idForumParent"]);

$oTpl = new Template("modifier_forum.tpl");
$oBlock_Forum = new TPL_Block("BLOCK_FORUM",$oTpl);

$oTpl->remplacer("{fenetre->modalite}",$url_sModaliteFenetre);
$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{forum_parent->id}",$url_iIdForumParent);

$oSet_Modifier_Forum  = $oTpl->defVariable("SET_MODIFIER_FORUM");
$oSet_Titre_Forum     = $oTpl->defVariable("SET_ONGLET_FORUM");
$oSet_Supprimer_Forum = $oTpl->defVariable("SET_SUPPRIMER_FORUM");

// Onglet
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSet_Onglet = $oTplOnglet->defVariable("SET_ONGLET");

if ($url_sModaliteFenetre == "ajouter")
{
	$oBlock_Forum->ajouter($oSet_Modifier_Forum);
	
	// Onglet "Forum"
	$oBlock_Forum->remplacer("{onglet->forum}",$oSet_Onglet);
	$oBlock_Forum->remplacer("{onglet->titre}","Forum");
	$oBlock_Forum->remplacer("{onglet->texte}",$oSet_Titre_Forum);
	
	// Titre
	$oBlock_Forum->remplacer("{titre->valeur}","");
	
	// Modalité
	$oBlock_Forum->remplacer("{modalite->parent->selectionner}"," selected");
	$oBlock_Forum->remplacer("{modalite->tous->selectionner}",NULL);
	$oBlock_Forum->remplacer("{modalite->equipe->selectionner}",NULL);
	
	// Statut
	$oBlock_Forum->remplacer("{statut->ouvert->id}",STATUT_OUVERT);
	$oBlock_Forum->remplacer("{statut->consultable->id}",STATUT_LECTURE_SEULE);
	$oBlock_Forum->remplacer("{statut->fermer->id}",STATUT_FERME);
	$oBlock_Forum->remplacer("{statut->invisible->id}",STATUT_INVISIBLE);
	
	$oBlock_Forum->remplacer("{statut->ouvert->selectionner}"," selected");
	$oBlock_Forum->remplacer("{statut->consultable->selectionner}",NULL);
	$oBlock_Forum->remplacer("{statut->fermer->selectionner}",NULL);
	$oBlock_Forum->remplacer("{statut->invisible->selectionner}",NULL);
	
	// Accessible aux visiteurs
	$oBlock_Forum->remplacer("{accessible_visiteurs->selectionner}"," checked");
}
else if ($url_sModaliteFenetre == "modifier")
{
	$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdForumParent);
	
	$oBlock_Forum->ajouter($oSet_Modifier_Forum);
	
	// Onglet "Forum"
	$oBlock_Forum->remplacer("{onglet->forum}",$oSet_Onglet);
	$oBlock_Forum->remplacer("{onglet->titre}","Sujet");
	$oBlock_Forum->remplacer("{onglet->texte}",$oSet_Titre_Forum);
	
	// Titre
	$oBlock_Forum->remplacer("{titre->valeur}",htmlentities($oSujetForum->retTitre()));
	
	// Modalité
	$iModaliteParent = $oSujetForum->retModalite();
	$oBlock_Forum->remplacer("{modalite->parent->selectionner}",($iModaliteParent == MODALITE_IDEM_PARENT ? " selected" : NULL));
	$oBlock_Forum->remplacer("{modalite->tous->selectionner}",($iModaliteParent == MODALITE_POUR_TOUS ? " selected" : NULL));
	$oBlock_Forum->remplacer("{modalite->equipe->selectionner}",($iModaliteParent == MODALITE_PAR_EQUIPE ? " selected" : NULL));
	
	// Statut
	
	// Accessible aux visiteurs
	$oBlock_Forum->remplacer("{accessible_visiteurs->selectionner}",($oSujetForum->retAccessibleVisiteurs() == "1" ? " checked" : NULL));
}
else if ($url_sModaliteFenetre == "supprimer")
{
	$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdForumParent);
	
	$oBlock_Forum->ajouter($oSet_Supprimer_Forum);
	$oBlock_Forum->remplacer("{sujet->titre}",htmlentities($oSujetForum->retTitre()));
	$oBlock_Forum->remplacer("{messages->total}",$oSujetForum->retNombreMessages());
}

// Afficher le bloc
$oBlock_Forum->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

