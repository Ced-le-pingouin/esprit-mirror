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
require_once dir_database('formation.tbl.php'    , TRUE);
require_once dir_database('module.tbl.php'       , TRUE);
require_once dir_database('rubrique.tbl.php'     , TRUE);
require_once dir_database('activite.tbl.php'     , TRUE);
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
	 * Retourne le type parent d'un type donné
	 * 
	 * @param int $type Le type dont on veut connaître le type parent (direct)  
	 * @return int Le type parent du type passé en paramètre
	 * 
	 * @warning Le TYPE_UNITE (= TYPE_RUBRIQUE ?) est ignoré par cette méthode
	 * @todo Si on détermine que TYPE_UNITE peut être ignoré partout, les 
	 * méthodes typeEstParentDe() et typeEstEnfantDe() pourraient disparaître ou
	 * constituer uniquement un appel à cette méthode
	 */
	function retTypeParent($type)
	{
		if ($type <= TYPE_FORMATION)
			return TYPE_INCONNU;
		
		if ($type == TYPE_UNITE)
			return TYPE_MODULE;
			
		if ($type == TYPE_ACTIVITE)
			return TYPE_RUBRIQUE;
			
		return $type - 1;
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
		
		if ($v_iTypeParent == TYPE_RUBRIQUE)
			return $v_iTypeEnfant == TYPE_ACTIVITE;
		
		if ($v_iTypeParent == TYPE_SOUS_ACTIVITE)
			return FALSE;
		
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
		
		if ($v_iTypeEnfant == TYPE_UNITE)
			return $v_iTypeParent == TYPE_MODULE;
		
		if ($v_iTypeEnfant == TYPE_ACTIVITE)
			return $v_iTypeParent == TYPE_RUBRIQUE || $v_iTypeParent == TYPE_UNITE;
		
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
	
	/**
	 * Indique si un type est ancêtre d'un autre
	 *
	 * @param int $type1 Le type dont il faut déterminer s'il est l'ancêtre du second
	 * @param int $type2 Le type dont il faut déterminer s'il est le descendant du premier
	 * @return bool \c true si $type1 est ancêtre de $type2, \c false sinon
	 */
	function typeEstAncetreDe($type1, $type2)
	{
		if ($type1 == TYPE_INCONNU || $type2 == TYPE_INCONNU)
			return false;
			
		if ($type1 == TYPE_RUBRIQUE)
			return $type2 > TYPE_UNITE;
		
		return ($type1 < $type2);
	}
	
	/**
	 * Indique si un type est descendant d'un autre
	 *
	 * @param int $type1 Le type dont il faut déterminer s'il est le descendant du second
	 * @param int $type2 Le type dont il faut déterminer s'il est l'ancêtre du premier
	 * @return bool \c true si $type1 est descendant de $type2, \c false sinon
	 */
	function typeEstDescendantDe($type1, $type2)
	{
		return ElementFormation::typeEstAncetreDe($type2, $type1);
	}
	
	/**
	 * Copie un élément de formation vers un autre, de la façon la plus 
	 * automatisée possible, càd même si le type de l'élément de destination 
	 * n'est pas un parent direct du type de l'élément source (par ex. copier 
	 * une activité vers une formation) 
	 *
	 * @param mixed $elemSrc
	 * @param mixed $elemDst
	 */
	function copier($elemSrc, $elemDst)
	{
		list($elemDstFinal, $position) =
		 ElementFormation::_trouverCibleCopie($elemSrc, $elemDst);
		
		if (!is_null($elemDstFinal))
			$elemSrc->copierAvecNumOrdre($elemDstFinal->retId(), $position);
	}
	
	/**
	 * A partir d'une paire "élément source"/"élément destination" de n'importe
	 * quel "type" (formation, module etc.), détermine l'élément destination
	 * exact vers lequel une copie peut s'effectuer (type élément destination = 
	 * parent du type élément source), ainsi que la position occupée par la
	 * copie dans cet élément 
	 *
	 * @param mixed $elemSrc
	 * @param mixed $elemDst
	 * @return array|null
	 */
	function trouverCibleCopie($elemSrc, $elemDst)
	{
		// si type dest = parent direct de type src, la copie est directe, avec n° ordre = 1 
		if (ElementFormation::typeEstParentDe($elemDst->retTypeNiveau(),
		                                      $elemSrc->retTypeNiveau()))
			return array($elemDst, 1);
		
		// sinon, si type dest = ancêtre type src, on descend l'arborescence jusqu'au 1er "parent" dest possible pour la copie de l'élément
		if (ElementFormation::typeEstAncetreDe($elemDst->retTypeNiveau(),
		                                       $elemSrc->retTypeNiveau()))
        {
   			$cible = $elemDst;
   			$a = LIEN_UNITE;
        	
   			$itr = new IterateurRecursif(new IterateurElementFormation($cible),
			                             ITR_REC_PARENT_AVANT);
			for (; $itr->estValide(); $itr->suiv())
			{
				$cible = $itr->courant();
				if (ElementFormation::typeEstParentDe($cible->retTypeNiveau(), $elemSrc->retTypeNiveau())
				     && $cible->estConteneur())
					return array($cible, 1);
			}
		}
		else
		{
			$cible = $elemDst;

			// si type dest est frère ou descendant de type src, on remonte jusqu'au parent correct pour la copie
			while ($cible->retTypeNiveau() != TYPE_FORMATION)
			{
				$position = $cible->retNumOrdre() + 1;
				$cible = ElementFormation::retElementFormation($cible->oBdd, ElementFormation::retTypeParent($cible->retTypeNiveau()), $cible->retIdParent());
				
				if (ElementFormation::typeEstParentDe($cible->retTypeNiveau(), $elemSrc->retTypeNiveau()))
					return array($cible, $position);
			}
		}
		
		return null;
	}
}
?>
