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

/*
** Fichier			: bdd_mysql.class.php
** Description		: classe formant une interface avec des base de
**					  données MySQL
** Création			: 04-09-2001 (Cédric FLOQUET, cedric.floquet@advalvas.be)
** Dernière modif	: 02-04-2003
**
** (c) 2001 Unité de Technologie de l'Education. Tous droits réservés.
*/


/*
** Classe			: CBddMySql
** Description		: interface de base pour l'utilisation d'une base de 
**					  données MySQL
*/
class CBddMySql
{
	var $sHote; var $sLogin; var $sMdp;		// infos pour la connexion à la base
	var $sNom;								// nom de la base
	var $hLien = 0;							// handle vers la base
	var $sRequete = "";						// dernière requête 'tentée' (pas forcément réussie)
	var $ahResult;							// handles de résultats de requêtes


	/*
	** Fonction 		: CBddMySql (constructeur)
	** Description		: effectue la connexion au serveur de BDD, puis la connexion à une
	**					  base en particulier
	** Entrée			:
	**					v_sHote			: nom du serveur de BDD ("localhost" par défaut)
	**					v_sLogin		: nom d'utilisateur pour la connexion
	**					v_sMdp			: mot de passe pour la connexion
	**					v_sNom			: nom de la base à utiliser
	** Sortie			: aucune
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


	/*
	** Fonction 		: terminer (pseudo-destructeur)
	** Description		: ferme la connexion au serveur de BDD
	** Entrée			: aucune
	** Sortie			: aucune
	*/
	function terminer()
	{
		mysql_close($this->hLien) or $this->traiterErreur();
	}

	
	/*
	** Fonction 		: executerRequete
	** Description		: exécute une requête SQL
	** Entrée			:
	**					v_sRequete		: texte de la requête SQL (sans point-virgule
	**									  à la fin)
	** Sortie			:
	**					si la requête réussit, le numéro du résultat correspondant
	**					est retourné. Sinon, FALSE est retourné
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


	/*
	** Fonction 		: retDernierId
	** Description		: renvoie le dernier numéro inséré dans un champ AUTO_INCREMENT
	** Entrée			: aucune
	** Sortie			:
	**					numéro créé lors de l'insertion du champ
	*/
	function retDernierId() { return mysql_insert_id($this->hLien); }


	/*
	** Fonction 		: retNbResults
	** Description		: fonction d'accès au nombre actuel de résultats de requêtes
	** Entrée			: aucune
	** Sortie			:
	**					nombre actuel de résultats de requêtes 'exploitables'
	*/
	function retNbResults() { return count($this->ahResult); }


	/*
	** Fonction 		: retNbEnregsDsResult
	** Description		: renvoie le nombre total d'enregistrements contenus dans
	**					  un résultat de requête donné
	** Entrée			:
	**					v_iNumResult	: numéro du résultat à traiter
	** Sortie			:
	**					nombre total d'enregistrements pour ce résultat
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

	
	/*
	** Fonction 		: retEnregSuivant
	** Description		: renvoie l'enregistrement suivant d'un résultat
	** Entrée			:
	**					v_iNumResult	: numéro du résultat à traiter
	** Sortie			:
	**					l'enregistrement est retourné sous forme d'objet, dont
	**					les propriétés sont les différents champs. S'il n'y a 
	**					plus d'enregistrement, NULL est retourné
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
	

	/*
	** Fonction 		: retEnregPrecis
	** Description		: renvoie un enregistrement précis dans un résultat
	** Entrée			:
	**					v_iNumResult	: numéro du résultat à traiter
	**					v_iNumEnreg		: numéro de l'enregistrement
	** Sortie			:
	**					l'enregistrement est retourné sous forme de
	**					tableau de champs (indicés à partir de 0)
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


	/*
	** Fonction 		: libererResult
	** Description		: libère les ressources associées à un résultat
	** Entrée			:
	**					v_iNumResult	: numéro du résultat à traiter
	** Sortie			: aucune
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

	function validerDonnee ($v_sDonnee)
	{
		return stripslashes(trim($v_sDonnee));
	}
	
	function deverrouillerTables ()
	{
		$this->executerRequete("UNLOCK TABLES");
	}
	
	/*
	** Fonction 		: traiterErreur
	** Description		: affiche le message correspondant à la dernière erreur
	**					  survenue pour la base de données
	** Entrée			:
	**					v_bEstFatale	: si TRUE (par défaut), l'erreur entraine
	**									  l'arrêt immédiat du script PHP
	** Sortie			: aucune
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
