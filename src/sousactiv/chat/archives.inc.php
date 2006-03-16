<?php $sIconeTitres = "<img src=\"".dir_theme("icone-titres.jpg")."\" width=\"10\" height=\"10\" border=\"0\">&nbsp;"; ?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html;  charset=utf-8">
<title><?=$oArchive->retSalon()?></title>
<?php inserer_feuille_style("archives.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function init()
{
	<?=$sFonctionInit?>
	top.oSousMenu().location = "archives-sous_menu.php?AM=1";
	
	/*var elems = document.getElementsByTagName("span");
	
	for (var i=0; i< elems.length; i++)
	{
		elems.item(i).onclick = function() {
			document.getElementsByName("idPers").item(0).value = this.childNodes.item(0).nodeValue;
			document.getElementsByName("recharger").item(0).submit();
		};
	}*/
}

function imprimer() { window.print(); }
function telecharger() { document.forms["telecharger"].submit(); }

function rafraichir()
{
	var sLocation = self.location.href.replace(/#bottom/,"");
	self.location.replace(sLocation + "#bottom");
}

//-->
</script>
</head>
<body onload="init()">
<h3>Informations compl&eacute;mentaires</h3>
<table border="0" cellpadding="3" cellspacing="1" width="100%">
<?=$sInformations?>
</table>
<h3>Messages</h3>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td>
<table border="0" cellpadding="3" cellspacing="1" width="100%">
<?=$sEnteteTableau?>
<?=$sMessages?>
</table>
</td></tr>
</table>
<form name="telecharger" action="<?=dir_code_lib("download.php",FALSE,FALSE)?>" target="_top" method="get">
<input type="hidden" name="f" value="<?=rawurlencode($sArchiveChatTelecharger)?>">
</form>
<form name="recharger" action="<?=$HTTP_SERVER_VARS["PHP_SELF"]?>" method="get">
<input type="hidden" name="idNiveau" value="<?=$url_iIdNiveau?>">
<input type="hidden" name="typeNiveau" value="<?=$url_iTypeNiveau?>">
<input type="hidden" name="archive" value="<?=$url_sArchive?>">
<input type="hidden" name="idPers" value="<?=$url_iIdPers?>">
</form>
<a name="bottom"></a>
</body>
</html>
