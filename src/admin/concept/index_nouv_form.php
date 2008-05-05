<?php
$sParamsUrl = NULL;

foreach ($_GET as $sCle => $sValue)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValue}";
?>
<html><head><title>Cr&eacute;ation d'une nouvelle formation</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head></html>
<frameset rows="63,*,24" border="0" frameborder="0" framespacing="0">
<frame src="nouv_form-titre.php?tp=eConcept&st=Création d'une nouvelle formation" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
<frame src="nouv_form.php<?php echo $sParamsUrl?>" name="main" marginwidth="15" marginheight="10"  frameborder="0" scrolling="no" noresize="noresize">
<frame src="nouv_form_menu.php?fin=1" name="menu" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
