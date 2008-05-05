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
 * @file	export.php 
 * 
 * Permet l'exportation en cascade d'enregistrements de la DB, dans ce cas, à partir d'ids de formations
 * 
 * @todo
 * - transformer ce fouillis de tableaux en objets faciles à utiliser
 * - pour le moment, les infos sur les index et les clés étrangères dans les tables sont générés/rédigés avant
 *   l'exportation, puis lus. Il faudrait pouvoir récupérer ces infos dans la DB à la volée. Pour les clés étrangères, 
 *   c'est difficile étant donné qu'il faut lire dans la DB de phpMyAdmin qui enregistre les relations entre tables 
 *   (et que bien entendu ces relations décrites dans PMA soient à jour). Pour les index, y a-t-il des fonctions PHP 
 *   qui demandent directement des infos sur les tableS/champs/index, ou faut-il utiliser mysql_query pour récupérer 
 *   ces infos en SQL ?
 * - intégrer ces classes d'exportation "pur DB" dans les classes d'Esprit
 */

// on utilise les infos de la DB situées dans la config "normale" d'Esprit
require_once('../../../include/config.inc');

//define('DEBUG', TRUE);				/// Affiche des informations de déboguage supplémentaires											@enum DEBUG 
//define('AFFICHAGE_HTML', TRUE);		/// Détermine si l'affichage se fera en HTML ou en texte											@enum AFFICHAGE_HTML

define('ENCODAGE', 'utf-8');			/// Spécifie l'encodage utilisé dans ce fichier (UTF-8)												@enum ENCODAGE 
//define('NB_INDEX_PAR_REQUETE', 20);	/// Si cette constante a une valeur, seul ce nombre d'enregistrements seront ramenés par requête	@enum NB_INDEX_PAR_REQUETE
define('RECHERCHE_RECURSIVE', TRUE);	/// Indique s'il faut respecter l'ordre des tables, ou si une table et ses descendants sont scannés	@enum RECHERCHE_RECURSIVE
$aiFormationsAExporter = array			///< Les ids des formations à exporter se trouvent ici 
(
	20, 40, 41, 42, 44, 48, 55, 68, 81, 82, 92, 93, 94, 95, 96, 99, 
	100, 110, 111, 112, 113, 116, 128, 129, 139, 141, 143, 144, 145, 
	146, 147, 148, 149, 150, 151, 152, 153
);

// définition de quelques constantes et variables en fonction du type de sortie (affichage)
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

// en-tête (à enlever si sortie en texte simple ?)
header('content-type: '.MIME.'; charset='.ENCODAGE);

/**
 * Affiche une chaîne de caractères
 * 
 * @param	$v_sTexte		le texte à afficher
 * @param	$v_bEchapper	si \c true, le texte sera d'abord envoyé à une fonction d'échappement des caractères. 
 * 							La valeur par défaut dépend du type de sortie/affichage (voir constante ECHAPPER_PAR_DEFAUT)
 */
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

/**
 * Affiche une chaîne de caractères et se place à la ligne suivante
 * 
 * @param	$v_sTexte		le texte à afficher
 * @param	$v_bEchapper	si \c true, le texte sera d'abord envoyé à une fonction d'échappement des caractères.
 * 							La valeur par défaut dépend du type de sortie/affichage (voir constante ECHAPPER_PAR_DEFAUT)
 */
function affln($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { aff($v_sTexte, $v_bEchapper); aff(LF, FALSE); }

/**
 * Affiche une chaîne de caractères, uniquement si l'application tourne en mode DEBUG (la constante du même nom doit 
 * exister)
 * 
 * @param	$v_sTexte		le texte à afficher
 * @param	$v_bEchapper	si \c true, le texte sera d'abord envoyé à une fonction d'échappement des caractères.
 * 							La valeur par défaut dépend du type de sortie/affichage (voir constante ECHAPPER_PAR_DEFAUT)
 */
function affd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT) { if (defined('DEBUG')) aff($v_sTexte, $v_bEchapper); }

/**
 * Affiche une chaîne de caractères et se place à la ligne suivante, uniquement si l'application tourne en mode DEBUG 
 * (la constante du même nom doit exister)
 * 
 * @param	$v_sTexte		le texte à afficher
 * @param	$v_bEchapper	si \c true, le texte sera d'abord envoyé à une fonction d'échappement des caractères.
 * 							La valeur par défaut dépend du type de sortie/affichage (voir constante ECHAPPER_PAR_DEFAUT)
 */
