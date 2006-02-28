<?php
/**
 * @file	template.inc.php
 * 
 * D�claration des classes pour les templates
 * 
 * @date	2002/03/21
 * 
 * @author	J�r�me TOUZE
 * @author	C�dric FLOQUET
 * @author	Filippo PORCO
 */

/**
 * Gestion de templates, avec possibilit� de remplacements de variables, de r�p�tition et masquage de blocs
 */
class Template
{
	var $data = NULL;	///< Contenu textuel du template, avec les remplacements �ventuels
	
	/**
	 * Constructeur. Initialise le le template avec le contenu d'un fichier, th�oriquement au format HTML
	 * 
	 * @param	v_sTplFichier	le nom du fichier template � charger
	 */
	function Template($v_sTplFichier)
	{
		if ($v_sTplFichier && is_file($v_sTplFichier))
		{
			if ($fp = @fopen($v_sTplFichier, "r"))
			{
				while ($sLigne = fgets($fp, 4096))
					$this->data .= $sLigne;
				fclose($fp);
			}
		}
		else
			$this->data = "";
	}
	
	/**
	 * Ajoute le contenu d'un fichier template � la fin du contenu actuel
	 * 
	 * @param	v_sTplFichier	le nom du fichier template � ajouter
	 */
	function ajouterTemplate($v_sTplFichier)
	{
		$this->Template($v_sTplFichier);
	}
	
	/**
	 * Retourne une "variable de template", c�d un bloc entre balises [bloc+][bloc-]. Par exemple si le template 
	 * contient un segment "[bloc+]contenu[bloc-]", un appel <code>defVariable("bloc")</code> renvoie le texte \c "contenu"
	 * 
	 * @param	looptag		le nom du bloc dont les balises entourent la variable de template
	 * 
	 * @return	la variable trouv�e. S'il n'existe pas de variable d�finie par des blocs [looptag+][lopptag-], renvoie 
	 * 			\c null
	 */
	function defVariable($looptag)
	{
		$sVariable = NULL;
		
		if(strpos($this->data,"[$looptag-]"))
		{
			$debut = strpos($this->data, "[$looptag+]");
			$fin = strpos($this->data, "[$looptag-]", $debut) + strlen("[$looptag-]"); // + taille de la balise finale
			$sVariable = substr($this->data, $debut, ($fin-$debut));
			//effacement du bloc
			$this->data = str_replace($sVariable, "", $this->data);
			// enlevement des balises + et - ds le tableau
			$sVariable = str_replace("[$looptag+]", "", $sVariable);
			$sVariable = str_replace("[$looptag-]", "", $sVariable);
			$sVariable = trim($sVariable);
		}
		
		return $sVariable;
	}
	
	/**
	 * Remplace "simplement" des morceaux de template par d'autres. Utilis� pour inclure des variables dans le template 
	 * qui seront remplac�es en PHP, par exemple un appel <code>remplacer("{utilisateur}", $oProjet->oUtilisateur->retPseudo())</code>
	 * remplace toutes les cha�nes \c "{utilisateur}" trouv�es dans le template par le pseudo de l'utilisateur connect�
	 * 
	 * @param	in	la cha�ne de caract�re � trouver dans le template
	 * @param	out	la cha�ne par laquelle on veut remplacer toutes les occurences de \p in
	 */
	function remplacer($in, $out)
	{
		$this->data = str_replace($in, $out, $this->data);
	}
	
	/**
	 * Affiche les donn�es contenues actuellement dans le template. G�n�ralement appel� � la fin du script PHP qui 
	 * utilise le template. Un remplacement automatique se fait avant l'affichage, sur les faux protocoles <code>
	 * "racine://", "admin://", "commun://", "theme://", "javascript://", et "lib://", qui sont remplac�s par, 
	 * respectivement, les resultats des appels aux fonctions globales dir_root_plateform(), dir_admin(), 
	 * dir_theme_commun(), dir_theme(), dir_javascript(), et dir_lib()
	 * 
	 * @see	#retDonnees()
	 */
	function afficher()
	{
		// {{{ Ajouter par Fil
		$asRechercher = array("racine://", "admin://", "commun://", "theme://", "javascript://", "lib://");
		$asRemplacer = array(dir_root_plateform(NULL, FALSE),dir_admin(), dir_theme_commun(), dir_theme(), dir_javascript(), dir_lib());
		$this->data = str_replace($asRechercher, $asRemplacer, $this->data);
		// }}}
		echo $this->retDonnees();
	}
	
	// {{{ Ajout� par Fil
	/**
	 * Retourne le contenu actuel du template, avec les remplacements et autres traitements de blocs �ventuels
	 * 
	 * @return	le contenu du template apr�s traitements
	 */
	function retDonnees()
	{
		return trim($this->data);
	}
	
