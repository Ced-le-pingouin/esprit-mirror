<?php

/*
** Fichier ................: bdd.class.php
** Description ............: 
** Date de cr�ation .......: 05/12/2002
** Derni�re modification ..: 25/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_code_lib("bdd_mysql.class.php"));
require_once(dir_include("config.inc"));				// Information � propos de la base de donn�es

/**
 * Me documenter svp
 * @class CBdd
 */
class CBdd extends CBddMySql
{
	function CBdd ()
	{
		global $g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd;
		$this->CBddMySql($g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd);
	}
	
	function detruire () { $this = NULL; }
}

?>
