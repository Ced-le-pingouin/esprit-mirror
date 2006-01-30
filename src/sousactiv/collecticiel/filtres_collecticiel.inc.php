<?php

function construireHtmlSelect ($v_sNomSelect,$v_sOnChange,$v_amListe,$v_mSelect)
{
	$sHtmlSelect = "<select"
		." name=\"{$v_sNomSelect}\""
		." onchange=\"{$v_sOnChange}\""
		.">";
	
	for ($i=0; $i<count($v_amListe); $i++)
		$sHtmlSelect .= "<option"
			." value=\"".$v_amListe[$i][0]."\""
			.($v_amListe[$i][0] == $v_mSelect ? " selected" : NULL)
			.">".$v_amListe[$i][1]."</option>";
	
	$sHtmlSelect .= "</select>\n";
	
	return $sHtmlSelect;
}

// ----------------------
// STATUTS
// ----------------------
$amStatuts = array(
		array(0,"Tous les documents"),
		array(STATUT_RES_EVALUEE,"Evalu�"),
		array(STATUT_RES_ACCEPTEE,"Accept�"),
		array(STATUT_RES_APPROF,"Approfondir"),
		array(STATUT_RES_SOUMISE,"Soumis pour �valuation"),
		array(STATUT_RES_EN_COURS,"En cours"),
		array(STATUT_RES_TRANSFERE,"Transf�r�"));

$sHtmlSelectStatut = construireHtmlSelect("SELECT_STATUT","document.forms[0].submit()",$amStatuts,$g_iStatut);

// ----------------------
// PERSONNES/EQUIPES
// ----------------------
$asListes = array(NULL);

for ($i=0; $i<count($asNomsEspaces); $i++)
	$asListes[] = array($aiIdsEspaces[$i],$asNomsEspaces[$i]);

if ($iModalite == MODALITE_INDIVIDUEL)
	$asListes[0] = array(0,($i > 0 ? "Tous les �tudiants" : "Pas d'�tudiant trouv�"));
else
	$asListes[0] = array(0,($i > 0 ? "Toutes les �quipes" : "Pas d'�quipe trouv�e"));

$sHtmlSelectPersonne = construireHtmlSelect("SELECT_PERSONNE","document.forms[0].submit()",$asListes,$g_iIdPers);

// ----------------------
// DATES
// ----------------------
$asDate = array(
		array("0","Tous les jours"),
		array(date("Y-m-d"),"Aujourd'hui"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y"))),"Depuis hier"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-2,date("Y"))),"Depuis 2 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-3,date("Y"))),"Depuis 3 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-4,date("Y"))),"Depuis 4 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-5,date("Y"))),"Depuis 5 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y"))),"Depuis 6 jours"),
		array(date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y"))),"Depuis 1 semaine"),
		array(date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y"))),"Depuis 1 mois"));

$sHtmlSelectDate = construireHtmlSelect("SELECT_DATE","document.forms[0].submit()",$asDate,$g_sDate);

$sHtmlCheckboxBloc = "<br>"
	."<input type=\"checkbox\" name=\"CB_AFF_BLOC\""
	." onchange=\"document.forms[0].submit()\""
	." onclick=\"blur()\""
	.($g_bBloc ? " checked" : NULL)
	.">Afficher les blocs vides";

?>