	/**
	 * Retourne le nombre de caract�res contenu dans le template, tel qu'il se trouve apr�s les �ventuels traitements 
	 * de blocs, remplacements, etc
	 * 
	 * @return	le nombre de caract�res contenus dans le template apr�s traitements
	 */
	function caracteres()
	{
		return strlen($this->data);
	}
	// }}}
}

/**
 * Gestion des blocs de templates, c�d des parties masquables ou r�p�tables d'un template
 * 
 * @todo	Etudier la possibilit� de faire h�riter cette classe de Template, car certaines fonctions sont identiques
 */
class TPL_Block
{
	var $looptag;			///< Balise qui entourait le bloc dans le template parent
	var $data;				///< Contenu textuel courant du bloc, donc avec les remplacements et r�p�titions �ventuels
	var $template_parent;	///< Objet Template parent, auquel appartient le bloc courant
	var $copy_data;			///< Pendant une boucle, contient les donn�es originales du bloc de template, sans aucun traitement, de fa�on � la r�cup�rer au d�but de chaque it�ration
	var $iNbLoops;			///< Nombre courant d'it�ration en cas de boucles sur un bloc
	var $asData;			///< Tableau qui contient les donn�es trait�es (remplacements) pour chaque it�ration pendant une boucle
	
	/**
	 * Constructeur. Initialise la balise "nom du bloc", le template parent, et d�bute le traitement sur ce dernier
	 * 
	 * @param	looptag		le nom des balises entourant le bloc de template
	 * @param	template	l'objet Template parent
	 * 
	 * @see	#extraire()
	 */
	function TPL_Block($looptag, &$template)
	{
		$this->looptag = $looptag;
		$this->template_parent = &$template;
		$this->extraire();
	}
	
	/**
	 * Initialise les donn�es du bloc pour les pr�parer � une boucle. Le bloc pourra �tre r�p�t� plusieurs fois de suite
	 * dans le template, � son emplacement d'origine et avec des remplacements diff�rents par it�rations. Utilis� par 
	 * exemple pour cr�er les lignes d'une table HTML
	 */
	function beginLoop()
	{
		$this->copy_data = $this->data;
		$this->data = "";
		$this->iNbLoops = 0;
	}
	
	/**
	 * Passe � l'it�ration suivante d'une boucle sur un bloc de template. Les donn�es ayant subi des traitements
	 * (remplacements) dans l'it�ration courante sont sauvegard�es d'abord
	 */
	function nextLoop()
	{
		if ($this->data != "")
			$this->asData[] = $this->data;
		$this->data = $this->copy_data;
		$this->iNbLoops++;
	}
	
	/**
	 * Retourne le nombre d'it�rations de boucle qui ont eu lieu jusqu'ici sur ce bloc de template
	 * 
	 * @return	le nombre actuel d'it�rations dans un boucle sur un bloc de template
	 */
	function countLoops()
	{
		return $this->iNbLoops;
	}
	
	/**
	 * Remplace "simplement" des morceaux de bloc de template par d'autres. Utilis� pour inclure des variables dans le 
	 * bloc de template, qui seront remplac�es en PHP
	 * 
	 * @param	in	la cha�ne de caract�re � trouver dans le bloc de template
	 * @param	out	la cha�ne par laquelle on veut remplacer toutes les occurences de \p in
	 * 
	 * @see		Template#remplacer()
	 */
	function remplacer($in, $out)
	{
		$this->data = str_replace($in, $out, $this->data);
	}
	
	/**
	 * "Extrait" le bloc de son template parent. Cela consiste � remplacer dans ce dernier le bloc et son contenu par 
	 * un texte temporaire, qui pourra ensuite �tre remplac� par le bloc apr�s traitements comme les boucles et 
	 * remplacements.
	 * 
	 * Par exemple le bloc suivant :
	 * 
	 * <code>
	 * [bloc+]
	 * <tr>
	 *   <td>{nom_utilisateur}</td>
	 *   <td>{prenom_utilisateur}</td>
	 * </tr>
	 * [bloc-]
	 * <code>
	 * 
	 * sera enti�rement remplac�, le temps des traitements sur le bloc (avant l'appel � #afficher() donc), par 
	 * \c [bloc_tmp] dans son template parent. Lorsque les traitements sont termin�s et qu'on demande l'affichage 
	 * du bloc r�sultant, \c [bloc_tmp] sera remplac� dans le Template parent par ce bloc r�sultant
	 */
	function extraire()
	{
		if (strpos($this->template_parent->data,"[$this->looptag-]"))
		{
			$debut = strpos($this->template_parent->data, "[".$this->looptag."+]");
			$fin = strpos($this->template_parent->data, "[".$this->looptag."-]", $debut) + strlen("[".$this->looptag."-]"); // + taille de la balise finale
			$this->data = substr($this->template_parent->data, $debut, ($fin-$debut));
			$this->template_parent->data = str_replace($this->data, "[$this->looptag"."_tmp]", $this->template_parent->data);
			// enlevement des balises + et - ds le tableau
			$this->data = str_replace("[$this->looptag+]", "", $this->data);
			$this->data = str_replace("[$this->looptag-]", "", $this->data);
		}
	}
	
