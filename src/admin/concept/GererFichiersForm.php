<?php
require_once 'globals.inc.php';
require_once 'admin_globals.inc.php';
require_once dir_include('nav_fichiers/NavigateurFichiers.php');

class GererFichiersForm extends NavigateurFichiers
{
	function recupererDonnees()
	{
		$oProjet = new CProjet();
		if ($oProjet->verifPermission('PERM_MOD_SESSION'))
			$this->aDonneesUrl['r'] = $oProjet->oFormationCourante->retDossier();
		
		parent::recupererDonnees();
	}
}

$page = new GererFichiersForm();
$page->demarrer(dir_include('nav_fichiers/NavigateurFichiers.tpl'));

?>
