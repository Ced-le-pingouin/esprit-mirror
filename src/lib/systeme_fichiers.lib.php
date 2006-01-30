<?php

/*
** Librairie ..............: systeme_fichiers.lib.php
** Description ............:
** Date de création .......: 24/08/2004
** Dernière modification ..: 03/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

function mkdirr($pathname, $mode="755")
{
    // Check if directory already exists
    if (@is_dir($pathname) || empty($pathname)) {
        return TRUE;
    }
	
    // Ensure a file does not already exist with the same name
    if (@is_file($pathname)) {
        return FALSE;
    }
	
    // Crawl up the directory tree
    $next_pathname = substr($pathname, 0, strrpos($pathname, '/'));
	
    if (mkdirr($next_pathname, $mode)) {
        if (!@file_exists($pathname)) {
            return @mkdir($pathname, $mode);
        }
    }
	
    return FALSE;
}

function copier_repertoire ($v_sRepSrc,$v_sRepDst)
{
	$sChemin = getcwd();
	
	if (!is_dir($v_sRepDst))
		@mkdir($v_sRepDst,0777);
	
	if (!is_dir($v_sRepSrc))
		return;
	
	copier_fichiers($v_sRepSrc,$v_sRepDst);
	
	chdir($sChemin);
}

function copier_fichiers ($v_sRepSrc,$v_sRepDst)
{
  	if (!is_dir($v_sRepDst))
		mkdir($v_sRepDst,0777);
	
	if (is_dir($v_sRepSrc))
	{
		chdir($v_sRepSrc);
		
		$handle = opendir(".");
		
		while (($file = readdir($handle)) !== FALSE)
		{
			if (($file != ".") && ($file != ".."))
			{
				if (is_dir($file))
				{
					copier_fichiers("{$v_sRepSrc}/{$file}","{$v_sRepDst}/{$file}");
					chdir($v_sRepSrc);
				}
				
				// Ne copie pas les fichiers php
				if (is_file($file) && !ereg("\.php$",$file))
				{
					copy("{$v_sRepSrc}/{$file}","{$v_sRepDst}/{$file}");
				}
			}
		}
		
		closedir($handle);
	}
}

function vider_repertoire ($v_sRepertoire,$sFiltre=NULL)
{
	if (($hRepertoire = @opendir($v_sRepertoire)) === FALSE)
		return;
	
	while (($sFichier = @readdir($hRepertoire)) !== FALSE)
		if ($sFiltre == NULL)
			@unlink($v_sRepertoire.$sFichier);
		else if (ereg($sFiltre,$sFichier))
			@unlink($v_sRepertoire.$sFichier);
	
	@closedir($hRepertoire);
}

function effacer_repertoire ($v_sRepEffacer)
{
	$v_sRepEffacer = ereg_replace("/\$","",$v_sRepEffacer);
	
	if (is_dir($v_sRepEffacer))
	{
		$handle = opendir($v_sRepEffacer);
		
		while (($file = readdir($handle)) !== FALSE)
			if (($file != ".") && ($file != ".."))
				effacer_repertoire($v_sRepEffacer."/".$file);
		
		closedir($handle);
		
		@rmdir($v_sRepEffacer);
	}
	else
	{
		@unlink($v_sRepEffacer);
	}
}

function ret_taille_fichier ($v_iTaille)
{
	$sType = "bytes";
	
	if ($v_iTaille > '1023') { $v_iTaille = $v_iTaille/1024; $sType = "Ko";	}
	if ($v_iTaille > '1023') { $v_iTaille = $v_iTaille/1024; $sType = "Mo"; }
	if ($v_iTaille > '1023') { $v_iTaille = $v_iTaille/1024; $sType = "Go"; }
	if ($v_iTaille > '1023') { $v_iTaille = $v_iTaille/1024; $sType = "To";	}
	
	if ($v_iTaille < '10') $v_iTaille = intval($v_iTaille*100)/100;
	else if ($v_iTaille < '100') $v_iTaille = intval($v_iTaille*10)/10;
	else $v_iTaille = intval($v_iTaille);
	
	// Remplacer le point par une virgule
	$v_iTaille = str_replace("." , "," , $v_iTaille);
	
	return "{$v_iTaille} {$sType}";
}

?>
