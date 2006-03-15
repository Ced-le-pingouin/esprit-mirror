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
 * @file	bdd_mysql.class.php
 * 
 * Contient la classe de gestion de DB, ici MySQL
 * 
 * @date	2001/09/04
 * 
 * @author	Cédric FLOQUET
 */

/**
 * Interface avec une DB MySQL
 */
class CBddMySql
{
	var $sHote;			///< Paramètre "hôte" pour la connexion à la DB
	var $sLogin;		///< Paramètre "username" pour la connexion à la DB
	var $sMdp;			///< Paramètre "mot de passe" pour la connexion à la DB
	var $sNom;			///< Nom de la DB de départ
	var $hLien = 0;		///< Handle de la connexion établie à la DB
	var $sRequete = "";	///< Dernière requête 'tentée' (pas forcément réussie)
	var $ahResult;		///< Tableau des handles de résultats de requêtes
	
	/**
	 * Constructeur. Effectue la connexion au serveur DB, et sélectionne automatiquement la DB spécifiée
	 * 
	 * @param	v_sHote		l'adresse du serveur DB
	 * @param	v_sLogin	le username à utiliser pour la connexion
	 * @param	v_sMdp		le mot de passe à utiliser pour la connexion
	 * @param	v_sNom		le nom de la DB à sélectionner automatiquement
	 */
	function CBddMySql($v_sHote = "localhost", $v_sLogin, $v_sMdp, $v_sNom)
	{
		// les infos de connexion sont copiées dans les propriétés de la classe
		$this->sHote = $v_sHote;
		$this->sLogin = $v_sLogin;
		$this->sMdp = $v_sMdp;

		// tente la connexion, si echec, on arrête directement
		$this->hLien = mysql_connect($this->sHote, $this->sLogin, $this->sMdp) or die;

		// connexion à la base voulue
		mysql_select_db($v_sNom, $this->hLien) or $this->traiterErreur();

		// connexion à la base réussie -> son nom est copié dans la propriété ad hoc
		$this->sNom = $v_sNom;
	}
	
	/**
	 * Pseudo-destructeur (doit être appelé explicitement). Ferme la connexion au serveur DB
	 */
	function terminer()
	{
		mysql_close($this->hLien) or $this->traiterErreur();
	}

	
	/**
	 * Exécute une requête SQL, et en cas de réussite enregistre le handle du résultat dans le tableau \c ahResult
	 * 
	 * @param	v_sRequete	le texte de la requête, sans point-virgule final. Une seule requête à la fois donc
	 * @param	v_bAfficher	si \c true, le texte de la requête est affiché dans la page (debug)
	 * 
	 * @return	l'indice du handle de résultat dans le tableau \c ahResult en cas de réussite de la requête, \c false 
	 * 			en cas d'échec (erreur de syntaxe ou autre)
	 */
	function executerRequete($v_sRequete, $v_bAfficher = FALSE)
	{
		// si la requête n'est pas vide, on la copie dans la propriété ad hoc
		if ($v_sRequete != "")
			$this->sRequete = $v_sRequete;

		// affiche le texte de la requête avant l'exécution
		if ($v_bAfficher)
			print $this->sRequete . "<br>";

		// si la requête est valide...
		if ($hResult = mysql_query($this->sRequete, $this->hLien))
		{
			// ...on enregistre le handle du résultat à la fin de notre tableau...
			$this->ahResult[] = $hResult;
			// ...on place le pointeur du tableau à la fin...
			end($this->ahResult);
			// ...et on renvoie l'indice (donc le numéro) de ce résultat
			return key($this->ahResult);
		}
		// requête invalide -> erreur, retourne FALSE
		else
			$this->traiterErreur();
			
		return FALSE;
	}
	
	
	/**
	 * Retourne l'id généré pour le dernier enregistrement inséré avec champ AUTO_INCREMENT
	 * 
	 * @return	l'id du dernier enregistrement inséré avec colonne AUTO_INCREMENT
	 */
	function retDernierId() { return mysql_insert_id($this->hLien); }
	
