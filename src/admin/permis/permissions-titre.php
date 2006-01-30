<?php

require_once ("globals.inc.php");

$oProjet = new CProjet();

$sIdStatutOptions = NULL;
$aoListeStatuts = $oProjet->retListeStatut();

foreach ($aoListeStatuts as $aStatut)
	$sIdStatutOptions .= "<option"
		." value='".$aStatut["IdStatut"]."'"
		.">".$aStatut["NomStatut"]."</option>\n";

$oProjet->terminer();

?>
<html>
<head>
<?=inserer_feuille_style("barre_outils.css")?>
<style type="text/css">
<!--
div.toolbar { position: absolute; left: 0px; top: 0px; width: 100%; height: 32px; }
div#toolbar2 { position: absolute; left: 0px; top: 32px; width: 100%; height: 25px; }
-->
</style>
<script type="text/javascript" language="javascript" src="permissions.js"></script>
<script type="text/javascript" language="javascript">
<!--

function init()
{
	if (top.frames['Principale'])
		document.forms[0].submit();
	else
		setTimeout("init()",1000);
}

function envoyer(v_iIdStatut)
{
	with (document.forms[0])
	{
		if (typeof(v_iIdStatut) == "string")
			elements["params"].value = v_iIdStatut;
		submit();
	}
}

//-->
</script>
</head>
<body onload="init()">
<div id="toolbar1" class="toolbar">
<form name="StatutForm" action="permissions.php" method="post" target="Principale">
<table border="0" cellspacing="0" cellpadding="3" width="100%" height="100%">
<tr>
<td>&nbsp;Liste&nbsp;des&nbsp;statuts&nbsp;:&nbsp;</td>
<td>
<select name="idStatut" size="1" onchange="envoyer(this.value)">
<?=$sIdStatutOptions?>
</select>
</td>
<td>&nbsp;Filtre&nbsp;:&nbsp;</td>
<td width="99%">
<select name="filtre" onchange="envoyer()">
<option value="">Tout</option>
<option value="BLOC">Bloc</option>
<option value="CONCEPT">Concepteur</option>
<option value="COTUTEUR">Co-tuteur</option>
<option value="COURRIEL">Courriel</option>
<option value="COURS">Cours</option>
<option value="ELEMENT_ACTIF">El&eacute;ment actif</option>
<option value="EQUIPE">&Eacute;quipe</option>
<option value="ETUDIANT">&Eacute;tudiant</option>
<option value="FERME">Fermer</option>
<option value="FORMULAIRE">Formulaire</option>
<option value="FORUM">Forum</option>
<option value="MESSAGE">--> messages</option>
<option value="SUJET">--> sujets</option>
<option value="GALERIE">Galerie</option>
<option value="INV">Invisible</option>
<option value="OUTIL">Outils</option>
<option value="RESP">Responsable</option>
<option value="RUBRIQUE">Rubrique/Unit&eacute;</option>
<option value="SESSION">Session</option>
<option value="STATUT">Statut</option>
<option value="TUTEUR">Tuteur</option>
</select>
</td>
</tr>
</table>
<input type="hidden" name="params" value="<?=$aoListeStatuts[0]['IdStatut']?>">
</form>
</div>
<div id="toolbar2" class="toolbar">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr>
<td><input type="button" value="Appliquer" onclick="appliquer()"></td>
<td width="99%" align="right">
<a href="javascript: oui(); void(0);">Tout &agrave; oui</a>
<a href="javascript: non(); void(0);">Tout &agrave; non</a>
<a href="javascript: inverser(); void(0);">Inverser</a>
<a href="javascript: retablir(); void(0);">R&eacute;tablir</a>
&nbsp;</td>
</tr>
<table>
</div>
</body>
</html>

