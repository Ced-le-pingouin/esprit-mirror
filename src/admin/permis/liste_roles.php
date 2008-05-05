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
** Fichier ................: liste_inscrits.php
** Description ............:
** Date de création .......: 17/09/2002
** Dernière modification ..: 13/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();


// ---------------------
// Récupérer les variables de l'url
// ---------------------
$iIdForm     = (empty($_GET["idform"]) ? 0 : $_GET["idform"]);
$iStatutPers = (empty($_GET["STATUT_PERS"]) ? STATUT_PERS_ETUDIANT : $_GET["STATUT_PERS"]);
$iIdPers     = (empty($_GET["IDPERS"]) ? 0 : $_GET["IDPERS"]);
$iIdMod      = (empty($_GET["ID_MOD"]) ? 0 : $_GET["ID_MOD"]);
$sAction     = (empty($_GET["ACTIOND"]) ? NULL : $_GET["ACTIOND"]);

if ($sAction == "effacer" && $iIdPers > 0){
 
   if($iIdPers == 1){
      echo "<span style='color:red'>Ce compte administrateur ne peut pas être supprimé !</span>";
   }
   else {
      $oResp = new CProjet_Admin($oProjet->oBdd);			
		
      $oResp->effacer($iIdPers);
			
      $oResp = NULL;

   }	
}

$iNbAdmins = $oProjet->initAdministrateurs();
$sNomsInscrits = "";
if ($iNbAdmins > 0){
   foreach ($oProjet->aoAdmins as $oAdmin){
      $sNomsInscrits = $sNomsInscrits."<tr><td width=\"1%\"> <input type=\"radio\" name=\"IDPERS\" onfocus=\"blur()\""; 
      $sNomsInscrits = $sNomsInscrits." value ='". $oAdmin->retId ()."'  /></td><td>".$oAdmin->retNomComplet(TRUE)."</td></tr>"; 	   
	
   }
}
else{
$sNomsInscrits = "Aucun compte administrateur";
}


?>
<html>
<head>
		<?php inserer_feuille_style("admin/personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--
function init() {<?php echo (isset($sMajListeCours) ? " {$sMajListeCours} " : NULL); ?>}
//-->
</script>
</head>
<body style="background-color: #FFFFFF;" onload="init()" class="associer_personnes">
<form method="get">
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<?php 
echo $sNomsInscrits;
?>
</table>
<input type="hidden" name="ACTIOND" value="effacer">
<input type="hidden" name="STATUT_PERS" value="<?php echo $iStatutPers; ?>">
<input type="hidden" name="ID_MOD" value="<?php echo $iIdMod; ?>">
<input type="hidden" name="idform" value="<?php echo $iIdForm; ?>">
</form>
<?php
if ($sAction == "effacer")
	echo "<script language=\"javascript\">\noFrmPersonne().document.forms[0].submit();\n</script>"
?>
</body>
</html>

