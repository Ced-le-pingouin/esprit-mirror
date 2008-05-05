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

/**
 * @file	ElementFormation.php
 */
 
require_once dirname(__FILE__).'/globals.inc.php';
require_once dir_database('formation.tbl.php', TRUE);
require_once dir_database('module.tbl.php', TRUE);
require_once dir_database('rubrique.tbl.php', TRUE);
require_once dir_database('activite.tbl.php', TRUE);
require_once dir_database('sous_activite.tbl.php', TRUE);

/**
 * Classe utilitaire pour créer et obtenir des informations sur un "élément de 
 * formation", càd des objets de CFormation, CModule, etc.
 * 
 * @todo	Cette classe devrait normalement devenir une classe de base ("mère")
 * 			dont devraient hériter les classes qui représentent un élément de 
 * 			formation
 */
class ElementFormation
{
	/**
	 * "Fabrique" qui crée un élément de formation en utilisant la classe 
	 * appropriée, selon le type d'élément passé (voir constantes TYPE_...)
	 * 
	 * @param	v_oBdd			l'objet CBdd qui représente la connexion 
	 * 							courante à la DB
	 * @param	v_iTypeElement	le type (niveau) de l'élément à créer
	 * @param	v_iIdElement	l'id de l'élément à créer
	 * 
	 * @return	l'élément crée
	 */
	function retElementFormation(&$v_oBdd, $v_iTypeElement, $v_iIdElement = NULL)
	{
		static $aCorrespondances = array(TYPE_FORMATION     => 'CFormation', 
	                                     TYPE_MODULE        => 'CModule', 
	                                     TYPE_RUBRIQUE      => 'CModule_Rubrique',
	                                     TYPE_ACTIVITE      => 'CActiv',
	                                     TYPE_SOUS_ACTIVITE => 'CSousActiv'
	                                    );
		
		return new $aCorrespondances[$v_iTypeElement]($v_oBdd, $v_iIdElement);
	}
	
	/**
	 * Indique si un élément est de type (niveau) parent au type d'un autre 
	 * élément
	 * 
	 * @param	v_iTypeParent	le type de l'élément dont on veut vérifier s'il 
	 * 							est parent de l'autre élément
	 * @param	v_iTypeEnfant	le type de l'élément dont on veut vérifier si 
	 * 							l'autre est sont parent
	 * 
	 * @return \c true si \c v_iTypeParent est de type parent au type de 
	 * 			v_iTypeEnfant, \c false sinon
	 */
	function typeEstParentDe($v_iTypeParent, $v_iTypeEnfant)
	{
		if ($v_iTypeParent == TYPE_MODULE)
			return $v_iTypeEnfant == TYPE_RUBRIQUE || $v_iTypeEnfant == TYPE_UNITE;
		else if ($v_iTypeParent == TYPE_RUBRIQUE)
			return $v_iTypeEnfant == TYPE_ACTIVITE;
		else if ($v_iTypeParent == TYPE_SOUS_ACTIVITE)
			return FALSE;
		else
			return $v_iTypeParent == ($v_iTypeEnfant-1);
	}
	
	/**
	 * Indique si un élément est de type (niveau) enfant au type d'un autre 
	 * élément
	 * 
	 * @param	v_iTypeEnfant	le type de l'élément dont on veut vérifier s'il 
	 * 							est enfant de l'autre élément
	 * @param	v_iTypeEnfant	le type de l'élément dont on veut vérifier si 
	 * 							l'autre est sont enfant
	 * 
	 * @return \c true si \c v_iTypeEnfant est de type enfant au type de 
	 * 			v_iTypeParent, \c false sinon
	 */
	function typeEstEnfantDe($v_iTypeEnfant, $v_iTypeParent)
	{
		if ($v_iTypeEnfant == TYPE_FORMATION)
			return FALSE;
		else if ($v_iTypeEnfant == TYPE_UNITE)
			return $v_iTypeParent == TYPE_MODULE;
		else if ($v_iTypeEnfant == TYPE_ACTIVITE)
			return $v_iTypeParent == TYPE_RUBRIQUE || $v_iTypeParent == TYPE_UNITE;
		else
			return $v_iTypeEnfant == ($v_iTypeParent+1);
	}
	
	/**
	 * Indique si deux éléments sont de types (niveaux) identiques ou 
	 * équivalents
	 * 
	 * @param	v_iType1	le type du premier élément
	 * @param	v_iType2	le type du second élément
	 * 
	 * @return	\c true si les deux types sont identiques ou équivalents, 
	 * 			\c false sinon
	 */
	function typeEstFrereDe($v_iType1, $v_iType2)
	{
		$idem = array(TYPE_RUBRIQUE, TYPE_UNITE);
		
		if (in_array($v_iType1, $idem))
			return in_array($v_iType2, $idem);
		else
			return $v_iType1 == $v_iType2;
	}
}
?>