function afflnd($v_sTexte, $v_bEchapper = ECHAPPER_PAR_DEFAUT)
{
	if (defined('DEBUG'))
		affln($v_sTexte, $v_bEchapper);
}

/**
 * Retourne le "timestamp" courant (le temps écoulé depuis le 1er janvier 1970), en secondes, sous forme de nombre à 
 * virgule flottante, avec la précision jusqu'aux microsecondes 
 */
function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Débute la session de profiling (le moment courant est enregistré)
 */
function debutProf() { $GLOBALS['_ced_iTempsDebut'] = microtime_float(); }

/**
 * Met fin à la session de profiling, et calcule le temps écoulé depuis le début de la session
 * 
 * @param	$v_bAfficher	si \c true, le temps écoulé est affiché entre parenthèses (\c false par défaut)
 * 
 * @return	le temps écoulé pendant la session de profiling
 */
function finProf($v_bAfficher = FALSE)
{
	$r_iIntervalleTemps = microtime_float() - $GLOBALS['_ced_iTempsDebut'];
	
	if ($v_bAfficher)
		aff(" ({$r_iIntervalleTemps}s)");
		
	return $r_iIntervalleTemps;
}

/** @name Constantes utilitaires de la classe CExportDb */
//@{
define('TABLE_SRC'   , 0);	/// Position dans le tableau du nom de la table	source		@enum TABLE_SRC
define('CHAMPS_SRC'  , 1);	/// Position dans le tableau du nom des champs source		@enum CHAMPS_SRC
define('TABLE_DEST'  , 2);	/// Position dans le tableau du nom de la table	destination	@enum TABLE_DEST
define('CHAMPS_DEST' , 3);	/// Position dans le tableau du nom des champs destination	@enum CHAMPS_DEST
//@}

/**
 * Classe de gestion des exportations d'enregistrements en cascade à partir de la DB
 */
class CExportDb
{
	var $sFichierClesPrimaires = 'refs_db/cles_primaires.csv';			///< Chemin du fichier CSV contenant les infos sur les tables et leurs clés primaires
	var $sFichierRef           = 'refs_db/src_reference_dest.csv';		///< Chemin du fichier CSV contenant les relations enfant->parent entre tables à prendre en compte
	var $sFichierRefPar        = 'refs_db/src_referencee_par_dest.csv';	///< Chemin du fichier CSV contenant les relations parent<-enfant entre tables à prendre en compte
	var $asCommentairesFichiers;										///< Tableau contenant les différents types de commentaires pris en compte dans les fichiers ci-dessus
	
	var $sHote;	///< Hôte où se trouve le serveur MySQL
	var $sUser;	///< Utilisateur à connecter au serveur MySQL
	var $sMdp;	///< Mot de passe pour cet utilisateur
	var $sBase;	///< Base de données concernée par l'exportation
	var $hLien;	///< Handle de connexion à la DB
	
	var $aaTables;	///< Tableau contenant les tables de la DB, leurs noms, relations, et enregistrements à exporter
	
	/**
	 * Constructeur
	 */
	function CExportDb()
	{
		// initialisations (les infos de la DB sont reprises de la configuration d'Esprit)
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
		// toutes les tables qui disposent d'une clé primaire auront potentiellement des enregistrements à exporter, 
		// et étant donné que ceux-ci seront stockés sous forme de tableau, on crée ce dernier pour chaque table
		foreach(array_keys($this->aaTables) as $sNomTable)
			$this->aaTables[$sNomTable]['AExporter'] = array();			
		
		// connexion à la DB
		$this->connecterDb();
	}
	
