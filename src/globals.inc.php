<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: globals.inc.php
** Description ............: 
** Date de création .......: 17/09/2001
** Dernière modification ..: 14/11/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// Déclaration des fichiers à inclure
// ---------------------
require_once(dir_include("plate_forme.class.php"));
require_once(dir_include("template.inc.php"));
require_once(dir_include("gettext.inc.php"));

/**
 * Description....
 */

function retPageVide () { return dir_root_plateform('blank.php',FALSE); }

// ---------------------
// globales
// ---------------------
function dir_include ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE) { return dir_root_plateform("include/{$v_sFichierAInclure}",$v_bCheminAbsolu); }
function dir_lang ($v_sLang="fr",$v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE) { return dir_root_plateform("lang/{$v_sLang}/{$v_sFichierAInclure}",$v_bCheminAbsolu); }

// *************************************
// REPERTOIRE CONTENANT LES FICHIERS DE DEFINITION
// *************************************

function dir_definition ($v_sFichierAInclure=NULL) { return dir_include("def/$v_sFichierAInclure"); }

// ---------------------
// Librairies communes
// ---------------------
function dir_code_lib ($v_sFichierAInclure=NULL,$v_bCheminHttp=FALSE,$v_bCheminAbsolu=TRUE)
{
	$v_sFichierAInclure = "code_lib/{$v_sFichierAInclure}";
	if ($v_bCheminHttp)
		return dir_http_plateform(dir_lib($v_sFichierAInclure, FALSE));
	else
		return dir_lib($v_sFichierAInclure,$v_bCheminAbsolu);
	//return (($v_bCheminHttp) ? dir_http() : ($v_bCheminAbsolu ? dir_document_root() : "/"))."code_lib/new/$v_sFichierAInclure";
}

function dir_code_lib_ced ($v_sFichierAInclure=NULL,$v_bCheminHttp=FALSE,$v_bCheminAbsolu=TRUE)
{
	return dir_code_lib($v_sFichierAInclure, $v_bCheminHttp, $v_bCheminAbsolu);
	//return (($v_bCheminHttp) ? dir_http() : ($v_bCheminAbsolu ? dir_document_root() : "/"))."code_lib/$v_sFichierAInclure";
}

/**
 * Cette fonction retourne le chemin des librairies.
 *
 * @param v_sFichierAInclure
 * @param v_bCheminAbsolu
 */
function dir_lib ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_root_plateform("lib/{$v_sFichierAInclure}",$v_bCheminAbsolu); }
function dir_tmp ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_root_plateform("tmp/{$v_sFichierAInclure}",$v_bCheminAbsolu); }

// {{{ Base de données
function dir_database ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
{
	return dir_root_plateform("database/{$v_sFichierAInclure}",$v_bCheminAbsolu);
}
// }}}

// *************************************
// PLATE-FORME/FORMATION/RESSOURCES/COURS
// *************************************

function dir_http_plateform ($v_sFichierAInclure=NULL)
{
	if (eregi("^4.2",phpversion()))
	{
		$tmp  = dirname(__FILE__);
		$tmp1 = dir_document_root();
		return (substr($tmp,strlen($tmp1))."/{$v_sFichierAInclure}");
	}
	
	$sChemin = ereg_replace(dir_document_root(),dir_http(),dir_root_plateform());
	
	return ($sChemin."{$v_sFichierAInclure}");
}

/**
 * Cette méthode retourne le chemin absolue du répertoire racine de la
 * plate-forme
 * @param $v_sFichierAInclure string
 * @param $v_bCheminAbsolu    boolean
 * @return
 */
function dir_root_plateform ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
{
	$sChemin = str_replace("\\","/",realpath(dirname(__FILE__))."/");
	if (!$v_bCheminAbsolu) $sChemin = str_replace(dir_document_root(),"/",$sChemin);
	return "{$sChemin}{$v_sFichierAInclure}";
}