	/**
	 * Retourne le nombre de résultats de requêtes (tableau \c ahResult)
	 * 
	 * @return	le nombre de résultats de requêtes exploitables
	 */
	function retNbResults() { return count($this->ahResult); }
	
	/**
	 * Retourne le nombre d'enregistrements récupérés par une requête
	 * 
	 * @param	v_iNumResult	l'indice du handle de résultats à utiliser (plusieurs résultats peuvent être gardés, 
	 * 							dans \c ahResult). Par défaut \c null, ce qui revient à traiter le résultat de la 
	 * 							dernière requête réussie
	 * 
	 * @return	le nombre d'enregistrements récupérés dans le résultat de requête spécifié
	 */
	function retNbEnregsDsResult($v_iNumResult = NULL)
	{
		// si le numéro de résultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}
	
		return mysql_num_rows($this->ahResult[$v_iNumResult]);
	}
	
	/**
	 * Retourne sous forme d'objet PHP l'enregistrement suivant dans un résultat de requête
	 * 
	 * @param	v_iNumResult	l'indice du résultat de requête à utiliser (voir #retNbEnregsDsResult())
	 * 
	 * @return	l'objet PHP contenant les champs de l'enregistrement suivant du résultat de la requête
	 */
	function retEnregSuiv($v_iNumResult = NULL)
	{
		// si le numéro de résultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}
	
		return mysql_fetch_object($this->ahResult[$v_iNumResult]);
	}
	
	/**
	 * Retourne le premier champ d'un enregistrement donné dans un résultat de requête
	 * 
	 * @param	v_iNumResult	l'indice du résultat de requête à utiliser (voir #retNbEnregsDsResult())
	 * @param	v_iNumEnreg		l'indice de l'enregistrement dans le résultat (le premier = 0)
	 * 
	 * @return	le premier champ de l'enregistrement spécifié, dans le résultat de requête voulu
	 * 
	 * @note	Cette fonction est limitée au premier champ, elle est surtout utilisée pour les requêtes SELECT dont 
	 * 			le résultat n'a qu'un enregistrement et un seul champ non nommé, comme <code>SELECT MD5("blah")</code> 
	 * 			ou <code>SELECT COUNT(*) FROM Table</code>
	 */
	function retEnregPrecis($v_iNumResult = NULL, $v_iNumEnreg = 0)
	{
		// si le numéro de résultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}
	
		return mysql_result($this->ahResult[$v_iNumResult], $v_iNumEnreg);
	}
	
	/**
	 * Libère les ressources associées à un résultat de requête. Le résultat est également enlevé du tableau \c ahResult
	 * 
	 * @param	v_iNumResult	l'indice du résultat à libérer (voir #retNbEnregsDsResult())
	 */
	function libererResult($v_iNumResult = NULL)
	{
		// si le numéro de résultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}

		// on libère les ressources
		mysql_free_result($this->ahResult[$v_iNumResult]);
		// puis on supprime l'entrée correspondant au résultat dans notre tableau
		unset($this->ahResult[$v_iNumResult]);
	}
	
	/**
	 * Fonction utilitaire pour "nettoyer" une donnée texte. Pour le moment, il s'agit d'un trim suivi de l'enlèvement 
	 * des backslashes
	 * 
	 * @param	v_sDonnee	le texte à nettoyer
	 * 
	 * @return	le texte nettoyé
	 */
	function validerDonnee($v_sDonnee)
	{
		return stripslashes(trim($v_sDonnee));
	}
	
	/**
	 * Exécute un "UNLOCK TABLES"
	 */
	function deverrouillerTables()
	{
		$this->executerRequete("UNLOCK TABLES");
	}
	
	/**
	 * Affiche la dernière erreur SQL survenue et arrête éventuellement le script PHP courant
	 * 
	 * @param	v_bEstFatale	si \c true (défaut), le script PHP est stoppé
	 */
	function traiterErreur($v_bEstFatale = TRUE)
	{
		// si on est connecté, affiche le dernier message d'erreur
		if ($this->hLien)
			print mysql_error($this->hLien);

		// arrêt du script PHP si nécessaire
		if ($v_bEstFatale)
			exit();
	}
}

?>
