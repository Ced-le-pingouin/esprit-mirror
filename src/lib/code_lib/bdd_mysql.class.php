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
**					  donn�es MySQL
** Cr�ation			: 04-09-2001 (C�dric FLOQUET, cedric.floquet@advalvas.be)
** Derni�re modif	: 02-04-2003
**
** (c) 2001 Unit� de Technologie de l'Education. Tous droits r�serv�s.
*/


/*
** Classe			: CBddMySql
** Description		: interface de base pour l'utilisation d'une base de 
**					  donn�es MySQL
*/
class CBddMySql
{
	var $sHote; var $sLogin; var $sMdp;		// infos pour la connexion � la base
	var $sNom;								// nom de la base
	var $hLien = 0;							// handle vers la base
	var $sRequete = "";						// derni�re requ�te 'tent�e' (pas forc�ment r�ussie)
	var $ahResult;							// handles de r�sultats de requ�tes


	/*
	** Fonction 		: CBddMySql (constructeur)
	** Description		: effectue la connexion au serveur de BDD, puis la connexion � une
	**					  base en particulier
	** Entr�e			:
	**					v_sHote			: nom du serveur de BDD ("localhost" par d�faut)
	**					v_sLogin		: nom d'utilisateur pour la connexion
	**					v_sMdp			: mot de passe pour la connexion
	**					v_sNom			: nom de la base � utiliser
	** Sortie			: aucune
	*/
	function CBddMySql($v_sHote = "localhost", $v_sLogin, $v_sMdp, $v_sNom)
	{
		// les infos de connexion sont copi�es dans les propri�t�s de la classe
		$this->sHote = $v_sHote;
		$this->sLogin = $v_sLogin;
		$this->sMdp = $v_sMdp;

		// tente la connexion, si echec, on arr�te directement
		$this->hLien = mysql_connect($this->sHote, $this->sLogin, $this->sMdp) or die;

		// connexion � la base voulue
		mysql_select_db($v_sNom, $this->hLien) or $this->traiterErreur();

		// connexion � la base r�ussie -> son nom est copi� dans la propri�t� ad hoc
		$this->sNom = $v_sNom;
	}


	/*
	** Fonction 		: terminer (pseudo-destructeur)
	** Description		: ferme la connexion au serveur de BDD
	** Entr�e			: aucune
	** Sortie			: aucune
	*/
	function terminer()
	{
		mysql_close($this->hLien) or $this->traiterErreur();
	}

	
	/*
	** Fonction 		: executerRequete
	** Description		: ex�cute une requ�te SQL
	** Entr�e			:
	**					v_sRequete		: texte de la requ�te SQL (sans point-virgule
	**									  � la fin)
	** Sortie			:
	**					si la requ�te r�ussit, le num�ro du r�sultat correspondant
	**					est retourn�. Sinon, FALSE est retourn�
	*/
	function executerRequete($v_sRequete, $v_bAfficher = FALSE)
	{
		// si la requ�te n'est pas vide, on la copie dans la propri�t� ad hoc
		if ($v_sRequete != "")
			$this->sRequete = $v_sRequete;

		// affiche le texte de la requ�te avant l'ex�cution
		if ($v_bAfficher)
			print $this->sRequete . "<br>";

		// si la requ�te est valide...
		if ($hResult = mysql_query($this->sRequete, $this->hLien))
		{
			// ...on enregistre le handle du r�sultat � la fin de notre tableau...
			$this->ahResult[] = $hResult;
			// ...on place le pointeur du tableau � la fin...
			end($this->ahResult);
			// ...et on renvoie l'indice (donc le num�ro) de ce r�sultat
			return key($this->ahResult);
		}
		// requ�te invalide -> erreur, retourne FALSE
		else
			$this->traiterErreur();
			
		return FALSE;
	}


	/*
	** Fonction 		: retDernierId
	** Description		: renvoie le dernier num�ro ins�r� dans un champ AUTO_INCREMENT
	** Entr�e			: aucune
	** Sortie			:
	**					num�ro cr�� lors de l'insertion du champ
	*/
	function retDernierId() { return mysql_insert_id($this->hLien); }


	/*
	** Fonction 		: retNbResults
	** Description		: fonction d'acc�s au nombre actuel de r�sultats de requ�tes
	** Entr�e			: aucune
	** Sortie			:
	**					nombre actuel de r�sultats de requ�tes 'exploitables'
	*/
	function retNbResults() { return count($this->ahResult); }


	/*
	** Fonction 		: retNbEnregsDsResult
	** Description		: renvoie le nombre total d'enregistrements contenus dans
	**					  un r�sultat de requ�te donn�
	** Entr�e			:
	**					v_iNumResult	: num�ro du r�sultat � traiter
	** Sortie			:
	**					nombre total d'enregistrements pour ce r�sultat
	*/
	function retNbEnregsDsResult($v_iNumResult = NULL)
	{
		// si le num�ro de r�sultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}
	
		return mysql_num_rows($this->ahResult[$v_iNumResult]);
	}

	
	/*
	** Fonction 		: retEnregSuivant
	** Description		: renvoie l'enregistrement suivant d'un r�sultat
	** Entr�e			:
	**					v_iNumResult	: num�ro du r�sultat � traiter
	** Sortie			:
	**					l'enregistrement est retourn� sous forme d'objet, dont
	**					les propri�t�s sont les diff�rents champs. S'il n'y a 
	**					plus d'enregistrement, NULL est retourn�
	*/
	function retEnregSuiv($v_iNumResult = NULL)
	{
		// si le num�ro de r�sultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}
	
		return mysql_fetch_object($this->ahResult[$v_iNumResult]);
	}
	

	/*
	** Fonction 		: retEnregPrecis
	** Description		: renvoie un enregistrement pr�cis dans un r�sultat
	** Entr�e			:
	**					v_iNumResult	: num�ro du r�sultat � traiter
	**					v_iNumEnreg		: num�ro de l'enregistrement
	** Sortie			:
	**					l'enregistrement est retourn� sous forme de
	**					tableau de champs (indic�s � partir de 0)
	*/
	function retEnregPrecis($v_iNumResult = NULL, $v_iNumEnreg = 0)
	{
		// si le num�ro de r�sultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}
	
		return mysql_result($this->ahResult[$v_iNumResult], $v_iNumEnreg);
	}


	/*
	** Fonction 		: libererResult
	** Description		: lib�re les ressources associ�es � un r�sultat
	** Entr�e			:
	**					v_iNumResult	: num�ro du r�sultat � traiter
	** Sortie			: aucune
	*/
	function libererResult($v_iNumResult = NULL)
	{
		// si le num�ro de r�sultat n'est pas fourni, on prend le dernier
		if (empty($v_iNumResult))
		{
			end($this->ahResult);
			$v_iNumResult = key($this->ahResult);
		}

		// on lib�re les ressources
		mysql_free_result($this->ahResult[$v_iNumResult]);
		// puis on supprime l'entr�e correspondant au r�sultat dans notre tableau
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
	** Description		: affiche le message correspondant � la derni�re erreur
	**					  survenue pour la base de donn�es
	** Entr�e			:
	**					v_bEstFatale	: si TRUE (par d�faut), l'erreur entraine
	**									  l'arr�t imm�diat du script PHP
	** Sortie			: aucune
	*/
	function traiterErreur($v_bEstFatale = TRUE)
	{
		// si on est connect�, affiche le dernier message d'erreur
		if ($this->hLien)
			print mysql_error($this->hLien);

		// arr�t du script PHP si n�cessaire
		if ($v_bEstFatale)
			exit();
	}
}

?>