function dir_formation ($v_iIdForm=NULL,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
{
	$sFormation = NULL;
	$sCheminAbsolu = dir_root_plateform(NULL,$v_bCheminAbsolu);
	if ($v_iIdForm > 0)
		$sFormation = "f{$v_iIdForm}/";
	return "{$sCheminAbsolu}formation/{$sFormation}{$v_sFichierAInclure}";
}

function dir_cours ($v_iIdActiv,$v_iIdForm=NULL,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
{
	if ($v_iIdActiv < 1) return;
	$sRepFormation = NULL;
	if ($v_iIdForm > 0) $sRepFormation = dir_formation($v_iIdForm,NULL,$v_bCheminAbsolu);
	return "{$sRepFormation}activ_{$v_iIdActiv}/{$v_sFichierAInclure}";
}

function dir_collecticiel ($v_iFormation,$v_iActiv,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	return dir_cours($v_iActiv,$v_iFormation,"ressources/".addslashes($v_sFichierAInclure),$v_bCheminAbsolu);
}

function dir_rubriques ($v_iIdForm,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
{
	$sRepFormation = dir_formation($v_iIdForm,$v_sFichierAInclure,$v_bCheminAbsolu);
	return "{$sRepFormation}rubriques/{$v_sFichierAInclure}";
}

// *************************************
// FICHIER RESSOURCES
// *************************************

function dir_ressources ($v_sFichierAInclure=NULL)
{
	return "ressources/documents/".addslashes($v_sFichierAInclure);
}

function dir_ressources_doc ()
{
	return NULL;
}

function dir_images_communes ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	return dir_root_plateform("images/communes{$v_sFichierAInclure}",$v_bCheminAbsolu);
}

function dir_icones ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	return dir_root_plateform("images/icones/{$v_sFichierAInclure}",$v_bCheminAbsolu);
}

// ---------------------
// OUTILS D'ADMINISTRATION
// ---------------------
function dir_admin ($v_sTypeAdmin=NULL,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	$sChemin = "admin";
	
	if (!empty($v_sTypeAdmin))
		$sChemin .= "/{$v_sTypeAdmin}";
		
	return dir_root_plateform("{$sChemin}/{$v_sFichierAInclure}",$v_bCheminAbsolu);
}

// {{{ Méthodes des thèmes
function dir_theme ($v_sFichierAInclure=NULL,$v_bCheminHttp=FALSE,$v_bCheminAbsolu=FALSE)
{	
	$sCheminTheme = "themes/".THEME."/$v_sFichierAInclure";
	
	if ($v_bCheminHttp)
		return dir_http_plateform($sCheminTheme);
	else
		return dir_root_plateform($sCheminTheme,$v_bCheminAbsolu);
}

function dir_theme_commun ($v_sFichierAInclure=NULL,$v_bCheminHttp=FALSE,$v_bCheminAbsolu=FALSE)
{	
	$sCheminTheme = "themes/commun/{$v_sFichierAInclure}";
	
	if ($v_bCheminHttp)
		return dir_http_plateform($sCheminTheme);
	else
		return dir_root_plateform($sCheminTheme,$v_bCheminAbsolu);
}

function dir_modeles ($v_sTypeModele,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	if (isset($v_sTypeModele))
		$v_sTypeModele = "{$v_sTypeModele}/";
	return dir_formation(NULL,"modeles/{$v_sTypeModele}{$v_sFichierAInclure}",$v_bCheminAbsolu);
}
// }}}

// {{{ Méthodes des chats
function dir_chat ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_sousactiv(LIEN_CHAT,$v_sFichierAInclure,$v_bCheminAbsolu); }
function dir_chat_client ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_chat("client/{$v_sFichierAInclure}",$v_bCheminAbsolu); }
function dir_chat_serveur ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_chat("server/{$v_sFichierAInclure}",$v_bCheminAbsolu); }

/*
 * Cette méthode est devenue obsolète. Veuillez utiliser la nouvelle méthode
 * "dir_chat_archives"
 */
function dir_chat_log ($v_iIdActiv,$v_iIdForm,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_cours($v_iIdActiv,$v_iIdForm,"chatlog/{$v_sFichierAInclure}",$v_bCheminAbsolu); }

function dir_chat_archives ($v_iTypeNiveau,$v_aiIds,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	switch ($v_iTypeNiveau)
	{
		case TYPE_SOUS_ACTIVITE: return dir_cours($v_aiIds["idActiv"],$v_aiIds["idForm"],"chatlog/{$v_sFichierAInclure}",$v_bCheminAbsolu); break;
		case TYPE_RUBRIQUE: return dir_formation($v_aiIds["idForm"],"chatlog/{$v_sFichierAInclure}",$v_bCheminAbsolu); break;
	}
}
// }}}

