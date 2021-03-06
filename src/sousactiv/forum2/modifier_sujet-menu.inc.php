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

if ($url_sModaliteFenetre == "ajouter")
{
	$aMenus = array(
		array("Déposer","top.oPrincipale().envoyer()")
		, array("Annuler","top.close()")
	);
}
else if ($url_sModaliteFenetre == "modifier")
{
	$aMenus = array(
		array("Valider","top.oPrincipale().envoyer()")
		, array("Annuler","top.close()")
	);
}
else if ($url_sModaliteFenetre == "supprimer")
{
	$aMenus = array(
		array("Oui","top.oPrincipale().envoyer()")
		, array("Non","top.close()")
	);
}
?>