	// {{{ Ajout� par Fil
	/**
	 * Pas de description pour le moment
	 * 
	 * @param	v_iCycle
	 * 
	 * @todo	Compl�ter cette description
	 */
	function cycle($v_iCycle = NULL)
	{
		if (strpos($this->data,"[CYCLE:"))
		{
			$iNbBoucles = $this->iNbLoops-1;
			$debut = strpos($this->data, "[CYCLE:");
			$fin = strpos($this->data, "]", $debut);
			$sVariable = substr($this->data, $debut, ($fin-$debut)+1);
			$asListeCycles = explode("|", substr($sVariable, 7, -1));
			$iNbCycles = count($asListeCycles);
			$iCycle = (isset($v_iCycle) ? $v_iCycle : $iNbBoucles%$iNbCycles);
			$this->data = str_replace($sVariable, $asListeCycles[$iCycle], $this->data);
		}
	}
	
	/**
	 * R�cup�re les variables situ�es � l'int�rieur d'un bloc
	 * 
	 * @param	string	$looptag
	 * @param	boolean	$v_bRetTableau
	 * 
	 * @return	une cha�ne de caract�res ou un tableau de cha�ne de caract�res
	 * 
	 * @todo	V�rifier cette description
	 */
	function defVariable($looptag, $v_bRetTableau = FALSE, $v_sSeparateur="###")
	{
		$sVariable = NULL;
		
		if (strpos($this->data, "[$looptag-]"))
		{
			$debut = strpos($this->data, "[$looptag+]");
			$fin = strpos($this->data, "[$looptag-]") + strlen("[$looptag-]"); // + taille de la balise finale
			$sVariable = substr($this->data, $debut, ($fin-$debut));
			//effacement du bloc
			$this->data = str_replace($sVariable, "", $this->data);
			// enlevement des balises + et - ds le tableau
			$sVariable = str_replace("[$looptag+]", "", $sVariable);
			$sVariable = str_replace("[$looptag-]", "", $sVariable);
			$sVariable = trim($sVariable);
		}
		
		return ($v_bRetTableau && isset($sVariable) ? explode($v_sSeparateur,$sVariable) : $sVariable);
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @param	looptag			A compl�ter
	 * @param	v_sSeparateur	A compl�ter
	 * 
	 * @return	A compl�ter
	 * 
	 * @todo	Compl�ter cette description
	 */
	function defTableau($looptag, $v_sSeparateur = ",")
	{
		return $this->defVariable($looptag, TRUE, $v_sSeparateur);
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @param	looptag		A compl�ter
	 * 
	 * @todo	Compl�ter cette description
	 */
	function effacerVariable($looptag)
	{
		$this->defVariable($looptag);
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @return	A compl�ter
	 * 
	 * @todo	Compl�ter cette description
	 */
	function retDonnees()
	{
		return $this->data;
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @param	v_sDonnees	A compl�ter
	 * 
	 * @todo	Compl�ter cette description
	 */
	function defDonnees($v_sDonnees)
	{
		$this->data = $v_sDonnees;
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @return	A compl�ter
	 * 
	 * @todo	Compl�ter cette description
	 */
	function caracteres()
	{
		return strlen($this->data);
	}
	// }}}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @param	sTexteAjout
	 * 
	 * @todo	Compl�ter cette description
	 */
	function ajouter($sTexteAjout)
	{
		$this->data = $this->data . $sTexteAjout;
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @todo	Compl�ter cette description
	 */
	function effacer()
	{
		$this->template_parent->data = str_replace("[$this->looptag"."_tmp]", "", $this->template_parent->data);
	}
	
	/**
	 * Pas de description pour le moment
	 * 
	 * @todo	Compl�ter cette description
	 */
	function afficher()
	{
		if (count($this->asData))
			$this->data = implode("", $this->asData) . $this->data;
		$this->template_parent->data = str_replace("[$this->looptag"."_tmp]", $this->data, $this->template_parent->data);
	}
}

?>