// *************************************
// VARIABLES APACHE
// *************************************

function dir_http ($v_sSeparateur="/")
{
	return "http://".$_SERVER["HTTP_HOST"].$v_sSeparateur;
}

function dir_document_root ($v_sFichierAInclure=NULL)
{
	// Ex.: /www/htdocs/html/
	$sDocumentRoot = str_replace("\\","/",realpath($_SERVER["DOCUMENT_ROOT"]));
	return ("{$sDocumentRoot}/{$v_sFichierAInclure}");
}

// *************************************
// Répertoires des formations
// *************************************

function dir_root_formation ($v_sFichierAInclure=NULL)
{
	// Ex.: /www/htdocs/html/esprit/formation/
	return (dir_root_plateform()."/formation/{$v_sFichierAInclure}");
}

// *************************************
// Répertoire des fonctions globales en JavaScript
// *************************************

function dir_javascript ($v_sFichierAInclure=NULL)
{
	// Ex.: /esprit/js/
	return (dir_root_plateform("js/{$v_sFichierAInclure}",FALSE));
}

function dir_template ($v_sSousRepertoire=NULL,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=TRUE)
{
	if (isset($v_sSousRepertoire))
		$v_sSousRepertoire .= "/";
	return (dir_root_plateform("templates/{$v_sSousRepertoire}{$v_sFichierAInclure}",$v_bCheminAbsolu));
}

// ---------------------
// Forum
// ---------------------
function dir_forum ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE) { return dir_sousactiv(LIEN_FORUM,$v_sFichierAInclure,$v_bCheminAbsolu); }