	/**
	 * Lit le fichier contenant les infos sur les tables et leurs clés primaires, qui est au format CSV, et les 
	 * enregistre en mémoire
	 */
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
				$this->defClePrimaire($asChamps[TABLE_SRC], $asChamps[CHAMPS_SRC]);
			}
		}
	}
	
	/**
	 * Lit le fichier d'infos sur les relations enfant->parent entre les tables, et les enregistre en mémoire
	 */
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
	
	/**
	 * Lit le fichier d'infos sur les relations parent<-enfant entre les tables, et les enregistre en mémoire
	 */
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
	
	/**
	 * Ecrit le fichier SQL contenant les résultat de l'exportation
	 * 
	 * @param	$v_sNomFichier	le chemin du fichier à écrire
	 */
	function ecrireFichierResultat($v_sNomFichier)
	{
		affln('* Ecriture du fichier... ');
		debutProf(); // on affichera le temps qui a été nécessaire pour écrire le fichier
		
		$hFichier = fopen($v_sNomFichier, 'wb')
		  or die('Impossible de créer le fichier résultat!');
		
		// pour chaque table, si des enregistrements sont à exporter, on ramène chaque enregistrement complet (car 
		// pendant la recherche, seule la clé primaire est stockée), pour ensuite lui faire correspondre un INSERT dans 
		// le fichier résultat écrit
		$sContenuFichier = '';
		foreach (array_keys($this->aaTables) as $sNomTable)
		{
			if ($this->aEnregsAExporter($sNomTable))
			{
				$sCleTable = $this->retClePrimaire($sNomTable);
				$asEnregsAExporter = $this->retEnregsAExporter($sNomTable);
				foreach ($asEnregsAExporter as $sEnregAExporter)
				{
					$hResult = $this->executerRequete("SELECT * FROM `{$sNomTable}` WHERE ".$this->retConditionCle(NULL, $sCleTable, array($sEnregAExporter)));
					$asEnreg = mysql_fetch_assoc($hResult);
					foreach ($asEnreg as $sNomChamp=>$sValeur)
					{
						if (is_null($sValeur))
							$asEnreg[$sNomChamp] = 'NULL';
						else
							$asEnreg[$sNomChamp] = "'".mysql_real_escape_string($sValeur)."'";
					}
					$sContenuFichier .= "INSERT INTO `{$sNomTable}` (`".implode('`, `', array_keys($asEnreg))."`) VALUES (".implode(', ', $asEnreg).");\n";
					$this->libererResult($hResult);
				}
				
//				$sContenuFichier .=
//					"mysqldump -h {$this->sHote} -u {$this->sUser} -p ".
//					" --where=\"".$this->retConditionCle(NULL, $this->retClePrimaire($sNomTable), $asEnregsAExporter)."\"".
//					" {$this->sBase} {$sNomTable}".
//					" > {$sNomTable}.sql".
//					"\n"
					;
			}
		}
		
		fwrite($hFichier, $sContenuFichier);
		fclose($hFichier);
		
		aff('* Ecriture du fichier terminée ');
		affln('('.finProf().' sec)'); // affichage du temps écoulé pendant l'écriture du fichier
	}
	
	/**
	 * Indique si une chaîne de caractères représentant une ligne de fichier, est considérée comme commentée ou pas
	 * 
	 * @param	$v_sLigne	la chaîne de caractères représentant la ligne
	 * 
	 * @return	\c true si la ligne est commentée
	 */
	function estCommentee($v_sLigne)
	{
		foreach ($this->asCommentairesFichiers as $sCommentaire)
			if (substr($v_sLigne, 0, strlen($sCommentaire)) == $sCommentaire)
				return TRUE;
			
		return FALSE;
	}
	
	/**
	 * Réalise la connexion au serveur MySQL. Si celle-ci échoue, le script est stoppé
	 */
	function connecterDb()
	{
		$this->hLien = @mysql_connect($this->sHote, $this->sUser, $this->sMdp)
		  or die('Erreur de connexion au serveur MySQL');
		@mysql_select_db($this->sBase)
		  or die("Erreur de sélection de la DB ($this->sBase)");
	}
	
	/**
	 * Boucle principale de recherche des enregistrements à exporter
	 * 
	 * @param	$v_bRecursif	si \c true, tous les enregistrements des tables enfants/parents de la table traitée sont 
	 * 							cherchés et ramenés avant de passer à la table suivante dans la liste (\c false par 
	 * 							défaut)
	 * 
	 * @return	le nombre de passages successifs effectués dans les tables pour trouver tous les enregistrements 
	 * 			dépendants à exporter
	 */
	function trouverRelations($v_bRecursif = FALSE)
	{
		affln('* Recherche des enregistrements à exporter... ');
		// on va enregistrer le temps nécessaire à la recherche
		debutProf(); 
		
		// tant que de nouveaux enregistrements à exporter ont été trouvés dans au moins une table, on tente à nouveau 
		// de chercher des enregistrements enfants ou parents à exporter dans la liste complète des tables 
		$i = 0;
		while ($this->aEnregsAjoutesToutesTables())
		{
			$i++;
			
			affln('  - Passage n°'.$i);
			
			// remise à faux du drapeau "nouveaux enregistrements trouvés" pour toutes les tables
			$this->reinitEnregsAjoutesToutesTables();
			
			// on recherche, pour les enregistrements exportés de chaque table, les enregistrements d'autres tables 
			// dépendants car référencés par ou qui référencent ceux-ci
			$this->trouverRelationsToutesTables('enfants', $v_bRecursif);
			$this->trouverRelationsToutesTables('parents', $v_bRecursif);
			
			// en mode DEBUG, on affiche, après chaque passage, le nombre et les valeurs des enregistrements à exporter 
			afflnd('');
			if (defined('DEBUG'))
			{
				foreach (array_keys($this->aaTables) as $sNomTable)
					if ($this->aEnregsAExporter($sNomTable)) 
						afflnd($sNomTable.' ('.count($this->retEnregsAExporter($sNomTable)).') : '.implode(', ', $this->retEnregsAExporter($sNomTable)));
			}
		}
		aff('* Recherche terminée ');
		// on affiche le temps écoulé pendant la recherche des enregistrements
		affln('('.finProf().' sec)');
		affln('Nombre de passages : '.$i);
		
		$sSuffixe = $v_bRecursif?'.rec':'';
		$this->ecrireFichierResultat('result'.$sSuffixe.'.txt', $i);
		
		return $i;
	}
	
	/**
	 * Boucle secondaire de recherche des enregistrements à exporter, pour toutes les tables, dans le cadre d'un type de  
	 * relation spécifique avec les autres tables (soit parent, soit enfant)
	 * 
	 * @param	$v_sTypeRel		si \c 'enfants', la recherche porte, pour les enregistrements exportés de chaque table, 
	 * 							sur les enregistrements des autres tables qui les référencent. Si \c 'parents', elle 
	 * 							porte sur les enregistrements des autres tables qui sont référencés par les exportés de 
	 * 							chaque table traitée
	 * @param	$v_bRecursif	si \c true, tous les enregistrements des tables enfants/parents de la table traitée sont 
	 * 							cherchés et ramenés avant de passer à la table suivante dans la liste (\c false par défaut)
	 * 
	 * @return	\c true si de nouveaux enregistrements à exporter ont été trouvés et ajoutés pour une ou plusieurs 
	 * 			tables
	 */
	function trouverRelationsToutesTables($v_sTypeRel, $v_bRecursif = FALSE)
	{
		$r_bEnregsAjoutes = FALSE;
		
		foreach (array_keys($this->aaTables) as $sNomTable)
			if ($this->aEnregsAExporter($sNomTable))
				$r_bEnregsAjoutes = $this->trouverRelationsTable($sNomTable, $v_sTypeRel, $v_bRecursif) || $r_bEnregsAjoutes;
				
		return $r_bEnregsAjoutes;
	}
	
	/**
	 * Effectue la recherche de nouveaux enregistrements à exporter, en se basant sur les relations parents ou enfants 
	 * d'une table par rapport aux autres (les enregistrements d'autres tables, dépendants de ceux déjà marqués pour
	 * l'exportation dans la table traitée, seront eux aussi marqués pour l'exportation)
	 * 
	 * @param	$v_sTableSource	la table dont il faut examiner les enregistrements marqués pour l'exportation, pour 
	 * 							tenter de trouver des enregistrements d'autres tables qui en sont dépendants (et qui  
	 * 							devront donc eux aussi être exportés)
	 * @param	$v_sTypeRel		si \c 'enfants', la recherche porte, pour les enregistrements exportés de chaque table, 
	 * 							sur les enregistrements des autres tables qui les référencent. Si \c 'parents', elle 
	 * 							porte sur les enregistrements des autres tables qui sont référencés par les exportés de 
	 * 							chaque table traitée
	 * @param	$v_bRecursif	si \c true, tous les enregistrements des tables enfants/parents de la table traitée sont 
	 * 							cherchés et ramenés avant de passer à la table suivante dans la liste (\c false par défaut)
	 * 
	 * @return	\c true si de nouveaux enregistrements à exporter ont été trouvés et marqués pour exportation dans une 
	 * 			ou plusieurs tables
	 */
	function trouverRelationsTable($v_sTableSource, $v_sTypeRel, $v_bRecursif = FALSE)
	{
		// au départ, on n'a découvert aucun nouvel enregistrement à exporter
		$r_bEnregsAjoutes = FALSE;
		
		// si la table à examiner n'a aucune relation du type demandé (parent/enfant) avec d'autres tables, on passe
		if ($this->aRelations($v_sTableSource, $v_sTypeRel))
		{
			// on aura besoin de la clé primaire (nom(s) du (des) champ(s) la composant) de la table traitée, et aussi 
			// des valeurs de cette clé pour chaque enregistrement marqué pour l'exportation
			$sCleSource = $this->retClePrimaire($v_sTableSource);
			$asEnregsAExporter = $this->retEnregsAExporter($v_sTableSource);
			
			// on traite à tour de rôle chaque relation du type demandé pour la table spécifiée
			foreach ($this->aaTables[$v_sTableSource][$v_sTypeRel] as $asRel)
			{
				// on a besoin des champs et des tables qui entrent dans la relation, pour construire la requête SQL de
				// recherche
				$sChampSource = $asRel[CHAMPS_SRC];
				$sCleDest = $this->ajouterPrefixeCle('d', $this->retClePrimaire($asRel[TABLE_DEST]));
				$sTableDest = $asRel[TABLE_DEST];
				$sChampDest = $asRel[CHAMPS_DEST];
				
				// si on a défini un nombre maximum d'enregistrements à inspecter en une seule fois, il faut diviser le 
				// tableau les contenant en plusieurs tableaux
				if (defined('NB_INDEX_PAR_REQUETE'))
					$asEnregsAExporterParties = array_chunk($asEnregsAExporter, NB_INDEX_PAR_REQUETE);
				else
					$asEnregsAExporterParties = array(0=>$asEnregsAExporter);
				
				// au départ, on n'a trouvé aucun nouvel enregistrement dépendant en se basant sur les "parties" de 
				// l'ensemble des enregistrements à inspecter pour la table courante
				$bEnregsAjoutesParties = FALSE;
				// on traite les enregistrements par petits groupes (boucle utile seulement si on a défini ci-dessus un 
				// nombre maximum d'enregistrements à inspecter en une fois; sinon on passera de toute façon un seule 
				// fois dans cette boulce)
				foreach ($asEnregsAExporterParties as $asEnregsAExporterPartie)
				{
					// construction de la requête qui vérifie si des enregistrements dépendants de ceux de la table 
					// courante sont trouvés dans l'autre table qui intervient dans la relation
					$sRequeteSql =
						 " SELECT"
						."   DISTINCT $sCleDest"
						." FROM"
						."   $sTableDest AS d"
						."   INNER JOIN $v_sTableSource AS s"
						."     ON (d.$sChampDest = s.$sChampSource)"
						." WHERE"
						.    $this->retConditionCle('s', $sCleSource, $asEnregsAExporterPartie)
						;
					
					$asIds = $this->executerRequeteSurIds($sRequeteSql);
					
					// si des enregistrements dépendants ont été trouvés, on les ajoute à ceux à exporter pour la table
					// concernée, et on marque le fait que de nouveaux enregistrements à exporter ont été trouvés (signe 
					// qu'il faudra effectuer un passage supplémentaire pour trouver d'éventuelles dépendances des 
					// nouveaux enregistrements avec d'autres)
					if (count($asIds))
					{
						$bEnregsAjoutesTmp = $this->ajouterEnregsAExporter($sTableDest, $asIds);
						$r_bEnregsAjoutes =  $bEnregsAjoutesTmp || $r_bEnregsAjoutes;
					}
					else
					{
						$bEnregsAjoutesTmp = FALSE;
					}
					
					$bEnregsAjoutesParties = $bEnregsAjoutesParties || $bEnregsAjoutesTmp;
					
					// messages de déboguage si on a trouvé de nouveaux enregistrements dépendants
					if ($bEnregsAjoutesTmp)
					{
						if ($v_sTypeRel == 'enfants')
						{
							afflnd(
								$v_sTableSource.'<='.$sTableDest.
								' ('.count($asIds).')'.
//								' - '.
//								$sRequeteSql.
								' : '.
								implode(', ', $asIds)
							);
						}
						else
						{
							afflnd(
								DEBUT_EMPHASE.
								$v_sTableSource.'=>'.$sTableDest.
								' ('.count($asIds).')'.
//								' - '.
//								$sRequeteSql.
								' : '.
								implode(', ', $asIds).
								FIN_EMPHASE
								, FALSE
							);
						}
					}
				}
				
				// si on est en mode récursif, et qu'on a trouvé de nouveaux enregistrements dans une table, on traite 
				// cette table avant de terminer
				if ($v_bRecursif && $bEnregsAjoutesParties)
					$r_bEnregsAjoutes = $this->trouverRelationsTable($sTableDest, $v_sTypeRel, $v_bRecursif) || $r_bEnregsAjoutes;
			}
		}
		
		return $r_bEnregsAjoutes;
	}
	
	/**
	 * Retourne les ids (clé primaire) d'enregistrements trouvés par une requête SQL
	 * 
	 * @param	$v_sRequete	la requête SQL à exécuter
	 * 
	 * @return	un tableau contenant les valeurs des clés primaires des enregistrements trouvés, si la clé primaire est 
	 * 			constituée de plusieurs champs, chaque élément du tableau est une chaîne concaténant les valeurs des 
	 * 			champs de la clé primaire, séparées par le caractère '&', par ex. si la clé primaire est IdPers et 
	 * 			IdEquipe, les enregistrements trouvés pourraient être représentés par '1&32' ou '22&91'
	 */
	function executerRequeteSurIds($v_sRequete)
	{
		$asEnreg = NULL;
		$asIds = array();
		
		$hResult = $this->executerRequete($v_sRequete);
		
		// si plusieurs champs sont ramenés dans la réponse, ils seront concaténés en une chaîne, séparés par '&' 
		while ($asEnreg = mysql_fetch_row($hResult))
			$asIds[] = implode('&', $asEnreg);
		
		$this->libererResult($hResult);
		
		return $asIds;
	}
	
	/**
	 * Exécute une requête SQL et renvoie le handle de résultat associé
	 * 
	 * @param	$v_sRequete		la requête SQL à exécuter
	 * @param	$v_bAfficher	si \c true, affiche la requête avant de l'excécuter (pour le déboguage) (\c false par défaut)
	 * 
	 * @return	le handle de résultat pour cette requête
	 */
	function executerRequete($v_sRequete, $v_bAfficher = FALSE)
	{
		if ($v_bAfficher)
			affln($v_sRequete);
		
		$r_hResult = mysql_query($v_sRequete)
		  or die(mysql_error());
		
		return $r_hResult;
	}
	
	/**
	 * Libère les ressources associées à un résultat de requête MySQL
	 * 
	 * @param	$v_hResult	le handle de résultat dont il faut libérer les ressources
	 */
	function libererResult($v_hResult)
	{
		mysql_free_result($v_hResult);
	}
	
	/**
	 * Enregistre en mémoire le(s) nom(s) du (des) champ(s) composant la clé primaire d'une table
	 * 
	 * @param	$v_sTable	le nom de la table concernée
	 * @param	$v_sCle		le nom du (des) champ(s) constituant la clé de cette table. Si plusieurs champs, les noms 
	 * 						doivent être séparés par un caractère '&' et concaténés en une seule chaîne
	 */
	function defClePrimaire($v_sTable, $v_sCle)
	{
		$this->aaTables[$v_sTable]['Cle'] = $v_sCle;
	}
	
	/**
	 * Retourne le(s) nom(s) du (des) champ(s) constituant la clé d'une table
	 * 
	 * @param	$v_sTable	le nom de la table de laquelle on veut connaître la clé primaire
	 * 
	 * @return	le(s) nom(s) du (des) champ(s) constituant la clé primaire pour la table spécifiée. Si plusieurs champs, 
	 * 			leurs noms sont concaténés et séparés par le caractère '&' dans la chaîne retournée
	 */
	function retClePrimaire($v_sTable)
	{
		return $this->aaTables[$v_sTable]['Cle'];
	}
	
	/**
	 * Détermine si une table possède des relations d'un certain type (parent/enfant) avec au moins une autre table
	 * 
	 * @param	$v_sTable	le nom de la table pour laquelle on veut connaître l'existence de relations
	 * @param	$v_sTypeRel	le type de relation dont on veut connaître l'existence pour la table. Peut valoir 
	 * 						\c 'parents' ou \c 'enfants'
	 * 
	 * @return	\c true si la table spécifiée a des relations du type demandé avec d'autres tables
	 */
	function aRelations($v_sTable, $v_sTypeRel)
	{
		return (boolean)count($this->aaTables[$v_sTable][$v_sTypeRel]);
	}
	
	/**
	 * Réinitialise les drapeaux "nouveeaux enregistrements trouvés" sur toutes les tables
	 */
	function reinitEnregsAjoutesToutesTables()
	{
		foreach (array_keys($this->aaTables) as $sNomTable)
			$this->reinitEnregsAjoutes($sNomTable);
	}
	
	/**
	 * Réinitialise les drapeaux "nouveaux enregistrements trouvés" sur une table
	 * 
	 * @param	$v_sTable	le nom de la table pour laquelle il faut réinitialiser le drapeau
	 */
	function reinitEnregsAjoutes($v_sTable)
	{
		$this->aaTables[$v_sTable]['EnregsAjoutes'] = FALSE;
	}
	
	/**
	 * Active le drapeau "nouveaux enregistrements trouvés" dans toutes les tables
	 */
	function defEnregsAjoutesToutesTables()
	{
		foreach (array_keys($this->aaTables) as $sNomTable)
			$this->defEnregsAjoutes($sNomTable);
	}
	
	/**
	 * Active le drapeau "nouveaux enregistrements trouvés" sur une table
	 * 
	 * @param	$v_sTable	le nom de la table pour laquelle il faut activer le drapeau
	 */
	function defEnregsAjoutes($v_sTable)
	{
		$this->aaTables[$v_sTable]['EnregsAjoutes'] = TRUE;
	}
	
	/**
	 * Détermine si au moins une table a son drapeau "nouveaux enregistrements trouvés" activé
	 * 
	 * @return	\c true si au moins une table a son drapeau "nouveaux enregistrements trouvés" activé
	 */
	function aEnregsAjoutesToutesTables()
	{
		foreach (array_keys($this->aaTables) as $sNomTable)
			if ($this->aEnregsAjoutes($sNomTable))
				return TRUE;
		
		return FALSE;
	}
	
	/**
	 * Détermine si la table spécifiée a son drapeau "nouveaux enregistrements trouvés" activé
	 * 
	 * @return	\c true si au la table a son drapeau "nouveaux enregistrements trouvés" activé
	 */
	function aEnregsAjoutes($v_sTable)
	{
		return $this->aaTables[$v_sTable]['EnregsAjoutes'];
	}
	
	/**
	 * Ajoute, pour une table, les valeurs de clé primaire de plusieurs enregistrements à exporter, si ceux-ci ne sont 
	 * pas déjà marqués pour exportation
	 * 
	 * @param	$v_sTable		le nom de la table pour laquelle il faut ajouter/marquer des enregistrements à exporter
	 * @param	$v_asValeurs	le tableau des valeurs de clé primaire pour les différents enregistrements à ajouter à 
	 * 							l'exportation. S'il s'agit d'une table pour laquelle la clé primaire est constituée de 
	 * 							plusieurs champs, les valeurs doivent être concaténées dans une chaîne, et séparées par 
	 * 							le caractère '&'
	 * 
	 * @return	\c true si au moins un des enregistrements fournis a effectivement été ajouté pour exportation (pour  
	 * 			qu'un enregistrement soit ajouté, il faut qu'il ne s'y trouve pas déjà bien entendu)
	 */
	function ajouterEnregsAExporter($v_sTable, $v_asValeurs)
	{
		$r_bEnregAjoute = FALSE;
		
		foreach ($v_asValeurs as $sValeur)
			$r_bEnregAjoute = $this->ajouterEnregAExporter($v_sTable, $sValeur) || $r_bEnregAjoute;
		
		return $r_bEnregAjoute;
	}
	
	/**
	 * Ajoute, pour une table, la valeur de clé primaire d'un enregistrement à exporter, si celui-ci n'est pas déjà 
	 * marqué pour exportation
	 * 
	 * @param	$v_sTable		le nom de la table pour laquelle il faut ajouter un enregistrement à exporter
	 * @param	$v_sValeur		la valeur de clé primaire pour l'enregistrements à ajouter à l'exportation. S'il s'agit 
	 * 							d'une table pour laquelle la clé primaire est constituée de plusieurs champs, les 
	 * 							valeurs doivent être concaténées dans une chaîne, séparées par le caractère '&'
	 * 
	 * @return	\c true si au moins l'enregistrement a effectivement été ajouté pour exportation (pour cela il faut 
	 * 			qu'il ne s'y trouve pas déjà bien entendu)
	 */
	function ajouterEnregAExporter($v_sTable, $v_sValeur)
	{
		$r_bEnregAjoute = FALSE;
		
		if (!array_key_exists($v_sValeur, $this->aaTables[$v_sTable]['AExporter']))
		{
			$r_bEnregAjoute = TRUE;
			$this->defEnregsAjoutes($v_sTable);
			$this->aaTables[$v_sTable]['AExporter'][$v_sValeur] = TRUE;
		}
		
		return $r_bEnregAjoute;
	}
	
	/**
	 * Détermine si une table a au moins un enregistrement marqué pour exportation
	 * 
	 * @param	$v_sTable	le nom de la table sur laquelle on veut effectuer la vérification
	 * 
	 * @return	\c true si la table spécifiée a des enregistrements à exporter
	 */
	function aEnregsAExporter($v_sTable)
	{
		return (boolean)(count($this->aaTables[$v_sTable]['AExporter']));
	}
	
	/**
	 * Retourne les les enregistrements actuellement marqués pour l'exportation dans une table
	 * 
	 * @param	$v_sTable	le nom de la table dont on veut récupérer les enregistrements à exporter
	 * 
	 * @return	le tableau des valeurs de clé primaire des enregistrements marqués pour l'exportation dans la table 
	 * 			spécifiée. Si la clé primaire de cette table est constituée de plusieurs champs, les éléments du tableau 
	 * 			sont représentées par une chaîne de caractères reprenant les valeurs de chaque champ, concaténées et 
	 * 			séparées par le caractère '&'
	 */
	function retEnregsAExporter($v_sTable)
	{
		return array_keys($this->aaTables[$v_sTable]['AExporter']);
	}
	
	/**
	 * Ajoute un préfixe et un point devant le nom d'un ou plusieurs champs (dans ce dernier cas, séparés par des 
	 * virgules), et retourne la chaîne résultante
	 * 
	 * @param	$v_sPrefixe	le préfixe à placer devant le ou les champs spécifié(s)
	 * @param	$sv_sCle	le ou les champ(s) devant le(s)quel(s) ajouter le préfixe spécifié. Si plusieurs champs, 
	 * 						ceux-ci seront séparés par des virgules, sans espaces
	 * 
	 * @return	la chaîne de caractères représentant le ou les champs donné(s) en entrée, avec le préfixe ajouté
	 */
	function ajouterPrefixeCle($v_sPrefixe, $v_sCle)
	{
		$asCles = explode(',', $v_sCle);
		for ($i = 0; $i < count($asCles); $i++)
			$asCles[$i] = $v_sPrefixe.'.'.$asCles[$i];
		return implode(', ', $asCles);
	}
	
	/**
	 * Construit une condition SQL (qui pourra être ajoutée à la clause WHERE d'un SELECT) qui effectue une recherche 
	 * des valeurs passées en paramètre, dans le ou les champs passé(s) également en paramètre
	 * 
	 * @param	$v_sPrefixe				le préfixe à placer devant le nom du ou des champ(s). Ce préfixe peut être vide
	 * @param	$v_sCle					le ou les champ(s), sous forme de chaîne de caractères, dans le(s)quel(s) il 
	 * 									faudra effectuer la recherche. Si plusieurs, ils doivent être séparés par des 
	 * 									virgules, sans espaces (et toujours sous forme de chaîne)
	 * @param	$v_asEnregsAExporter	les valeurs à rechercher dans le(s) champ(s) spécifié(s), sous forme de tableau. 
	 * 									Si plusieurs champs sont spécifiés dans \p v_sCle, chaque élément du tableau 
	 * 									doit alors contenir des valeurs pour chaque champ, ces valeurs étant séparées  
	 * 									par une virgule, sans espaces, le tout concaténé en chaîne de caractères
	 * 
	 * @param	$v_bInverser			si \c true, la condition est inversée (NOT) (\c false par défaut)
	 * 
	 * @return	la chaîne de caractère à ajouter au WHERE et représentant la condition demandée
	 */
	function retConditionCle($v_sPrefixe, $v_sCle, $v_asEnregsAExporter, $v_bInverser = FALSE)
	{
		$v_sPrefixe = !empty($v_sPrefixe)?$v_sPrefixe.'.':'';
		$sCondition = ' ';
		$asCles = explode(',', $v_sCle);
		
		if (empty($v_asEnregsAExporter))
		{
			$sCondition = '0';
		}
		else if (count($asCles) == 1)
		{
			$sCondition .= $v_sPrefixe.$asCles[0].' IN ('.implode(', ', $v_asEnregsAExporter).')';
		}
		else
		{
			foreach ($v_asEnregsAExporter as $sEnregAExporter)
			{
				$asValeursParCle = explode('&', $sEnregAExporter);
				$asPartiesCondition = array();
				for ($i = 0; $i < count($asCles); $i++)
					$asPartiesCondition[] = $v_sPrefixe.$asCles[$i].'='.$asValeursParCle[$i];
				$asConditions[] = '('.implode(' AND ', $asPartiesCondition).')';
			}
			
			$sCondition .= implode(' OR ', $asConditions);
		}
		
		if ($v_bInverser)
			$sCondition = "NOT ($sCondition)";
		
		return $sCondition;
	}
}

// script principal de l'exportation
aff(EN_TETE, FALSE);

$oExport = new CExportDb();
$oExport->ajouterEnregsAExporter('Formation', $aiFormationsAExporter);
$oExport->trouverRelations(RECHERCHE_RECURSIVE);

aff(PIED_DE_PAGE, FALSE);

?>