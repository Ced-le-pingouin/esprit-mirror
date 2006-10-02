<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<script src="selectionobj.js" type="text/javascript"></script>
<title>Déplacer un élément</title>
</head>
<body class="popup">
<div id="contenu">
	<form action="position_objet.php" name="formposition" method="get">
	Position : 
	<select name="ordreobj">
	[BLOCK_POSITION+]
		<option value="{ordre_obj_form}" {obj_actuel}>{ordre_obj_form}</option>
	[BLOCK_POSITION-]
	</select>
	<input type="hidden" name="idobj" value="{id_obj}" />
	<input type="hidden" name="idformulaire" value="{id_formulaire}" />
	<input type="hidden" name="deplacer" value="deplacer" />
	</form>
	[BLOCK_FERMER+]
	<script language="javascript" type="text/javascript">
	rechargerlistepopup({id_obj},{id_formulaire});
	window.close();
	</script>
	[BLOCK_FERMER-]
</div>
<div id="barreaction">
	<a id="valider" href="#" onclick="document.forms['formposition'].submit();">Valider</a>
	<a id="fermer" href="#" onclick="window.close ();">Fermer</a>
</div>
</body>
</html>
