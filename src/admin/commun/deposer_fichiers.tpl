<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{title}</title>
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">

<script type="text/javascript" language="javascript">
<!--
function envoyer()
{
	if (document.forms[0].elements["nomFichierCopier"].value.length < 1)
		return false;
	
	if (document.getElementById)
	{
		document.getElementById("deposer").style.display = "none";
		document.getElementById("deposer").style.visibility = "hidden";
		
		document.getElementById("barre_de_progression").style.display = "block";
		document.getElementById("barre_de_progression").style.visibility = "visible";
	}
	
	document.forms[0].submit();
}

function activer_boutton_deposer()
{
	with (document.forms[0])
		elements["btnDeposer"].disabled = (elements["nomFichierCopier"].value.length < 1);
}
//-->
</script>
<style type="text/css">
<!--
#barre_de_progression { display: none; visibility: hidden; text-align: center; }
#deposer { display: block; visibility: visible; }
//-->
</style>
</head>
<body topmargin="5" leftmargin="5" rightmargin="5" bottommargin="5">
{form}
[BLOCK_DEPOSER_FICHIERS+]
[VAR_ONGLET_TITRE+]{title}[VAR_ONGLET_TITRE-]
[VAR_ONGLET_TEXTE+]
<table border="0" cellpadding="2" cellspacing="0" align="left">
<tr><td><b>Rechercher le fichier&nbsp;:</b><br><input type="file" name="nomFichierCopier"><br><br><b>D&eacute;poser dans le r&eacute;pertoire&nbsp;:</b><br>
<select name="nomRepertoireCopie">[BLOCK_NOM_REPERTOIRE_COPIE+]<option value="{option.value}">{option.label}</option>[BLOCK_NOM_REPERTOIRE_COPIE-]</select></td>
<td valign="top"><table border="0" cellspacing="0" cellpadding="0">
<tr><td><input type="button" name="btnDeposer" value="Déposer" onclick="envoyer()" style="width: 110px;"></td></tr>
<tr><td><input type="button" name="btnAnnuler" value="Annuler" onclick="self.close()" style="width: 110px;"></td></tr>
</table></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td><input type="checkbox" name="dezipFichier"{input.dezipFichier.checked}>&nbsp;&nbsp;Ne pas d&eacute;compresser (si fichier zipp&eacute;)</td><td>&nbsp;</td></tr>
</table>
[VAR_ONGLET_TEXTE-]
<div id="deposer">{div.deposer.text}</div>
[BLOCK_DEPOSER_FICHIERS-]
<input type="hidden" name="repDest" value="{input.repDest.value}">
<input type="hidden" name="effFichiers" value="{input.effFichiers.value}">
{/form}
<div id="barre_de_progression"><p>&nbsp;</p><img src="theme://barre-de-progression.gif" border="0"><br>Chargement du document vers le serveur</div>
</body>
</html>

