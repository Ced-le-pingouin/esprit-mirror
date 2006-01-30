<?php

/*
** Fichier ................: chat.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 15/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("chat.tbl.php"));
require_once(dir_database("ids.class.php"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdChat = (empty($HTTP_GET_VARS["idChat"]) ? 0 : $HTTP_GET_VARS["idChat"]);

$oChat = new CChat($oProjet->oBdd,$url_iIdChat);

$iIdNiveau = $oChat->retIdNiveau();
$iTypeNiveau = $oChat->retTypeNiveau();

switch ($iTypeNiveau)
{
	case TYPE_SOUS_ACTIVITE:
	//   ------------------
		$oObjNiveau = new CSousActiv($oProjet->oBdd,$iIdNiveau);
		break;
		
	case TYPE_RUBRIQUE:
	//   -------------
		$oObjNiveau = new CModule_Rubrique($oProjet->oBdd,$iIdNiveau);
		break;
}

$iNbrChats = $oObjNiveau->retNombreChats();

$iNumOrdre = $oChat->retNumOrdre();
$sNomChat = $oChat->retNom();

$sModaliteChat = array(NULL,NULL);
$sModaliteChat[$oChat->retModalite()] = " checked";

$sSalonPriveChat = ($oChat->retSalonPrive() ? " checked" : NULL);
$sEnregConversation = ($oChat->retEnregConversation() ? " checked" : NULL);

// Couleur
$sCouleurChat = $oChat->retCouleur();
$sNomCouleurChat = $oChat->retNomCouleur();
$sValeurCouleurChat = $oChat->retValeurCouleur();

unset($oObjNiveau);

$oProjet->terminer();

// ---------------------
// Liste des numéris d'ordre
// ---------------------
$sNumerosOrdre = NULL;

for ($i=1; $i<=$iNbrChats; $i++)
{
	$sNumerosOrdre .= "<option"
		." value=\"{$i}\""
		.($i == $iNumOrdre ? " selected" : NULL)
		.">&nbsp;{$i}&nbsp;</option>";
}

$sMessageDePatience_0 = "<html>"
	."<head>"
	."<link type='text/css' rel='stylesheet' href='".dir_theme("globals.css")."'>"
	."</head>"
	."<body>";

$sMessageDePatience_1 = "<table border='0' cellspacing='0' cellpadding='0' width='100%' height='100%'>"
	."<tr><td align='center' valign='middle'>"
	."<img src='".dir_theme("barre-de-progression.gif")."' border='0'>"
	."<br>"
	."Mise à jour..."
	."</td></tr>"
	."</table>";

$sMessageDePatience_2 = "</body>"
	."</html>";

$sMessageDePatience_3 = "";

?>
<html>
<head>
<?php inserer_feuille_style("dialog.css; chat.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function envoyer()
{
	document.forms[0].submit();
	
	document.open("text/html","replace");
	document.writeln("<?=$sMessageDePatience_0?>");
	document.writeln("<?=$sMessageDePatience_1?>");
	document.writeln("<?=$sMessageDePatience_2?>");
	document.writeln("<?=$sMessageDePatience_3?>");
	document.close();
	
	top.recharger_fenetre_parente = true;
}

function ChangerCouleur(v_sValeurCouleur)
{
	var a = v_sValeurCouleur.split(";");
	
	document.forms[0].elements["couleurChat"].value = v_sValeurCouleur;
	
	// Modifier la page html
	document.getElementById("idNomCouleurChat").innerHTML = a[0];
	document.getElementById("idCouleurChat").style.background = "rgb(" + a[1] + ")";
}

function ChoisirCouleur()
{
	var sUrl = "chat_couleurs-index.php?CouleurChat=" + document.forms[0].elements["couleurChat"].value;
	var sNomFenetre = "WinChoisirCouleurChat";
	var sCaracteristiques = "width=300,height=460";
	var WinChoisirCouleurChat = open(sUrl,sNomFenetre,sCaracteristiques);
	WinChoisirCouleurChat.focus();
}

//-->
</script>

</head>
<body>
<?php
if ($url_iIdChat < 1)
{
	echo "<div align=\"center\">"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"85%\">"
		."<tr><td>"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">"
		."<tr><td style=\"text-align: center;\">"
		."<div class=\"attention\">"
		."<b>Aucun salon chat n'est cr&eacute;&eacute; pour l'instant.<br>"
		."Cliquez sur &laquo;&nbsp;"
		."<a"
		." href=\"javascript: top.oListe().ajouter(); void(0);\""
		." title=\"Cliquez ici pour créer un nouveau chat\""
		.">Ajouter</a>"
		."&nbsp;&raquo; pour en créer un.</b></div>"
		."</td></tr></table></td></tr></table></div>"
		."</body>\n</html>";
	exit();
}
?>
<form action="chat-liste.php" target="Liste" method="post">
<table border="0" cellpadding="0" cellspacing="3">
<tr>
<td><div class="intitule">Ordre&nbsp;:&nbsp;</div></td>
<td>
<select name="ordreChat"><?=$sNumerosOrdre?></select>
</td>
</tr>
<tr>
<td><div class="intitule">Nom&nbsp;:&nbsp;</div></td>
<td><input type="text" name="nomChat" size="40" value="<?=$sNomChat?>" style="width: 100%;"></td>
</tr>
<tr>
<tr>
<td><div class="intitule">Couleur&nbsp;:&nbsp;</div></td>
<td><table border="0" cellspacing="0" cellpadding="0" width="300"><tr><td style="background-color: #000000"><div onclick="ChoisirCouleur()" title="Cliquer ici pour changer la couleur du chat" style="cursor: pointer;"><table border="0" cellspacing="1" cellpadding="3" width="100%"><tr><td id="idCouleurChat" style="text-align: center; background-color: rgb(<?=$sValeurCouleurChat?>);"><b><span id="idNomCouleurChat"><?=$sNomCouleurChat?></span></b></td></tr></table></div></td></tr></table></td>
</tr>
<tr><td>&nbsp;</td><td><div style="text-align: center"><a href="javascript: ChoisirCouleur();" onfocus="blur()">Changer de couleur</a></div></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<td><div class="intitule">Modalit&eacute;&nbsp;:&nbsp;</div></td>
<td>
<input type="radio" name="modaliteChat" value="0"<?=$sModaliteChat[0]?>>&nbsp;Pour tous
<?php
echo "<input"
	." type=\"radio\""
	." name=\"modaliteChat\""
	." value=\"1\""
	.$sModaliteChat[1]
	.">&nbsp;Par &eacute;quipe";
?>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td>&nbsp;</td>
<td><input type="checkbox" name="utiliserSalonPriveChat"<?=$sSalonPriveChat?>>&nbsp;Utilisation du salon priv&eacute;</td>
</tr>
<tr><td>&nbsp;</td>
<td><input type="checkbox" name="enregistrerChat"<?=$sEnregConversation?>>&nbsp;Enregistrer les conversations</td>
</tr>
</table>
<input type="hidden" name="couleurChat" value="<?=$sCouleurChat?>">
<input type="hidden" name="idChat" value="<?=$url_iIdChat?>">
<input type="hidden" name="idNiveau" value="<?=$iIdNiveau?>">
<input type="hidden" name="typeNiveau" value="<?=$iTypeNiveau?>">
</form>
</body>
</html>