function dir_sousactiv ($v_iLienSousActiv=NULL,$v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
{
	switch ($v_iLienSousActiv)
	{
		case LIEN_PAGE_HTML: $sLienSousActiv = "lien/"; break;
		case LIEN_FORUM: $sLienSousActiv = "forum2/"; break;
		case LIEN_CHAT: $sLienSousActiv = "chat/"; break;
		case LIEN_COLLECTICIEL: $sLienSousActiv = "collecticiel/"; break;
		case LIEN_GALERIE: $sLienSousActiv = "galerie/"; break;
		case LIEN_FORMULAIRE: $sLienSousActiv = "formulaire/"; break;
		case LIEN_GLOSSAIRE: $sLienSousActiv = "glossaire/"; break;
		default: $sLienSousActiv = NULL;
	}
	return dir_root_plateform("sousactiv/{$sLienSousActiv}{$v_sFichierAInclure}",$v_bCheminAbsolu);
}

function MySQLEscapeString ($v_sTexte,$v_sTexteParDefaut=NULL)
{
	$v_sTexte = mysql_real_escape_string(trim(stripslashes($v_sTexte)));
	return (strlen($v_sTexte) > 0 ? $v_sTexte : $v_sTexteParDefaut);
}

/*! remplace htmlentities() vers l'UTF-8
 Attention : htmlentities marche parfois mal avec de l'UTF-8 en PHP4 */
function emb_htmlentities ($v_sTexte, $quote_style=ENT_COMPAT)
{
	//return htmlentities($v_sTexte,ENT_COMPAT,"UTF-8");
   	$v_sTexte = str_replace(array('<','>'), array('&lt;','&gt;'), $v_sTexte);
	switch ($quote_style) {
		case ENT_QUOTES:
			return str_replace(array('"',"'"), array('&quot;','&#039;'),mb_convert_encoding($v_sTexte,"HTML-ENTITIES","UTF-8"));
		case ENT_NOQUOTES:
			return mb_convert_encoding($v_sTexte,"HTML-ENTITIES","UTF-8");
		case ENT_COMPAT:
		default:
			return str_replace('"','&quot;',mb_convert_encoding($v_sTexte,"HTML-ENTITIES","UTF-8"));
	}
}

/*! convertit un texte pour être exporté vers du javascript */
function phpString2js ($text) {
	// "é'àç" -> "%26%2300233%3B%27%26%2300224%3B%26%2300231%3B"
    //return rawurlencode(utf8ToUnicodeSequences($text));
	// "é'àç" -> "\u00e9\'\u00e0\u00e7"
	return utf8ToUnicodeSequences(addslashes($text),false);
}

/*! convertit un texte UTF-8 en HTML ascii avec entitées &#XXX; */
function utf8ToUnicodeSequences ($source, $html=true) { 
    // array used to figure what number to decrement from character order value  
    // according to number of characters used to map unicode to ascii by utf-8 
    $decrement[4] = 240; 
    $decrement[3] = 224; 
    $decrement[2] = 192; 
    $decrement[1] = 0; 
     
    // the number of bits to shift each charNum by 
    $shift[1][0] = 0; 
    $shift[2][0] = 6; 
    $shift[2][1] = 0; 
    $shift[3][0] = 12; 
    $shift[3][1] = 6; 
    $shift[3][2] = 0; 
    $shift[4][0] = 18; 
    $shift[4][1] = 12; 
    $shift[4][2] = 6; 
    $shift[4][3] = 0; 
     
    $pos = 0; 
    $len = strlen ($source); 
    $encodedString = ''; 
    while ($pos < $len) { 
        $asciiPos = ord (substr ($source, $pos, 1)); 
        if (($asciiPos >= 240) && ($asciiPos <= 255)) { 
            // 4 chars representing one unicode character 
            $thisLetter = substr ($source, $pos, 4); 
            $pos += 4; 
        } 
        else if (($asciiPos >= 224) && ($asciiPos <= 239)) { 
            // 3 chars representing one unicode character 
            $thisLetter = substr ($source, $pos, 3); 
            $pos += 3; 
        } 
        else if (($asciiPos >= 192) && ($asciiPos <= 223)) { 
            // 2 chars representing one unicode character 
            $thisLetter = substr ($source, $pos, 2); 
            $pos += 2; 
        } 
        else { 
            // 1 char (lower ascii) 
            $thisLetter = substr ($source, $pos, 1); 
            $pos += 1; 
        } 

        $thisLen = strlen ($thisLetter); 
        if ($thisLen > 1) { 
            // process the string representing the letter to a unicode entity 
            $thisPos = 0; 
            $decimalCode = 0; 
            while ($thisPos < $thisLen) { 
                $thisCharOrd = ord (substr ($thisLetter, $thisPos, 1)); 
                if ($thisPos == 0) { 
                    $charNum = intval ($thisCharOrd - $decrement[$thisLen]); 
                    $decimalCode += ($charNum << $shift[$thisLen][$thisPos]); 
                } 
                else { 
                    $charNum = intval ($thisCharOrd - 128); 
                    $decimalCode += ($charNum << $shift[$thisLen][$thisPos]); 
                } 

                $thisPos++; 
            } 

			if ($html) {
				if ($thisLen == 1) 
					$encodedLetter = "&#". str_pad($decimalCode, 3, "0", STR_PAD_LEFT) . ';'; 
				else 
					$encodedLetter = "&#". str_pad($decimalCode, 5, "0", STR_PAD_LEFT) . ';'; 
			} else { // javascript
				$encodedLetter = '\u'. str_pad(dechex($decimalCode), 4, "0", STR_PAD_LEFT); 
			}

            $encodedString .= $encodedLetter; 
        } 
        else { 
            $encodedString .= $thisLetter; 
        } 
    } 
    return $encodedString; 
}

function unzip ($v_sDestination,$v_sFichierDecompresser)
{
	$iValeurRetour = NULL;
	
	if (eregi("\.zip\$",$v_sFichierDecompresser))
	{
		$sDirRoot = dir_root_plateform(); // FIX-ME
		$sRepertoireTravail = getcwd();
		@chdir($v_sDestination);
		if (stristr(PHP_OS,"linux"))
		{
			$sUnzip = "{$sDirRoot}bin/linux/unzip -o \"{$v_sFichierDecompresser}\"";
			@exec($sUnzip,$iValeurRetour);
		}
		else
		{
			include_once(dir_lib("unzip.php",TRUE));
			unzip_files($v_sDestination.$v_sFichierDecompresser);
		}
		@chdir($sRepertoireTravail);
	}
	
	return $iValeurRetour;
}

/**
 * Cette fonction insére une ligne de deboggage dans le fichier "errors.log".
 *
 * @param v_sMessage
 * @param v_sNomFichierSource
 * @param v_iNumLigne
 */
function debug ($v_sMessage,$v_sNomFichierSource=NULL,$v_iNumLigne=0)
{
	$v_sMessage = trim($v_sMessage);
	
	if (empty($v_sMessage))
		$v_sMessage = "Chaîne vide";
	
	if (isset($v_sNomFichierSource))
		$v_sNomFichierSource = str_replace(dir_document_root(),"/",$v_sNomFichierSource);
	
	$v_sMessage = rawurlencode($v_sMessage)
		.":"
		.rawurlencode(date("d-m-Y H:i:s"))
		.":"
		.$v_sNomFichierSource
		.":"
		.$v_iNumLigne
		."\n";
	
	$sRepFichierLog = dir_admin("console","log/errors.log",TRUE);
	
	$fp = fopen($sRepFichierLog,"a");
	
	if ($fp)
	{
		fwrite($fp,$v_sMessage);
		fclose($fp);
	}
}

function retParams ($v_iForm=0,$v_iMod=0,$v_iRubrique=0,$v_iUnite=0,$v_iActiv=0,$v_iSousActiv=0) { return "{$v_iForm}:{$v_iMod}:{$v_iRubrique}:{$v_iUnite}:{$v_iActiv}:{$v_iSousActiv}"; }

function erreurFatale ($v_sMessageErreur=NULL)
{
	echo "<html>",
		"<body>",
		"<div align=\"center\"><pre>",gettext('Une erreur fatale est survenue&nbsp;:'),"<br>",
		"<b>",emb_htmlentities($v_sMessageErreur),"</b></pre></div></body></html>";
	exit();
}

function convertLien ($v_sLienConvertir,$v_sId=NULL)
{
	if (strstr($v_sLienConvertir,"[tableaudebord /i]"))
		return "<a"
			.(isset($v_sId) ? " id=\"{$v_sId}\"": NULL)
			." href=\"racine://admin/tableaubord/tableau_bord-index.php"
				."?idNiveau={tableaudebord.niveau.id}"
				."&typeNiveau={tableaudebord.niveau.type}"
				."&idType=0"
				."&idModal=".MODALITE_INDIVIDUEL."\""
			." onclick=\"return tableau_de_bord(this)\""
			." onfocus=\"blur()\""
			." target=\"_blank\""
			.">Tableau de bord</a>";
	else if (strstr($v_sLienConvertir,"[tableaudebord /e]"))
		return "<a"
			.(isset($v_sId) ? " id=\"{$v_sId}\"": NULL)
			." href=\"racine://admin/tableaubord/tableau_bord-index.php"
				."?idNiveau={tableaudebord.niveau.id}"
				."&typeNiveau={tableaudebord.niveau.type}"
				."&idType=0"
				."&idModal=".MODALITE_PAR_EQUIPE."\""
			." onclick=\"return tableau_de_bord(this)\""
			." onfocus=\"blur()\""
			." target=\"_blank\""
			.">Tableau de bord</a>";
	
	return $v_sLienConvertir;
}

function convertBaliseMetaVersHtml ($v_sTexte)
{
	if (strlen($v_sTexte) < 1)
		return NULL;
	
	$v_sTexte = emb_htmlentities(trim(stripslashes($v_sTexte)));
	
	$asMetaTrouver   = array("h1","h2","h3","h4","h5","h6","b","u","i","s","tab","blockquote","center");
	$asMetaRemplacer = array("h1","h2","h3","h4","h5","h6","b","u","i","s","blockquote","blockquote","center");
	
	for ($iIdxMeta=0; $iIdxMeta<count($asMetaTrouver); $iIdxMeta++)	
	{
		$v_sTexte = str_replace("[".$asMetaTrouver[$iIdxMeta]."]","<".$asMetaRemplacer[$iIdxMeta].">",$v_sTexte);
		$v_sTexte = str_replace("[/".$asMetaTrouver[$iIdxMeta]."]","</".$asMetaRemplacer[$iIdxMeta].">",$v_sTexte);
	}
	
	// font normal
	$v_sTexte = str_replace("[n]", "<span style='font-weight: normal;'>", $v_sTexte);
	$v_sTexte = str_replace("[/n]", "</span>", $v_sTexte);
	
	// lien vers un site internet
	$v_sTexte = ereg_replace("\[http://([^[:space:]]*)([[:alnum:]#?/&=])\]","<a href=\"http://\\1\\2\" target=\"_blank\" onfocus=\"blur()\">http://\\1\\2</a>", $v_sTexte);
	
	// Ecrire un e-mail
	$v_sTexte = ereg_replace("\[mailto:[[:space:]]?([^[:space:]]*)([[:alnum:]#?/&=])\]","<a href=\"mailto:\\1\\2\" title=\"".gettext("Envoyer un e-mail")."\" onfocus=\"blur()\">\\1\\2</a>", $v_sTexte);
	
	// Alignements du texte:
	// - à gauche
	$v_sTexte = str_replace("[l]", "<div style='text-align: left;'>", $v_sTexte);
	$v_sTexte = str_replace("[/l]", "</div>", $v_sTexte);
	
	// - au centre
	$v_sTexte = str_replace("[c]", "<div style='text-align: center;'>", $v_sTexte);
	$v_sTexte = str_replace("[/c]", "</div>", $v_sTexte);
	
	// - à droite
	$v_sTexte = str_replace("[r]", "<div style='text-align: right;'>", $v_sTexte);
	$v_sTexte = str_replace("[/r]", "</div>", $v_sTexte);
	
	// - justifié
	$v_sTexte = str_replace("[j]", "<div style='text-align: justify;'>", $v_sTexte);
	$v_sTexte = str_replace("[/j]", "</div>", $v_sTexte);
	
	// Bi-directionnalité
	// Gauche à droite (standard)
	$v_sTexte = preg_replace("/\n\[ltr\](.+?)\[\/ltr\]/", '<div dir="ltr">$1</div>', $v_sTexte);
	$v_sTexte = str_replace("[ltr]", '<span dir="ltr">', $v_sTexte);
	$v_sTexte = str_replace("[/ltr]", "</span>", $v_sTexte);
	// Droite à gauche (arabe, hébreu)
	$v_sTexte = preg_replace("/\n\[rtl\](.+?)\[\/rtl\]/", '<div dir="rtl">$1</div>', $v_sTexte);
	$v_sTexte = str_replace("[rtl]", '<span dir="rtl">', $v_sTexte);
	$v_sTexte = str_replace("[/rtl]", "</span>", $v_sTexte);
	
	// Ajouter un retour à la ligne
	$v_sTexte = str_replace("[nl]", "<br>", $v_sTexte);
	
	// Liste
	while (($iDepart = strpos($v_sTexte,"[liste")) !== FALSE)
	{
		$iFin  = strpos($v_sTexte,"]",$iDepart)+1;
		$iFin2 = strpos($v_sTexte,"[/liste]",$iDepart);
		
		if ($iFin > $iFin2)
			break;
		
		$sBaliseDepart = substr($v_sTexte,$iDepart,($iFin-$iDepart));
		
		// Récupérer le contenu de la balise liste
		$sContenuBalise = trim(substr($v_sTexte,$iFin,($iFin2-$iFin)));
		
		// Composer la liste
		$sListe = NULL;
		foreach (explode("\n",$sContenuBalise) as $sLigne)
			$sListe .= "<li>".trim($sLigne)."</li>";
		
		if (strpos($sBaliseDepart,"1") !== FALSE)
			$sListe = "<ol type=\"1\">{$sListe}</ol>";
		else if (strpos($sBaliseDepart,"&quot;a&quot;") !== FALSE)
			$sListe = "<ol type=\"a\">{$sListe}</ol>";
		else if (strpos($sBaliseDepart,"&quot;A&quot;") !== FALSE)
			$sListe = "<ol type=\"A\">{$sListe}</ol>";
		else if (strpos($sBaliseDepart,"&quot;i&quot;") !== FALSE)
			$sListe = "<ol type=\"i\">{$sListe}</ol>";
		else if (strpos($sBaliseDepart,"&quot;I&quot;") !== FALSE)
			$sListe = "<ol type=\"I\">{$sListe}</ol>";
		else
			$sListe = "<ul>{$sListe}</ul>";
		
		$v_sTexte = trim(substr($v_sTexte,0,$iDepart))
			.$sListe
			.trim(substr($v_sTexte,($iFin2+strlen("[/liste]"))));
	}
	
	// Ligne horizontale
	$v_sTexte = str_replace("[hr]","<hr>",$v_sTexte);
	
	// Tableau de bord
	$v_sTexte = str_replace("[tableaudebord /i]",convertLien("[tableaudebord /i]"),$v_sTexte);
	$v_sTexte = str_replace("[tableaudebord /e]",convertLien("[tableaudebord /e]"),$v_sTexte);
	
	return nl2br($v_sTexte);
}

function enleverBaliseMeta ($v_sTexte)
{
	if (strlen($v_sTexte) < 1)
		return NULL;
	
	//$v_sTexte = emb_htmlentities(trim(stripslashes($v_sTexte)));
	
	$asMetaDebFin  = array("h1","h2","h3","h4","h5","h6","b","u","i","s","tab","blockquote","center", "n","l","c","r","j");
	$asMetaUnique = array("[nl]","[hr]");
	
	for ($iIdxMeta = 0; $iIdxMeta < count($asMetaDebFin); $iIdxMeta++)	
	{
		$v_sTexte = str_replace("[".$asMetaDebFin[$iIdxMeta]."]", "", $v_sTexte);
		$v_sTexte = str_replace("[/".$asMetaDebFin[$iIdxMeta]."]", "", $v_sTexte);
	}
	
	$v_sTexte = str_replace($asMetaUnique, "", $v_sTexte);
	
	// lien vers un site internet
	$v_sTexte = ereg_replace("\[(http://[^[:space:]]*[[:alnum:]#?/&=])\]","\\1", $v_sTexte);
	
	// Ecrire un e-mail
	$v_sTexte = ereg_replace("\[mailto:([^[:space:]]*[[:alnum:]#?/&=])\]","\\1", $v_sTexte);
	
	// Liste
	$v_sTexte = preg_replace("/\[\/?liste.*\]/U","", $v_sTexte);
	
	return $v_sTexte;
}

function emailValide ($v_sEmail) { return ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$v_sEmail); }

