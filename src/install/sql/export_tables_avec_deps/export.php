<?php
/*
 * - utiliser uniquement les indices 'enfants' et 'parents' (se débarrasser de 'RefPar' et 'Ref')
 * - transformer ce fouillis de tableaux en objets faciles à utiliser
 * - pour le moment, les infos sur les index et les clés étrangères dans les tables sont générés/rédigés avant
 *   l'exportation, puis lus. Il faudrait pouvoir récupérer ces infos dans la DB à la volée. Pour les clés étrangères, 
 *   c'est difficile étant donné qu'il faut lire dans la DB de phpMyAdmin qui enregistre les relations entre tables 
 *   (et que bien entendu ces relations décrites dans PMA soient à jour). Pour les index, y a-t-il des fonctions PHP 
 *   qui demandent directement des infos sur les tableS/champs/index, ou faut-il utiliser mysql_query pour récupérer 
 *   ces infos en SQL ?
 * - intégrer ces classes d'exportation "pur DB" dans les classes d'Esprit
 */
require_once('../../../include/config.inc');

define('DEBUG', TRUE);
define('AFFICHAGE_HTML', TRUE);

if (defined('AFFICHAGE_HTML'))
{
	define('MIME', "text/html");
	define('LF', "<br />");
	define('TAB', "&nbsp;&nbsp;&nbsp;&nbsp;");
	define('DEBUT_EMPHASE', '<em>');
	define('FIN_EMPHASE', '</em>');
	define('ECHAPPER_PAR_DEFAUT', TRUE);
	define('FCT_ECHAPPER', 'htmlentities');
}
else
{
	define('MIME', "text/plain");
	define('LF', "\n");
	define('TAB', "\t");
	define('DEBUT_EMPHASE', '*');
	define('FIN_EMPHASE', '*');
	define('ECHAPPER_PAR_DEFAUT', FALSE);
	define('FCT_ECHAPPER', 'addslashes');
}

header('content-type: '.MIME.'; charset=utf-8');

