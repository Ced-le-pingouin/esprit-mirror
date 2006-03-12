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
** Fichier ................: editeur-exporter.php
** Description ............:
** Date de création .......: 29/06/2004
** Dernière modification ..: 30/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

$url_sTexte            = (empty($HTTP_POST_VARS["edition"]) ? NULL : stripslashes($HTTP_POST_VARS["edition"]));
$url_sNomFichierExport = (empty($HTTP_POST_VARS["f"]) ? "fichier_cfj.txt" : stripslashes($HTTP_POST_VARS["f"]));

$url_sNomFichierExport = str_replace("\"","",$url_sNomFichierExport);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename={$url_sNomFichierExport}");
echo $url_sTexte;
?>
