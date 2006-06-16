<?php
require_once("globals.inc.php");
require_once("admin_globals.inc.php");

$oProjet = new CProjet();

$iIdFormation = $HTTP_GET_VARS["idForm"];

$sMessage = NULL;
if ($iIdFormation <= 0)
{
	$sMessage = "La formation à exporter est invalide";
}
else if ($oProjet->retIdUtilisateur())
{
	$oStatuts = new CStatutUtilisateur($oProjet->oBdd, $oProjet->retIdUtilisateur());
	$oStatuts->initStatuts();
	
	if ($oStatuts->estAdministrateur())
	{
		$oFormation = new CFormation($oProjet->oBdd, $iIdFormation);
		
		$r = $oFormation->copier(TRUE, 'sql');
		
		header('Content-Type: application/octet-stream' );
		header('Content-Disposition: attachment; filename='.basename($r));

		//print nl2br($sSql);
		//print $sSql;
		
		readfile($r);
		unlink($r);
		@rmdir(dirname($r));
		
		exit();
	}
	else
	{
		$sMessage = "Vous n'avez pas le statut requis pour exporter les formations";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Exporter la formation</title>
</head>
<body>
<?php
if ($sMessage)
	print $sMessage;
?>
</body>
</html>