function retDateFormatter ($v_sDate,$v_sFormatterDate="d/m/y")
{
	list($sDate,$sTime) = explode(" ",$v_sDate);
	$asDate = explode("-",$sDate);
	$asTime = explode(":",$sTime);
	return date($v_sFormatterDate,mktime($asTime[0],$asTime[1],$asTime[2],$asDate[1],$asDate[2],$asDate[0]));
}

function retTitrePageHtml ($v_sFichierHtml)
{
	if (is_file($v_sFichierHtml))
	{
		$asContenuFichier = file($v_sFichierHtml);
		
		foreach ($asContenuFichier as $sLigne)
			if (ereg("<title>(.*)</title>",$sLigne,$asTmp))
				return mb_convert_encoding($asTmp[1], 'UTF-8', 'HTML-ENTITIES');
	}
	return NULL;
}

function fermerBoiteDialogue ($v_sLigneJavascript=NULL)
{
	echo "<html>\n"
		."<head>\n"
	        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
		."<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n"
		."function fermer() { ".(isset($v_sLigneJavascript) ? "{$v_sLigneJavascript}; " : NULL)."top.close(); }\n"
		."//-->\n"
		."</script>\n"
		."</head>\n"
		."<body onload=\"fermer()\"></body>\n"
		."</html>\n";
}

function echod ($v_sTexte)
{
	$amCookie = retCookie();
	if (isset($amCookie[SESSION_STATUT_ABSOLU])
		&& STATUT_PERS_ADMIN == $amCookie[SESSION_STATUT_ABSOLU])
		echo $v_sTexte;
}

function retCookie ()
{
	global $g_sNomCookie;
	return explode(":",$_COOKIE[$g_sNomCookie]);
}

function dir_locale ($v_sFichierAInclure=NULL,$v_sLocale=NULL)
{
	if (empty($v_sLocale))
	{
		$amCookie = retCookie();
		$v_sLocale = (empty($amCookie[SESSION_LANG]) ? "fr" : $amCookie[SESSION_LANG]);
	}
	return dir_root_plateform()."locale/{$v_sLocale}/{$v_sFichierAInclure}";
}

?>
