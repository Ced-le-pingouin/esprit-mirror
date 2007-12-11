<?php

/**
 * \brief 
 *
 * @param $nomFichier nom du fichier à modifier
 * @return      nom du fichier produit, ou FALSE en cas d'erreur
 */
function hotpot_patch_file( $nomFichier, $IdHotpot ) {
	global $oProjet;
	$nouveauNomFichier = preg_replace('/\.html?$/','_HP-Esprit_.html',$nomFichier);
	if (file_exists($nouveauNomFichier)) {
		unlink($nouveauNomFichier);
	}
	$html = file_get_contents($nomFichier);
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

function ShowMessage(Feedback){
	xhr.open("GET","%s?action=hotpotScore&IdHotpot=%d&IdPers=%d&Score="+Score,true);
	xhr.send(null);
// CODE ESPRIT : FIN
ENDOFTEXT;
	$html = str_replace(
			"function ShowMessage(Feedback){",
			sprintf($insertJS, dir_http_plateform('ajax.php'), $IdHotpot, $oProjet->oUtilisateur->retId()),
			$html );
	// ...
	file_put_contents($nouveauNomFichier, $html);
	return $nouveauNomFichier;
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