<?php
require_once dirname(__FILE__).'/globals.inc.php';
require_once dir_database('formation.tbl.php', TRUE);
require_once dir_database('module.tbl.php', TRUE);
require_once dir_database('rubrique.tbl.php', TRUE);
require_once dir_database('activite.tbl.php', TRUE);
require_once dir_database('sous_activite.tbl.php', TRUE);

class ElementFormation
{
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
