<?php
require_once 'globals.inc.php';
require_once 'admin_globals.inc.php';
require_once dir_include('nav_fichiers/NavigateurFichiers.php');

class GererFichiersForm extends NavigateurFichiers
{
	var $sFiltreFichiers = '%(?:[/\\\\]|^)(?:activ_[0-9]+|chatlog|forum|html\.php|ressources|rubriques|tableaudebord\.csv)$%i';
	
	function recupererDonnees()
	{
		$oProjet = new CProjet();
		if ($oProjet->verifModifierFormation())
			$this->aDonneesUrl['r'] = $oProjet->oFormationCourante->retDossier();
		
		parent::recupererDonnees();
	}
}

$page = new GererFichiersForm();
$page->demarrer(dir_include('nav_fichiers/NavigateurFichiers.tpl'));
?>
