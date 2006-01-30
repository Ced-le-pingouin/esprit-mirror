<?
require("../template.inc");
require_once("../includes.inc.php");

$oProjet = new CProjet();
$tpl = new Template("liste_ajout_structure_ressource.tpl");

$Set_forum = $tpl->defVariable("SET_FORUM");
$Set_profil = $tpl->defVariable("SET_PROFIL");

$test_block = new TPL_Block("BLOCK_TEST",$tpl);
$test_block->ajouter($Set_forum);
$test_block->remplacer("[IFORUM]","KIKOU");

$test_block->ajouter($Set_profil);

$test_block->ajouter($Set_forum);
$test_block->remplacer("[IFORUM]","Ah que coucou!!!");


$test_block->remplacer("[TJRSMEME]","c tjrs le meme");

$test_block->afficher();

function retStructureRessource($v_IdCateg,$v_LgCib,$v_parent)
{
	GLOBAL $oProjet;
	$oStructureRessource = new CStructureRessource($oProjet->oBdd);
	$aoListeStructureRess = $oStructureRessource->retListeStructureRessourceSansTerminaison($v_IdCateg,$v_LgCib,$v_parent);
	if(!empty($aoListeStructureRess))
	{
		foreach($aoListeStructureRess AS $oStructure)
		{
			$aStruct[] = $oStructure;
			$tmpStruct = retStructureRessource($v_IdCateg,$v_LgCib,$oStructure->IdStructureRessource);
			if(!empty($tmpStruct))
			{
				$aStruct[] = $tmpStruct;
			}
		}
		return $aStruct;
	}
}

function afficheStructureRessource($v_aStruct,$v_profondeur)
{
	$sEspace = $v_profondeur * 25;
	if(is_array($v_aStruct))
	{
			foreach($v_aStruct AS $aStruct)
			{
				$sReturn .= afficheStructureRessource($aStruct,$v_profondeur+1);
			}
	}
	else
	{
		if(empty($v_aStruct->Branche))
			$sReturn .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">"
						."<tr><td><img src=\"../images/1pix.gif\" height=\"1\" width=\"".$sEspace."\" border=\"0\"></td><td>".$v_aStruct->Titre."</td><td><a href=\"javascript:PopupCenter('ajout_structure_ressource.php?IdCategRess=".$v_aStruct->IdCategorieRessource."&LgCib=".$v_aStruct->LangueCible."&ParentStructureRessource=".$v_aStruct->IdStructureRessource."',500,300,'menubar=no,scrollbars=yes,status=no,resizable=yes','admin');\">[+]</a></td></tr>"
						."</table>";
	}
	return $sReturn;
}

if($oProjet->VerifPermission("PERM_MODIF_PERMISSION"))
{
	$Liste_Categ_loop = new TPL_Block("AFFICHE_LISTE_CATEG",$tpl);
	$Structure_Categ_loop = new TPL_Block("AFFICHE_STRUCTURE",$tpl);

	if(isset($HTTP_GET_VARS["IdCategRess"]))
	{
		$Liste_Categ_loop->effacer();
		$Structure_Categ_loop->afficher();
		
		$oCategRess = $oProjet->retCategorieRessource($HTTP_GET_VARS["IdCategRess"]);

		$aLangueCible = $oProjet->retLangue($HTTP_GET_VARS["LgCib"]);
		eval('$iConstanteLangueCib = TXT_LANGUE_'.strtoupper($aLangueCible["AbrevLangue"]).';'); // astuce pour contrer le manque de la fonction constant() ds cette version de PHP
		$tpl->remplacer("[LANGUE_CIBLE]",$oProjet->retTexteM($iConstanteLangueCib));

		$oStructureRessource = new CStructureRessource($oProjet->oBdd);
		$tpl->remplacer("[ORDRE_CATEG]",$oCategRess->OrdreCategorieRessource);
		eval('$iConstante = '.$oCategRess->NomCategorieRessource.';'); // astuce pour contrer le manque de la fonction constant() ds cette version de PHP
		$tpl->remplacer("[NOM_CATEG]",$oProjet->retTexteM($iConstante)."&nbsp;<a href=\"javascript:PopupCenter('ajout_structure_ressource.php?IdCategRess=".$HTTP_GET_VARS["IdCategRess"]."&LgCib=".$HTTP_GET_VARS["LgCib"]."&ParentStructureRessource=0',500,300,'menubar=no,scrollbars=yes,status=no,resizable=yes','admin');\">[+]</a>");
		
		$Structure_loop = new TPL_Block("STRUCTURE_LISTE",$tpl);
	
		$aStructureRess = retStructureRessource($oCategRess->IdCategorieRessource,$HTTP_GET_VARS["LgCib"],0);
		if(isset($aStructureRess))
		{
			$Structure_loop->afficher();
			$sStructure = afficheStructureRessource($aStructureRess,-1);
			$tpl->remplacer("[NOM_BRANCHE]",$sStructure);
		}
		else
		{
			$Structure_loop->effacer();
		}
	}
	else
	{
		$Liste_Categ_loop->afficher();
		$Structure_Categ_loop->effacer();

		$tpl->remplacer("[LGCIB]",$HTTP_GET_VARS["LgCib"]);
		
		$aLangueCible = $oProjet->retLangue($HTTP_GET_VARS["LgCib"]);
		eval('$iConstanteLangueCib = TXT_LANGUE_'.strtoupper($aLangueCible["AbrevLangue"]).';'); // astuce pour contrer le manque de la fonction constant() ds cette version de PHP
		$tpl->remplacer("[LANGUE_CIBLE]",$oProjet->retTexteM($iConstanteLangueCib));

		$aListeLangue = $oProjet->retListeLangue();
		if($aListeLangue)
		{
			$optionsLgCib = "";
			foreach($aListeLangue as $aLangue)
			{
				eval('$iConstanteLangue = TXT_LANGUE_'.strtoupper($aLangue["AbrevLangue"]).';'); // astuce pour contrer le manque de la fonction constant() ds cette version de PHP
				if($aLangue["IdLangue"]==$HTTP_GET_VARS["LgCib"])
					$optionsLgCib .= "<option value='".$aLangue["IdLangue"]."' SELECTED>".$oProjet->retTexteM($iConstanteLangue)."</option>";
				else
					$optionsLgCib .= "<option value='".$aLangue["IdLangue"]."'>".$oProjet->retTexteM($iConstanteLangue)."</option>";
			}
			$tpl->remplacer("[OPTIONSLGCIB]",$optionsLgCib);
		}
		else
		{
			$tpl->remplacer("[OPTIONSLGCIB]","");
		}
		
		$Categ_loop = new TPL_Block("CATEG_LISTE",$tpl);
		
		$aoListeCategRess = $oProjet->retListeCategorieRessource();
		if(isset($aoListeCategRess))
		{
			$Categ_loop->beginLoop();
			foreach($aoListeCategRess AS $oCateg)
			{
				$Categ_loop->nextLoop();
				$Categ_loop->remplacer("[ORDRE_CATEG]",$oCateg->OrdreCategorieRessource);
				eval('$iConstante = '.$oCateg->NomCategorieRessource.';'); // astuce pour contrer le manque de la fonction constant() ds cette version de PHP
				$Categ_loop->remplacer("[NOM_CATEG]",$oProjet->retTexteM($iConstante));
				$Categ_loop->remplacer("[IDCATEGRESS]",$oCateg->IdCategorieRessource);
			}
			$Categ_loop->afficher();
		}
	}
	$tpl->afficher();
}
else
{
	echo "<html><body><b>Vous n'avez pas les droits pour afficher cette page.</b></body></html>";	
}
?>
