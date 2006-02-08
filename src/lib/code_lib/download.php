<?php

require_once (dirname (__FILE__)."/fichiers_permis.inc.php");

/*************************************************************************
** Script permettant de t�l�charger n'importe quel fichier, quel que soit
** son type (MIME), et ce m�me dans Internet Explorer (IE).
**
** Test� avec les navigateurs suivants:
**	IE 5.5:	OK
**	IE 5.0:	- Apr�s t�l�chargement, IE continue � 'chercher' (pointeur 
**		  sablier & terre tournante), mais le fichier est OK 
**		  (il faut cliquer 'Arr�ter' dans la barre de navigation)
**		- En plus, si le fichier n'a pas une extension reconnue
**		  par Windows, son extension sera doubl�e (bug!): par ex.
**		  'archive.tar..tar'
**	IE 4:	Impossible de forcer le download d'un fichier reconnu
**		(.doc, peut-�tre aussi .pdf et autres): il est affich�
**		directement.
**	N 4.7x:	OK
**	N 4.61:	OK
**	N 4.5:	OK
*************************************************************************/

/*************************************************************************
** FONCTIONS
*************************************************************************/

/*
** Fonction 		: retExt
** Description		: renvoie l'extension d'un fichier. Cette fonction devrait
**					  �tre am�lior�e, peut-�tre en utilisant les fonctions 
**					  pathinfo() et/ou realpath() de PHP 4.0.3+
** Entr�e			:
**					v_sNomFichier	: nom du fichier.
**									  Attention ! Cette version n'est pas
**									  prot�g�e contre les noms de fichiers
**									  contenant des '..' et '.' dans le 
**									  chemin, et dont le fichier n'a pas
**									  d'extension. Si on passe 
**									  'res/../res/fichier' comme param�tre, 
**									  la fonction va retourner '/res/fichier'
**									  comme extension
**					v_bMinuscules	: renvoie l'extension en minuscules
** Sortie			: l'extension du fichier (sans le point)
*/

function retExt ($v_sNomFichier, $v_bMinuscules = FALSE)
{
	$asMorceaux = explode(".", $v_sNomFichier);
	
	if (count($asMorceaux) > 1)
		$r_sExt = $asMorceaux[count($asMorceaux) - 1];
	else
		$r_sExt = "";
		
	if ($v_bMinuscules)
		return strtolower($r_sExt);
	else
		return $r_sExt;
}


function retVraiNomFichier ($v_sNomComplet)
{
	return ereg_replace("-([0-9]){4}\.",".",$v_sNomComplet);
}

// Attention ! le nom du fichier pass� en param�tre de l'URL est contenu
// dans la variable 'f' (f=...), mais il a d� �tre auparavant encod� par la
// fonction PHP 'rawurlencode'. C'est pour �viter le plantage avec les noms 
// de fichier contenant des espaces et autres caract�res sp�ciaux 

// FILIPPO : tu dois remplacer le 'f' entre [] par 'fd' (et enlever 'rawurldecode' ???)
$nomComplet = stripslashes(rawurldecode($HTTP_GET_VARS["f"]));

$sErreur = NULL;

// Si on essaye de downloader un fichier en partant du root ('/'), ou en 
// utilisant des '..' pour remonter dans l'arborescence, ou un fichier 
// auquel on n'a pas acc�s, il y aura un message d'erreur
/*
if ($nomComplet[0] == "/")
{
	$sErreur = "Utilisation non autoris�e du caract�re '/'";
}
else
*/
if (!(strpos($nomComplet, "..") === false))
{
	$sErreur = "Utilisation non autoris�e de la chaine '..'";
}
else if (!is_readable($HTTP_SERVER_VARS["DOCUMENT_ROOT"].$nomComplet))
{
	$sErreur = "Lecture impossible";
}
else
{
	// on r�cup�re le nom de fichier, sans le chemin
	$nomSimple = stripslashes(basename($nomComplet));
	$extension = retExt($nomSimple, TRUE);
	
	// on v�rifie que l'extension est autoris�e
	if (!validerFichier($nomSimple))
		$sErreur = "Le t�l�chargement de ce type de fichier ($nomSimple) n'est pas autoris�";
}

/*
	header("Content-Type: application/msword")
	
	application/msword
	application/ms-excel
*/

// s'il n'y a pas eu d'erreur, le t�l�chargement du fichier d�bute
if (empty($sErreur))
{
	// FILIPPO : enl�ve les commentaires des 2 lignes suivantes si tu veux
	// utiliser la variable 'filename' de l'URL pour nommer le fichier envoy�
	// � l'utilisateur. Si 'filename' n'a pas �t� sp�cifi�, le fichier aura le
	// m�me nom que sur le serveur
	
	if (isset ($HTTP_GET_VARS["fn"]))
	{
		if ($HTTP_GET_VARS["fn"] === "1") 
			$nomSimple = retVraiNomFichier($nomSimple);
		else
			$nomSimple = rawurldecode($HTTP_GET_VARS["fn"]).".".$extension;
	}
	
	//header ("Content-Type: application/force-download");
	header ("Content-Type: application/octet-stream");
	//header ("Content-Length: ".filesize($nomComplet));
	header ("Content-Disposition: attachment; filename=".str_replace(" ","_",$nomSimple));
	//header("Content-Transfer-Encoding: binary"); 
	readfile ($HTTP_SERVER_VARS["DOCUMENT_ROOT"].$nomComplet);
}
else
{
	// s'il y a eu erreur, on en affiche la raison
	print "<html>\n"
		."<head>\n<title>T�l�chargement impossible !</title>\n</head>\n"
		."<div style=\"text-align: center;\">\n"
		."<center>\n"
		."<h4>\n"
		."<br><br>ERREUR<br><br><br>"
		."Fichier '$nomComplet'<br><br>$sErreur"
		."</h4>\n"
		."<a href=\"javascript: history.back();\">Retour</a>"
		."</div>\n"
		."</body>\n"
		."</html>\n";
}

?>
