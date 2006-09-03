<?php
/*
 * - la structure de certaines tables diffère-t-elle entre Esprit 1.0 et Esprit 2.0 ?
 * - les mots de passe sont cryptés dans un champ PASSWORD de MySQL pre-4.1, alors qu'Esprit 2.0 est censé fonctionner 
 *   dans MySQL 4.1+
 * - il faut bien entendu récupérer les fichiers des formations également
 * - attention à l'encodage ? 
 * 
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
define('ENCODAGE', 'utf-8');

if (defined('AFFICHAGE_HTML'))
{
	define('EN_TETE',"<html>\n<head><title>Exportation</title></head>\n<body style=\"overflow: auto;\">\n");
	define('PIED_DE_PAGE',"\n</body>\n</html>");
	define('MIME', "text/html");
	define('LF', "<br />");
	define('TAB', "&nbsp;&nbsp;&nbsp;&nbsp;");
	define('DEBUT_EMPHASE', '<em>');
	define('FIN_EMPHASE', '</em>');
	define('ECHAPPER_PAR_DEFAUT', TRUE);
	define('FCT_ECHAPPER', 'htmlentities');
	$asParamsFctEchapper = array(ENT_COMPAT, ENCODAGE);
}
else
{
	define('EN_TETE',"");
	define('PIED_DE_PAGE',"");
	define('MIME', "text/plain");
	define('LF', "\n");
	define('TAB', "\t");
	define('DEBUT_EMPHASE', '*');
	define('FIN_EMPHASE', '*');
	define('ECHAPPER_PAR_DEFAUT', FALSE);
	define('FCT_ECHAPPER', 'addslashes');
}

header('content-type: '.MIME.'; charset='.ENCODAGE);

function aff($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT)
{
	global $asParamsFctEchapper;
	
	if ($v_bEchapper)
	{
		if (isset($asParamsFctEchapper) && count($asParamsFctEchapper))
			print call_user_func_array(FCT_ECHAPPER, array_merge($v_sTexte, $asParamsFctEchapper));
	}
	else
	{
		print $v_sTexte;
	}
}
function affln($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { aff($v_sTexte, $v_bEchapper); aff(LF, FALSE); }

function affd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { if (defined('DEBUG')) aff($v_sTexte, $v_bEchapper); }
function afflnd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { if (defined('DEBUG')) affln($v_sTexte, $v_bEchapper); }

function retChaineNiv($v_iNiveau) { return str_repeat(TAB, $v_iNiveau); }
function _affniv($v_iNiveau) { aff($this->retChaineNiv($v_iNiveau), FALSE); }

function affniv($v_iNiveau, $v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv($v_iNiveau); aff($v_sTexte, $v_bEchapper); }
function affnivln($v_iNiveau, $v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv($v_iNiveau); affln($v_sTexte, $v_bEchapper); }
function affnivd($v_iNiveau, $v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv($v_iNiveau); affd($v_sTexte, $v_bEchapper); }
function affnivlnd($v_iNiveau, $v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { $this->_affniv($v_iNiveau); afflnd($v_sTexte, $v_bEchapper); }


define('TABLE_SRC' , 0);
define('CHAMPS_SRC' , 1);
define('TABLE_DEST' , 2);
define('CHAMPS_DEST' , 3);

class CExport
{
	var $sFichierClesPrimaires = 'refs_db/cles_primaires.csv';
	var $sFichierRef           = 'refs_db/src_reference_dest.csv';
	var $sFichierRefPar        = 'refs_db/src_referencee_par_dest.csv';
	var $asCommentairesFichiers;
	
	var $sHote;
	var $sUser;
	var $sMdp;
	var $sBase;
	var $hLien;
	
	var $aaTables;
	
	function CExport()
	{
		// initialisations
		$this->sHote = $GLOBALS['g_sNomServeur'];
		$this->sUser = $GLOBALS['g_sNomProprietaire'];
		$this->sMdp  = $GLOBALS['g_sMotDePasse'];
		$this->sBase = $GLOBALS['g_sNomBdd'];
		$this->asCommentairesFichiers = array('#', '//');
		
		// les infos sur les clés primaire, étrangères, et relations parent-enfant entre tables/champs ont été extraites 
		// à l'avance et placée dans des fichiers CSV
		$this->lireFichierClesPrimaires();
		$this->lireFichierParents();
		$this->lireFichierEnfants();
		
		// connexion à la DB
		$this->connecterDb();
	}
	
	function lireFichierClesPrimaires()
	{
		$sLignes = file_get_contents($this->sFichierClesPrimaires)
		  or die("Problème d'ouverture du fichier des clés primaires ({$this->sFichierClesPrimaires})");
		$asLignes = explode("\n", $sLignes);
		for ($i = 0, $j = 0; $i < count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			if (!empty($sLigne) && !$this->estCommentee($sLigne))
			{
				$asChamps = explode(';', $sLigne);
				$this->defClePrimaire($asChamps[TABLE_SRC], $asChamps[1]);
			}
		}
	}
	
	function lireFichierParents()
	{
		$sLignes = file_get_contents($this->sFichierRef) 
		  or die("Problème d'ouverture du fichier des clés étrangères ({$this->sFichierRef})");
		$asLignes = explode("\n", $sLignes);
		for ($i = 0, $j = 0; $i < count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			if (!empty($sLigne) && !$this->estCommentee($sLigne))
			{
				$asChamps = explode(';', $sLigne);
				$this->aaTables[$asChamps[TABLE_SRC]]['parents'][] = $asChamps;
			}
		}
	}
	
	function lireFichierEnfants()
	{
		$sLignes = file_get_contents($this->sFichierRefPar)
		  or die("Problème d'ouverture du fichier des clés étrangères inversées ({$this->sFichierClesRefPar})");
		$asLignes = explode("\n", $sLignes);
		for ($i = 0, $j = 0; $i < count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			if (!empty($sLigne) && !$this->estCommentee($sLigne))
			{
				$asChamps = explode(';', $sLigne);
				$this->aaTables[$asChamps[TABLE_SRC]]['enfants'][] = $asChamps;
			}
		}
	}
	
	function ecrireFichierResultat($v_sNomFichier, $v_iNbPassages)
	{
		$hFichier = fopen($v_sNomFichier, 'wb')
		  or die('Impossible de créer le fichier résultat!');
		
		$sContenuFichier = '';
		foreach (array_keys($this->aaTables) as $sNomTable)
		{
			if ($this->aEnregsAExporter($sNomTable))
			{
				$asEnregsAExporter = $this->retEnregsAExporter($sNomTable);
				$sContenuFichier .=
					"$sNomTable (".count($asEnregsAExporter).")".
//					":\n".implode("\n", $asEnregsAExporter).
					"\n"
					;
			}
		}
				
		$sContenuFichier .= "\nNb de passages : $v_iNbPassages\n";
		
		fwrite($hFichier, $sContenuFichier);
		fclose($hFichier);
	}
	
	function estCommentee($v_sLigne)
	{
		foreach ($this->asCommentairesFichiers as $sCommentaire)
			if (substr($v_sLigne, 0, strlen($sCommentaire)) == $sCommentaire)
				return TRUE;
			
		return FALSE;
	}
	
	
	function connecterDb()
	{
		$this->hLien = @mysql_connect($this->sHote, $this->sUser, $this->sMdp)
		  or die('Erreur de connexion au serveur MySQL');
		@mysql_select_db($this->sBase)
		  or die("Erreur de sélection de la DB ($this->sBase)");
	}
	
	function trouverRelations($v_bRecursif = FALSE)
	{
		$i = 0;
		while ($this->aEnregsAjoutesToutesTables())
		{
			$i++;
			
			afflnd(LF.LF.DEBUT_EMPHASE.'--- Passage n°'.$i.' --- '.LF.FIN_EMPHASE , FALSE);
			
			$this->reinitEnregsAjoutesToutesTables();
			
			$this->trouverRelationsToutesTables('enfants', $v_bRecursif);
			$this->trouverRelationsToutesTables('parents', $v_bRecursif);
			
			foreach (array_keys($this->aaTables) as $sNomTable)
				if ($this->aEnregsAExporter($sNomTable)) 
					afflnd($sNomTable.' : '.implode(', ', $this->retEnregsAExporter($sNomTable)));
		}
		afflnd(LF.LF.DEBUT_EMPHASE.'*** Nombre de passages : '.$i.' ***'.LF.FIN_EMPHASE , FALSE);
		
		$sSuffixe = $v_bRecursif?'.rec':'';
		$this->ecrireFichierResultat('result'.$sSuffixe.'.txt', $i);
		
		return $i;
	}
	
	function trouverRelationsToutesTables($v_sTypeRel, $v_bRecursif = FALSE)
	{
		foreach (array_keys($this->aaTables) as $sNomTable)
			if ($this->aEnregsAExporter($sNomTable))
				$this->trouverRelationsTable($sNomTable, $v_sTypeRel, $v_bRecursif);
	}
	
	// $v_sTypeRel doit être 'enfants' ou 'parents'
	function trouverRelationsTable($v_sTableSource, $v_sTypeRel, $v_bRecursif = FALSE)
	{
		if ($this->aRelations($v_sTableSource, $v_sTypeRel))
		{
			$sCleSource = $this->retClePrimaire($v_sTableSource);
			$asEnregsAExporter = $this->retEnregsAExporter($v_sTableSource);
			foreach ($this->aaTables[$v_sTableSource][$v_sTypeRel] as $asRel)
			{
				$sChampSource = $asRel[CHAMPS_SRC];
				$sCleDest = $this->ajouterPrefixeCle('d', $this->retClePrimaire($asRel[TABLE_DEST]));
				$sTableDest = $asRel[TABLE_DEST];
				$sChampDest = $asRel[CHAMPS_DEST];
				
				$sRequeteSql =
					 " SELECT"
					."   DISTINCT $sCleDest" // "d.<nom_cle>", le "d." est ajouté à la création de cette chaîne si besoin est (parfois plusieurs champs pour former la clé)
					." FROM"
					."   $sTableDest AS d"
					."   INNER JOIN $v_sTableSource AS s ON (d.$sChampDest = s.$sChampSource)"
					." WHERE"
					// ci-dessous, condition sous forme "s.Index1 IN (...) [AND s.Index2 IN (...) ...]"
					.$this->retConditionCle('s', $sCleSource, $asEnregsAExporter);
					;
				$asIds = $this->executerRequeteSurIds($sRequeteSql);
				if (count($asIds))
				{
					$this->ajouterEnregsAExporter($sTableDest, $asIds);
					$sListeIds = implode(', ', $asIds);
				}
				else
				{
					$sListeIds = NULL;
				}
				
				if (!is_null($sListeIds))
				{
					if ($v_sTypeRel == 'enfants')
					{
						afflnd(
							$v_sTableSource.'<='.$sTableDest.
							' - '.
							$sRequeteSql.
							' : '.
							$sListeIds
						);
					}
					else
					{
						afflnd(
							DEBUT_EMPHASE.
							$v_sTableSource.'=>'.$sTableDest.
							' - '.
							$sRequeteSql.
							' : '.
							$sListeIds.
							FIN_EMPHASE
							, FALSE
						);
					}
				}
				
				if ($v_bRecursif && !is_null($sListeIds))
					$this->trouverRelationsTable($sTableDest, $v_sTypeRel, $v_bRecursif);
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
			aff($v_sRequete);
		
		$r_hResult = mysql_query($v_sRequete)
		  or die(mysql_error());
		
		return $r_hResult;
	}
	
	function libererResult($v_hResult)
	{
		mysql_free_result($v_hResult);
	}
	
	
	function defClePrimaire($v_sTable, $v_sCle)
	{
		$this->aaTables[$v_sTable]['Cle'] = $v_sCle;
	}
	
	function retClePrimaire($v_sTable)
	{
		return $this->aaTables[$v_sTable]['Cle'];
	}
	
	function aRelations($v_sTable, $v_sTypeRel)
	{
		return (boolean)count($this->aaTables[$v_sTable][$v_sTypeRel]);
	}
	
	function reinitEnregsAjoutesToutesTables()
	{
		foreach (array_keys($this->aaTables) as $sNomTable)
			$this->reinitEnregsAjoutes($sNomTable);
	}
	
	function reinitEnregsAjoutes($v_sTable)
	{
		$this->aaTables[$v_sTable]['EnregsAjoutes'] = FALSE;
	}
	
	function defEnregsAjoutes($v_sTable)
	{
		$this->aaTables[$v_sTable]['EnregsAjoutes'] = TRUE;
	}
	
	function aEnregsAjoutesToutesTables()
	{
		foreach (array_keys($this->aaTables) as $sNomTable)
			if ($this->aEnregsAjoutes($sNomTable))
				return TRUE;
		
		return FALSE;
	}
	
	function aEnregsAjoutes($v_sTable)
	{
		return $this->aaTables[$v_sTable]['EnregsAjoutes'];
	}
	
	
	function ajouterEnregsAExporter($v_sTable, $v_asValeurs)
	{
		$r_bEnregAjoute = FALSE;
		
		foreach ($v_asValeurs as $sValeur)
			$bEnregAjoute = $this->ajouterEnregAExporter($v_sTable, $sValeur) || $r_bEnregAjoute;
		
		return $r_bEnregAjoute;
	}
	
	function ajouterEnregAExporter($v_sTable, $v_sValeur)
	{
		$r_bEnregAjoute = FALSE;
		
		if (!is_array($this->aaTables[$v_sTable]['AExporter']))
			$this->aaTables[$v_sTable]['AExporter'] = array();
		
		if (!in_array($v_sValeur, $this->aaTables[$v_sTable]['AExporter']))
		{
			$r_bEnregAjoute = TRUE;
			$this->defEnregsAjoutes($v_sTable);
			$this->aaTables[$v_sTable]['AExporter'][] = $v_sValeur;
		}
		
		return $r_bEnregAjoute;
	}
	
	function aEnregsAExporter($v_sTable)
	{
		return (boolean)(count($this->aaTables[$v_sTable]['AExporter']));
	}
	
	function retEnregsAExporter($v_sTable)
	{
		return $this->aaTables[$v_sTable]['AExporter'];
	}
	
	
	function ajouterPrefixeCle($v_sPrefixe, $v_sCle)
	{
		$asCles = explode(',', $v_sCle);
		for ($i = 0; $i < count($asCles); $i++)
			$asCles[$i] = $v_sPrefixe.'.'.$asCles[$i];
		return implode(', ', $asCles);
	}
	
	function retConditionCle($v_sPrefixe, $v_sCle, $v_asEnregsAExporter)
	{
		$asValeurs = array();
		$asValeursParCle = array();
		
		for ($i = 0; $i < count($v_asEnregsAExporter); $i++)
		{
			$asValeurs = explode('&', $v_asEnregsAExporter[$i]);
			for ($j = 0; $j < count($asValeurs); $j++)
				$asValeursParCle[$j][] = $asValeurs[$j];
		}
		
		$asCles = explode(',', $v_sCle);
		for ($i = 0; $i < count($asCles); $i++)
			$asPartiesCondition[] = $v_sPrefixe.'.'.$asCles[$i].' IN ('.implode(', ', $asValeursParCle[$i]).')';
		
		return ' '.implode(' AND ', $asPartiesCondition);
	}
}
aff(EN_TETE, FALSE);

$oExport = new CExport();
$oExport->ajouterEnregsAExporter('Formation', array(92)); // formation <- liste fixe à exporter (point de départ des requêtes SQL)
$oExport->trouverRelations(FALSE);
affln('Terminé');

aff(PIED_DE_PAGE, FALSE);
?>