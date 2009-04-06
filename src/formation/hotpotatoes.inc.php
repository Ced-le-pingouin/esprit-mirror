<?php

/**
 * \brief 
 *
 * @param $nomFichier nom du fichier Ã  modifier
 * @return      nom du fichier produit, ou FALSE en cas d'erreur
 */
function hotpot_patch_file( $nomFichier, $IdHotpot, $IdActiv ,$IdSousActiv, $IdSessionExercice, $iNumeroPage) {
	global $oProjet;

	/**
	 * 
	 * On verifie que l'exercice n'a pas déjà été fait par l'étudiant sur cette "session d'exercice"
	 * Si déjà fait, on n'enregistre pas le score, mais on affiche celui enregisté auparavant
	 * 
	 */
	$oHotpotVerifScore	= new CHotpotatoesScore($oProjet->oBdd);
	$iScoreExercice		= $oHotpotVerifScore->ExerciceFait($oProjet->oUtilisateur->retId(), $IdHotpot, $IdSessionExercice, $iNumeroPage);

	/**
	 * On enregistre les noms de fichiers de chaque page dans un cookie.
	 */
	setcookie("Page$iNumeroPage", $nomFichier);

	$html = file_get_contents($nomFichier);

	if ($iScoreExercice == NULL) { // exercice pas encore fait, on active Ajax pour l'insérer dans la DB
	// Pas de gestions de "Detail" pour le moment (difficultÃ© AJAX + traitement XML)
	// modification du source HotPot
	$insertJS = <<<ENDOFTEXT
// CODE ESPRIT : DEBUT
var xhr = null;
if (window.XMLHttpRequest) // Firefox et autres
	xhr = new XMLHttpRequest(); 
else if (window.ActiveXObject) { // Internet Explorer 
	try {
		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
} else { // XMLHttpRequest incompatible avec ce navigateur 
	//alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
	xhr = false; 
}

// on ajoute un booléen pour éviter d'insérer plusieurs fois la même entrée.
// La fonction Finish() est appelée toutes les 30 secondes même quand l'exercice est terminé.

var ExerciceTermine = false;
var QArray = new Array();

function Finish(){
	if (!ExerciceTermine) { 
			xhr.open("GET","%s?action=hotpotScore&IdHotpot=%d&IdPers=%d&IdSessionExercice=%s&NumeroPage=%d&NombreTotal="+QArray.length+"&Score="+Score+"&DateDebut="+HPNStartTime+"&DateFin="+(new Date()).getTime(),true);
	xhr.send(null);
	ExerciceTermine = true;
	}
// CODE ESPRIT : FIN
ENDOFTEXT;
	$html = str_replace(
			"function Finish(){",
			sprintf($insertJS, dir_http_plateform('ajax.php'), $IdHotpot, $oProjet->oUtilisateur->retId(), $IdSessionExercice, $iNumeroPage),
			$html );
	}

/*
 * l'exercice est déjà fait, on affiche le score et la moyenne
 */
	else {
		$iMoyenne = $oHotpotVerifScore->CalculMoyenne($IdSessionExercice);
		$ModifieHtml = "
<script type=\"text/javascript\">
window.clearInterval(Interval);
setTimeout('Finish()', SubmissionTimeout);
WriteToInstructions(YourScoreIs + ' ' + $iScoreExercice + '%.<br />Moyenne : ' + $iMoyenne +'%.');

</script>
</body>";

	$html = str_replace("</body>", $ModifieHtml, $html);
	$html = str_replace("var TimeOver = false;", "var TimeOver = true;", $html);
	$html = str_replace("var Locked = false;", "var Locked = true;", $html);
	$html = str_replace("var Finished = false;", "var Finished = true;", $html);
	$html = str_replace("onload=\"TimerStartUp()\"", "", $html);
	}

	/**
	 * On modifie le bouton "=>" (quand il existe plusieurs fichiers html) afin qu'il passe par le fichier "html.php"
	 * et non plus en ouvrant directement le "fichier.html" 
 	*/
	$iNumeroPageSuivante = $iNumeroPage + 1;
	$insertHTML = "onclick=\"location='html.php?idActiv=%d&idSousActiv=%d&IdExercice=%d&IdHotpot=%d&NumeroPage=%d&fi=";
	$html = str_replace(
					"onclick=\"location='",
					sprintf($insertHTML, $IdActiv, $IdSousActiv, $IdSessionExercice, $IdHotpot, $iNumeroPageSuivante),
					$html );
	/**
 	* On modifie le bouton "<=" sinon il utilise la fonction history.back(), mais cela entraine une perte de l'id de la session pour cet exercice
 	* On prend le numero de page actuelle et on enlève 1.
 	*/
	$iNumeroPagePrecedente = $iNumeroPage-1;
	$sNomFichierPrecedent = $_COOKIE['Page'.$iNumeroPagePrecedente];
	$ModifierReferrer = "onclick=\"location='html.php?idActiv=%d&idSousActiv=%d&IdExercice=%d&IdHotpot=%d&NumeroPage=%d&fi=%s'";
	$html = str_replace(
					"onclick=\"history.back()",
				sprintf($ModifierReferrer, $IdActiv, $IdSousActiv, $IdSessionExercice, $IdHotpot, $iNumeroPagePrecedente,$sNomFichierPrecedent),
					$html );

	print $html;
	exit();
}


/**
 * \brief Fournit file_put_contents() avant PHP5
 *
 * @author		Aidan Lister <aidan@php.net>
 * @version		$Revision: 1.25 $
 * @internal	resource_context is not supported
 * @since		PHP 5
 */
/* @cond NEVER */
if (!function_exists('file_put_contents')) {
/* @endcond */
	function file_put_contents($filename, $content, $flags = null, $resource_context = null)
	{
		// If $content is an array, convert it to a string
		if (is_array($content)) {
			$content = implode('', $content);
		}

		// If we don't have a string, throw an error
		if (!is_scalar($content)) {
			user_error('file_put_contents() The 2nd parameter should be either a string or an array',
				E_USER_WARNING);
			return false;
		}

		// Get the length of data to write
		$length = strlen($content);

		// Check what mode we are using
		$mode = ($flags & FILE_APPEND) ?
					'a' :
					'wb';

		// Check if we're using the include path
		$use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
					true :
					false;

		// Open the file for writing
		if (($fh = @fopen($filename, $mode, $use_inc_path)) === false) {
			user_error('file_put_contents() failed to open stream: Permission denied',
				E_USER_WARNING);
			return false;
		}

		// Attempt to get an exclusive lock
		$use_lock = ($flags & LOCK_EX) ? true : false ;
		if ($use_lock === true) {
			if (!flock($fh, LOCK_EX)) {
				return false;
			}
		}

		// Write to the file
		$bytes = 0;
		if (($bytes = @fwrite($fh, $content)) === false) {
			$errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
							$length,
							$filename);
			user_error($errormsg, E_USER_WARNING);
			return false;
		}

		// Close the handle
		@fclose($fh);

		// Check all the data was written
		if ($bytes != $length) {
			$errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
							$bytes,
							$length);
			user_error($errormsg, E_USER_WARNING);
			return false;
		}

		// Return length
		return $bytes;
	}
}

?>