function aff($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT)
{
	if ($v_bEchapper)
		print call_user_func(FCT_ECHAPPER, $v_sTexte);
	else
		print $v_sTexte;
}
function affln($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { aff($v_sTexte, $v_bEchapper); aff(LF, FALSE); }

function affd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { if (defined('DEBUG')) aff($v_sTexte, $v_bEchapper); }
function afflnd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { if (defined('DEBUG')) affln($v_sTexte, $v_bEchapper); }

define('TABLE_SRC' , 0);
define('CHAMPS_SRC' , 1);
define('TABLE_DEST' , 2);
define('CHAMPS_DEST' , 3);

class CExport
{
	var $sFichierClesPrimaires = 'refs_db/cles_primaires.csv';
	var $sFichierRef           = 'refs_db/src_reference_dest.csv';
	var $sFichierRefPar        = 'refs_db/src_referencee_par_dest.csv';
	
	var $sHote;
	var $sUser;
	var $sMdp;
	var $sBase;
	
	var $hLien;
	
	var $aaTables;
	
	var $iNiveau = 0;
	
	function CExport()
	{
		$this->sHote = $GLOBALS['g_sNomServeur'];
		$this->sUser = $GLOBALS['g_sNomProprietaire'];
		$this->sMdp  = $GLOBALS['g_sMotDePasse'];
		$this->sBase = $GLOBALS['g_sNomBdd'];
		
		// formation <- liste fixe à exporter (point de départ des requêtes SQL)
		$this->defClesAExporter('Formation', array(92));
		
		// les infos sur les clés primaire, étrangères, et relations parent-enfant entre tables/champs ont été extraites 
		// à l'avance et placée dans des fichiers CSV
		$this->lireFichierClesPrimaires();
		$this->lireFichierRef();
		$this->lireFichierRefPar();
		
		// connexion à la DB
		$this->connecterDb();
		
		$sListeIdsForms = implode(',', $this->retClesAExporter('Formation'));
		afflnd("Formations: $sListeIdsForms");
		
		//$this->trouverRelationsTable('Formation', 'enfants', TRUE);
		
		/* $this->trouverRelationsTable(NULL, 'enfants', TRUE);
		$this->trouverRelationsTable(NULL, 'parents', TRUE); */
		
		$i = 0;
		while ($this->ontNouvellesValeursToutesTables())
		{
			afflnd(LF.LF.DEBUT_EMPHASE.'--- Passage n°'.++$i.' --- '.LF.FIN_EMPHASE , FALSE);
			
			$this->reinitNouvellesValeursToutesTables();
			$this->trouverRelationsToutesTables('enfants', TRUE);
			$this->trouverRelationsToutesTables('parents', TRUE);
		}
		afflnd(LF.LF.DEBUT_EMPHASE.'*** Nombre de passages : '.$i.' ***'.LF.FIN_EMPHASE , FALSE);
	}
	
	function lireFichierClesPrimaires()
	{
		$sLignes = file_get_contents($this->sFichierClesPrimaires)
		  or die("Problème d'ouverture du fichier des clés primaires ({$this->sFichierClesPrimaires})");
		$asLignes = explode("\n", $sLignes);
		for ($i = 0, $j = 0; $i < count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			if (!empty($sLigne))
			{
				$asChamps = explode(';', $sLigne);
				$this->aaTables[$asChamps[TABLE_SRC]]['Cle'] = $asChamps[1];
				//affln('Clé primaire de '.$asChamps[TABLE_SRC].' : '.$this->aaTables[$asChamps[TABLE_SRC]]['Cle'][CHAMPS_SRC]);
			}
		}
	}
	
	function lireFichierRef()
	{
		$sLignes = file_get_contents($this->sFichierRef) 
		  or die("Problème d'ouverture du fichier des clés étrangères ({$this->sFichierRef})");
		$asLignes = explode("\n", $sLignes);
		for ($i = 0, $j = 0; $i < count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			if (!empty($sLigne))
			{
				$asChamps = explode(';', $sLigne);
				$this->aaTables[$asChamps[TABLE_SRC]]['Ref'][] = $asChamps;
				//affln($asChamps[TABLE_SRC].' référence '.$this->aaTables[$asChamps[TABLE_SRC]]['Ref'][TABLE_DEST].'.'.$this->aaTables[$asChamps[TABLE_SRC]]['Ref'][CHAMPS_DEST]);
			}
		}
	}
	
	function lireFichierRefPar()
	{
		$sLignes = file_get_contents($this->sFichierRefPar)
		  or die("Problème d'ouverture du fichier des clé étrangères inversées ({$this->sFichierClesRefPar})");
		$asLignes = explode("\n", $sLignes);
		for ($i = 0, $j = 0; $i < count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			if (!empty($sLigne))
			{
				$asChamps = explode(';', $sLigne);
				$this->aaTables[$asChamps[TABLE_SRC]]['RefPar'][] = $asChamps;
				//affln($asChamps[TABLE_SRC].' référencé par '.$this->aaTables[$asChamps[TABLE_SRC]]['RefPar'][TABLE_DEST].'.'.$this->aaTables[$asChamps[TABLE_SRC]]['RefPar'][CHAMPS_DEST]);
			}
		}
	}
	
	function connecterDb()
	{
		$this->hLien = @mysql_connect($this->sHote, $this->sUser, $this->sMdp)
		  or die('Erreur de connexion au serveur MySQL');
		@mysql_select_db($this->sBase) or die("Erreur de sélection de la DB ($this->sBase)");
	}
	
	function trouverRelationsToutesTables($v_sTypeRel, $v_bRecursif)
	{
		foreach ($this->aaTables as $sNomTable=>$aaTable)
			if ($this->aClesAExporter($sNomTable))
				$this->trouverRelationsTable($sNomTable, $v_sTypeRel, $v_bRecursif);
	}
	
	// $v_sTypeRel doit être 'enfants' (sinon, il équivaut à 'parents')
	function trouverRelationsTable($v_sTableSource, $v_sTypeRel, $v_bRecursif)
	{
		$sTypeRel = ($v_sTypeRel == 'enfants') ? 'RefPar' : 'Ref';
		
		if (count($this->aaTables[$v_sTableSource][$sTypeRel]))
		{
			//$this->iNiveau++;
			
			$sCleSource = $this->aaTables[$v_sTableSource]['Cle'];
			$asClesAExporter = $this->retClesAExporter($v_sTableSource);
			foreach ($this->aaTables[$v_sTableSource][$sTypeRel] as $asRel)
			{
				$sChampSource = $asRel[CHAMPS_SRC];
				$sCleDest = $this->ajouterPrefixeCle('d', $this->aaTables[$asRel[TABLE_DEST]]['Cle']);
				$sTableDest = $asRel[TABLE_DEST];
				$sChampDest = $asRel[CHAMPS_DEST];
				
				$sRequeteSql =
					 " SELECT"
					."   $sCleDest" // "d.<nom_cle>", le "d." est ajouté à la création de cette chaîne si besoin est (parfois plusieurs champs pour former la clé)
					." FROM"
					."   $sTableDest AS d"
					."   INNER JOIN $v_sTableSource AS s ON (d.$sChampDest = s.$sChampSource)"
					." WHERE"
					." ".$this->retConditionCle('s', $sCleSource, $asClesAExporter);
					//."   s.$sCleSource IN ($sListeClesAExporterSource)"
					;
				$asIds = $this->executerRequeteSurIds($sRequeteSql);
				if (count($asIds))
				{
					$this->defClesAExporter($sTableDest, $asIds);
					$sListeIds = implode(',', $asIds);
				}
				else
				{
					$sListeIds = NULL;
				}
				
				if ($v_sTypeRel == 'enfants')
				{
					$this->affnivlnd(
						$v_sTableSource.'<='.$sTableDest.
						' - '.
						$sRequeteSql.
						' : '.$sListeIds
					);
				}
				else
				{
					$this->affnivlnd(
						DEBUT_EMPHASE.
						$v_sTableSource.'=>'.$sTableDest.
						' - '.
						$sRequeteSql.
						' : '.$sListeIds.
						FIN_EMPHASE
						, FALSE
					);
				}
				
				/* if ($v_bRecursif && !is_null($sListeIds))
					$this->trouverRelationsTable($sTableDest, $v_sTypeRel, $v_bRecursif); */
			}
		}
	}
	
	
	function executerRequeteSurIds($v_sRequete)
	{
		$asEnreg = NULL;
		$asIds = array();
		
		$hResult = $this->executerRequete($v_sRequete);
		
		while ($asEnreg = mysql_fetch_row($hResult))
			$asIds[] = implode('&', $asEnreg);
		
		$this->libererResult($hResult);
		
		return $asIds;
	}
	
	function executerRequete($v_sRequete, $v_bAfficher = FALSE)
	{
		if ($v_bAfficher)
			print $v_sRequete;
		
		return mysql_query($v_sRequete);
	}
	
	function libererResult($v_hResult)
	{
		mysql_free_result($v_hResult);
	}
	
	
	function reinitNouvellesValeursToutesTables()
	{
		foreach ($this->aaTables as $sNomTable=>$aaTable)
			$this->reinitNouvellesValeurs($sNomTable);
	}
	
	function reinitNouvellesValeurs($v_sTable)
	{
		$this->aaTables[$v_sTable]['NouvellesValeurs'] = FALSE;
	}
	
	function defNouvellesValeurs($v_sTable)
	{
		$this->aaTables[$v_sTable]['NouvellesValeurs'] = TRUE;
	}
	
	function ontNouvellesValeursToutesTables()
	{
		$r_bNouvellesValeurs = FALSE;
		
		foreach ($this->aaTables as $sNomTable=>$aaTable)
			$r_bNouvellesValeurs = $r_bNouvellesValeurs || $this->aNouvellesValeurs($sNomTable);
		
		return $r_bNouvellesValeurs;
	}
	
	function aNouvellesValeurs($v_sTable)
	{
		return $this->aaTables[$v_sTable]['NouvellesValeurs'];
	}
	
	
	function defClesAExporter($v_sTable, $v_asValeurs)
	{
		$r_bNouvelleValeur = FALSE;
		
		foreach ($v_asValeurs as $sValeur)
			$r_bNouvelleValeur = $r_bNouvelleValeur || $this->defCleAExporter($v_sTable, $sValeur);
		
		return $r_bNouvelleValeur;
	}
	
	function defCleAExporter($v_sTable, $v_sValeur)
	{
		$r_bNouvelleValeur = FALSE;
		
		if (!is_array($this->aaTables[$v_sTable]['AExporter']))
			$this->aaTables[$v_sTable]['AExporter'] = array();
		
		if (!in_array($v_sValeur, $this->aaTables[$v_sTable]['AExporter']))
		{
			$r_bNouvelleValeur = TRUE;
			$this->defNouvellesValeurs($v_sTable);
			$this->aaTables[$v_sTable]['AExporter'][] = $v_sValeur;
		}
		
		return $r_bNouvelleValeur;
	}
	
	function aClesAExporter($v_sTable)
	{
		return (boolean)(count($this->aaTables[$v_sTable]['AExporter']));
	}
	
	function retClesAExporter($v_sTable)
	{
		return $this->aaTables[$v_sTable]['AExporter'];
	}
	
	
	function ajouterPrefixeCle($v_sPrefixe, $v_sCle)
	{
		$asCles = explode(',', $v_sCle);
		for ($i = 0; $i < count($asCles); $i++)
			$asCles[$i] = $v_sPrefixe.'.'.$asCles[$i];
		return implode(',', $asCles);
	}
	
	function retConditionCle($v_sPrefixe, $v_sCle, $v_asClesAExporter)
	{
		$asValeurs = array();
		$asValeursParCle = array();
		
		for ($i = 0; $i < count($v_asClesAExporter); $i++)
		{
			$asValeurs = explode('&', $v_asClesAExporter[$i]);
			for ($j = 0; $j < count($asValeurs); $j++)
				$asValeursParCle[$j][] = $asValeurs[$j];
		}
		
		$asCles = explode(',', $v_sCle);
		for ($i = 0; $i < count($asCles); $i++)
			$asPartiesCondition[] = $v_sPrefixe.'.'.$asCles[$i].' IN ('.implode(',', $asValeursParCle[$i]).')';
		
		return implode(' AND ', $asPartiesCondition);
	}
	
	
	function retChaineNiv() { return str_repeat(TAB, $this->iNiveau); }
	function _affniv() { aff($this->retChaineNiv(), FALSE); }
	
	function affniv($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv(); aff($v_sTexte, $v_bEchapper); }
	function affnivln($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv(); affln($v_sTexte, $v_bEchapper); }
	function affnivd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv(); affd($v_sTexte, $v_bEchapper); }
	function affnivlnd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv(); afflnd($v_sTexte, $v_bEchapper); }
}

$oExport = new CExport();
?>
