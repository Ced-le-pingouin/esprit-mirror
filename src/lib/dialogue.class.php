<?php

require_once("globals.inc.php");

class CDialog {
	
	var $oTplIndex;
	var $oTplMenu;
	
	var $aoElementsMenu;
	
	function CDialog () {
		$this->init();
	}
	
	function init () {
		$this->oTplIndex = new Template(dir_theme("dialog_simple-index.tpl"));
		$this->oTplMenu = new Template(dir_theme("dialog_simple-Menu.tpl"));
		
		$this->aoElementsMenu = array();
	}
	
	function afficher () {
	}
	
	function afficherIndex () {
		$this->oTplIndex->afficher();
	}
	
	function afficherMenu () {
		$this->oTplMenu->afficher();
	}
}

class CDialogMenu {
	function CDialogMenu () {
	}
}

?>
