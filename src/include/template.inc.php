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
 * @file	template.inc.php
 * 
 * Contient les classes pour la gestion des templates
 * 
 * @date	2002/03/21
 * 
 * @author	Jérôme TOUZE
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 */

/**
 * Gestion de templates, avec possibilité de remplacements de variables, de répétition et masquage de blocs
 */
class Template
{
	var $data = NULL;	///< Contenu textuel du template, avec les remplacements éventuels
	
	/**
	 * Constructeur. Initialise le le template avec le contenu d'un fichier, théoriquement au format HTML
	 * 
	 * @param	v_sTplFichier	le nom du fichier template à charger
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
	 * Ajoute le contenu d'un fichier template à la fin du contenu actuel
	 * 
	 * @param	v_sTplFichier	le nom du fichier template à ajouter
	 */
	function ajouterTemplate($v_sTplFichier)
	{
		$this->Template($v_sTplFichier);
	}
	
	/**
	 * Retourne une "variable de template", càd un bloc entre balises [bloc+][bloc-]. Par exemple si le template 
	 * contient un segment "[bloc+]contenu[bloc-]", un appel <code>defVariable("bloc")</code> renvoie le texte \c "contenu"
	 * 
	 * @param	looptag		le nom du bloc dont les balises entourent la variable de template
	 * 
	 * @return	la variable trouvée. S'il n'existe pas de variable définie par des blocs [looptag+][lopptag-], renvoie 
	 * 			\c null
	 */
	function defVariable($looptag, $effacer = TRUE)
	{
		$sVariable = NULL;
		
		if(strpos($this->data,"[$looptag-]"))
		{
			$debut = strpos($this->data, "[$looptag+]");
			$fin = strpos($this->data, "[$looptag-]", $debut) + strlen("[$looptag-]"); // + taille de la balise finale
			$sVariable = substr($this->data, $debut, ($fin-$debut));
			//effacement du bloc
			if ($effacer)
				$this->data = str_replace($sVariable, "", $this->data);
			else
				$this->data = preg_replace('/\['.$looptag.'[-+]\]/', '', $this->data);
			// enlevement des balises + et - ds le tableau
			$sVariable = str_replace("[$looptag+]", "", $sVariable);
			$sVariable = str_replace("[$looptag-]", "", $sVariable);
			$sVariable = trim($sVariable);
		}
		
		return $sVariable;
	}
	
	/**
	 * Remplace "simplement" des morceaux de template par d'autres. Utilisé pour inclure des variables dans le template 
	 * qui seront remplacées en PHP, par exemple un appel <code>remplacer("{utilisateur}", $oProjet->oUtilisateur->retPseudo())</code>
	 * remplace toutes les chaînes \c "{utilisateur}" trouvées dans le template par le pseudo de l'utilisateur connecté
	 * 
	 * @param	in	la chaîne de caractère à trouver dans le template
	 * @param	out	la chaîne par laquelle on veut remplacer toutes les occurences de \p in
	 */
	function remplacer($in, $out)
	{
		$this->data = str_replace($in, $out, $this->data);
	}
	
	/**
	 * Affiche les données contenues actuellement dans le template. Généralement appelé à la fin du script PHP qui 
	 * utilise le template. Un remplacement automatique se fait avant l'affichage, sur les faux protocoles <code>
	 * "racine://", "admin://", "commun://", "theme://", "javascript://",</code> et \c "lib://", qui sont remplacés par, 
	 * respectivement, les resultats des appels aux fonctions globales <code>dir_root_plateform(), dir_admin(), 
	 * dir_theme_commun(), dir_theme(), dir_javascript(),</code> et \c dir_lib()
	 * 
	 * @see	#retDonnees()
	 */
	function afficher()
	{
		// {{{ Ajouté par Fil
		$asRechercher = array("racine://", "admin://", "commun://", "theme://", "javascript://", "lib://");
		$asRemplacer = array(dir_root_plateform(NULL, FALSE),dir_admin(), dir_theme_commun(), dir_theme(), dir_javascript(), dir_lib());
		$this->data = str_replace($asRechercher, $asRemplacer, $this->data);
		// }}}
		echo $this->retDonnees();
	}
	
	// {{{ Ajouté par Fil
	/**
	 * Retourne le contenu actuel du template, avec les remplacements et autres traitements de blocs éventuels
	 * 
	 * @return	le contenu du template après traitements
	 */
	function retDonnees()
	{
		return trim($this->data);
	}
	
	/**
	 * Retourne le nombre de caractères contenu dans le template, tel qu'il se trouve après les éventuels traitements 
	 * de blocs, remplacements, etc
	 * 
	 * @return	le nombre de caractères contenus dans le template après traitements
	 */
	function caracteres()
	{
		return strlen($this->data);
	}
	// }}}

	function activerBloc($looptag, $activ = TRUE)
	{
		if ($activ)
			$this->data = preg_replace('%\['.$looptag.'[-+]\]%', '', $this->data);
		else
			$this->desactiverBloc($looptag);
	}	
	
	function desactiverBloc($looptag)
	{
		$this->data = preg_replace('%\['.$looptag.'\+\].*\['.$looptag.'\-\]\\n?%s', '', $this->data);
	}
}

/**
 * Gestion des blocs de templates, càd des parties masquables ou répétables d'un template
 * 
 * @todo	Etudier la possibilité de faire hériter cette classe de Template, car certaines fonctions sont identiques
 */
class TPL_Block
{
	var $looptag;			///< Balise qui entourait le bloc dans le template parent
	var $data;				///< Contenu textuel courant du bloc, donc avec les remplacements et répétitions éventuels
	var $template_parent;	///< Objet Template parent, auquel appartient le bloc courant
	var $copy_data;			///< Pendant une boucle, contient les données originales du bloc de template, sans aucun traitement, de façon à la récupérer au début de chaque itération
	var $iNbLoops;			///< Nombre courant d'itération en cas de boucles sur un bloc
	var $asData;			///< Tableau qui contient les données traitées (remplacements) pour chaque itération pendant une boucle
	
	/**
	 * Constructeur. Initialise la balise "nom du bloc", le template parent, et débute le traitement sur ce dernier
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
	 * Initialise les données du bloc pour les préparer à une boucle. Le bloc pourra être répété plusieurs fois de suite
	 * dans le template, à son emplacement d'origine et avec des remplacements différents par itérations. Utilisé par 
	 * exemple pour créer les lignes d'une table HTML
	 */
	function beginLoop()
	{
		$this->copy_data = $this->data;
		$this->data = "";
		$this->iNbLoops = 0;
	}
	
	/**
	 * Passe à l'itération suivante d'une boucle sur un bloc de template. Les données ayant subi des traitements
	 * (remplacements) dans l'itération courante sont sauvegardées d'abord
	 */
	function nextLoop()
	{
		if ($this->data != "")
			$this->asData[] = $this->data;
		$this->data = $this->copy_data;
		$this->iNbLoops++;
	}
	
	/**
	 * Retourne le nombre d'itérations de boucle qui ont eu lieu jusqu'ici sur ce bloc de template
	 * 
	 * @return	le nombre actuel d'itérations dans un boucle sur un bloc de template
	 */
	function countLoops()
	{
		return $this->iNbLoops;
	}
	
	/**
	 * Remplace "simplement" des morceaux de bloc de template par d'autres. Utilisé pour inclure des variables dans le 
	 * bloc de template, qui seront remplacées en PHP
	 * 
	 * @param	in	la chaîne de caractère à trouver dans le bloc de template
	 * @param	out	la chaîne par laquelle on veut remplacer toutes les occurences de \p in
	 * 
	 * @see		Template#remplacer()
	 */
	function remplacer($in, $out)
	{
		$this->data = str_replace($in, $out, $this->data);
	}
	
	/**
	 * "Extrait" le bloc de son template parent. Cela consiste à remplacer dans ce dernier le bloc et son contenu par 
	 * un texte temporaire, qui pourra ensuite être remplacé par le bloc après traitements comme les boucles et 
	 * remplacements.
	 * 
	 * Par exemple le bloc suivant :
	 * 
	 * @code
	 * [bloc+]
	 * <tr>
	 *   <td>{nom_utilisateur}</td>
	 *   <td>{prenom_utilisateur}</td>
	 * </tr>
	 * [bloc-]
	 * @endcode
	 * 
	 * sera entièrement remplacé, le temps des traitements sur le bloc (avant l'appel à #afficher() donc), par 
	 * \c [bloc_tmp] dans son template parent. Lorsque les traitements sont terminés et qu'on demande l'affichage 
	 * du bloc résultant, \c [bloc_tmp] sera remplacé dans le Template parent par ce bloc résultant
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
	
	// {{{ Ajouté par Fil
	/**
	 * Remplace, dans l'itération courante d'une boucle, une balise de type [CYCLE:] par sa valeur pour cette itération.
	 * Voici un exemple de balise qui permettrait, pour chaque itération d'une une boucle créant des lignes de table 
	 * HTML, de modifier la classe CSS de chaque ligne afin d'en alterner les couleurs (classes ligneImpaire et lignePaire) :
	 * 
	 * @code
	 * [bloc+]
	 * <tr class="[CYCLE:ligneImpaire|lignePaire]">
	 *   <td>{nom_utilisateur}</td>
	 *   <td>{prenom_utilisateur}</td>
	 * </tr>
	 * [bloc-]
	 * @endcode
	 * 
	 * Les valeurs possibles pour les cycles sont donc séparées par un carcatère '|'.
	 * 
	 * @param	v_iCycle	l'index de la variable de cycle (la première ayant l'index zéro) qui remplacera la balise.
	 * 						S'il est absent, on prend l'index résultant de l'opération : 
	 * 							(index de l'itération courante    modulo    nb total de cycles possibles)
	 * 						Ce qui entraînera un cycle croissant dans les valeurs possibles (pour l'exemple, cela donne 
	 * 						ligneImpaire pour la 1ère itération, lignePaire pour la 2è, ligneImpaire pour la 3è, etc.
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
	 * Récupère les variables situées à l'intérieur d'un bloc. Les "variables" sont délimitées par des balises 
	 * [bloc+][bloc-], mais le contenu des balises, plutôt que d'être considéré comme des données à afficher comme 
	 * c'est le cas pour un véritable bloc, est retourné comme "variable", soit sous forme de chaîne (défaut), soit sous 
	 * forme de tableau. Dans ce dernier cas, le contenu des balises doit comprendre des séparateurs, qui seront 
	 * utilisés pour diviser le contenu en éléments de tableau. Au retour de la fonction, le bloc et son contenu sont 
	 * effacés du template. Une variable ne peut donc être récupérée qu'une seule fois dans un template
	 * 
	 * Exemple:
	 * 
	 * @code
	 * [variable+]<img src="image1.gif">|<img src="image2.gif">[variable-]
	 * @endcode
	 * 
	 * Si le code ci-dessus se trouve dans un bloc de template représenté en PHP par l'objet \c $oBloc, un appel de type 
	 * <code>$oBloc->defVariable("variable", $asTableau, '|');</code> retournera un tableau de deux éléments (chaînes), 
	 * le premier contenant <code>&lt;img src="image1.gif"&gt;</code>, la seconde <code>&lt;img src="image2.gif"&gt;
	 * </code>
	 * 
	 * @param	looptag			le nom du bloc "variable", tel qu'utilisé dans les balises de délimitation + et -
	 * @param	v_bRetTableau	si \c true, le contenu devra être retourné sous forme de tableau
	 * @param	v_sSeparateur	lorsque \p v_bRetTableau vaut \c true, ce paramètre est utilisé pour déterminer les
	 * 							éléments à extraire
	 * 
	 * @return	une chaîne de caractères ou un tableau de chaîne de caractères représentant le contenu de la variable 
	 * 			délimitée par le bloc
	 * 
	 */
	function defVariable($looptag, $v_bRetTableau = FALSE, $v_sSeparateur="###")
	{
		$sVariable = NULL;
		
		if (strpos($this->data, "[$looptag-]"))
		{
			$debut = strpos($this->data, "[$looptag+]");
			$fin = strpos($this->data, "[$looptag-]", $debut) + strlen("[$looptag-]"); // + taille de la balise finale
			$sVariable = substr($this->data, $debut, ($fin - $debut));
			//effacement du bloc
			$this->data = str_replace($sVariable, "", $this->data);
			// enlevement des balises + et - ds le tableau
			$sVariable = str_replace("[$looptag+]", "", $sVariable);
			$sVariable = str_replace("[$looptag-]", "", $sVariable);
			$sVariable = trim($sVariable);
		}
		
		return ($v_bRetTableau && isset($sVariable) ? explode($v_sSeparateur, $sVariable) : $sVariable);
	}
	
	/**
	 * Raccourci vers la fonction #defVariable() avec l'option tableau à \c true, et le séparateur par défaut à ','
	 * 
	 * @param	looptag			le nom du bloc "variable", tel qu'utilisé dans les balises de délimitation + et -
	 * @param	v_sSeparateur	la chaîne de caracères à utiliser pour séparer les éléments de la variables, qui seront
	 * 							retournés sous forme de tableau
	 * 
	 * @return	un tableau de chaînes de caractères représentant le contenu de la variable délimitée par le bloc
	 * 
	 * @see		#defVariable()
	 */
	function defTableau($looptag, $v_sSeparateur = ",")
	{
		return $this->defVariable($looptag, TRUE, $v_sSeparateur);
	}
	
	/**
	 * Efface complètement un bloc (variable) dans un bloc de template
	 * 
	 * @param	looptag		le nom du bloc contenant la variable, tel qu'utilisé dans les balises + et -
	 */
	function effacerVariable($looptag)
	{
		$this->defVariable($looptag);
	}
	
	/**
	 * Retourne le contenu actuel du bloc de template, avec les remplacements et autres traitements de éventuels
	 * 
	 * @return	le contenu du bloc de template après traitements
	 */
	function retDonnees()
	{
		return $this->data;
	}
	
	/**
	 * Définit le contenu du bloc de template. Cela écrase l'ancien contenu
	 * 
	 * @param	v_sDonnees	le texte représentant le nouveau contenu du bloc de template
	 */
	function defDonnees($v_sDonnees)
	{
		$this->data = $v_sDonnees;
	}
	
	/**
	 * Retourne le nombre de caractères qui composent le contenu du bloc de template
	 * 
	 * @return	le nombre de cractères contenus dans le bloc
	 */
	function caracteres()
	{
		return strlen($this->data);
	}
	// }}}
	
	/**
	 * Ajouter du texte à la suite du contenu actuel du bloc de template
	 * 
	 * @param	sTexteAjout		le texte à ajouter
	 */
	function ajouter($sTexteAjout)
	{
		$this->data = $this->data . $sTexteAjout;
	}
	
	/**
	 * Effacer complètement ce bloc dans le template parent. Cela qui implique l'effacement de la balise temporaire 
	 * qui avait été créée dans le parent, pour la durée des traitements/remplacements
	 */
	function effacer()
	{
		$this->template_parent->data = str_replace("[$this->looptag"."_tmp]", "", $this->template_parent->data);
	}
	
	/**
	 * Affiche le contenu du bloc de template dans son parent. Si le bloc est une boucle, cette fonction doit tout de
	 * même n'être appelée qu'une seule fois, après la boucle (composée d'appels à #beginLoop(), #nextLoop(), 
	 * #remplacer()), ce qui a pour effet d'afficher en une seule fois toutes les itérations de la boucle
	 * 
	 * @see	Template#afficher()
	 */
	function afficher()
	{
		if (count($this->asData))
			$this->data = implode("", $this->asData) . $this->data;
		$this->template_parent->data = str_replace("[$this->looptag"."_tmp]", $this->data, $this->template_parent->data);
	}
	
	function activerBloc($looptag, $activ = TRUE)
	{
		if ($activ)
			$this->data = preg_replace('%\['.$looptag.'[-+]\]%', '', $this->data);
		else
			$this->desactiverBloc($looptag);
	}
	
	function desactiverBloc($looptag)
	{
		$this->data = preg_replace('%\['.$looptag.'\+\].*\['.$looptag.'\-\]\\n?%s', '', $this->data);
	}
}

/**
 * Gestion d'un bloc de template particulier, qui peut servir à afficher des listes d'éléments "composites", càd dont 
 * chaque élément peut être simple, ou lui-même une liste d'éléments composites. C'est donc différent d'une simple 
 * boucle, et sert par ex. à afficher une arborescence de fichiers (un dossier contient lui-même soit des fichiers, 
 * soit des dossiers, qui eux-même etc.), ou une arborescence de cours/unités/activités/etc. dans une formation.
 * 
 * Par exemple, pour afficher une liste de dossiers/fichiers, on peut définir le code HTML liste comme ceci:
 * 
 * @code
 * [liste+]
 * <ul>
 *   [liste_el+]<li>
 *     {nom_fichier}
 *     [@liste]
 *   </li>[liste_el-]
 * </ul>
 * [liste-]
 * @endcode
 * 
 * Donc, la liste elle-même se définit exactement comme un TPL_Block. A l'intérieur de celle-ci, on encadre le code qui 
 * représente un élément de liste par un bloc du même nom que la liste mais avec le suffixe \c _el. Ensuite, on place 
 * où on veut dans le code de l'élément, une balise du même nom que la liste mais précédée de \c @, qui indique où 
 * s'insérera automatiquement une sous-liste du même type dans le cas où l'élément n'est pas "simple" mais de type
 * composite (par ex. un dossier dans le cas de l'arborescence de fichiers).
 * 
 * Dans les méthodes de cette classe, la différence est principalement sur nextLoop(), qui demande pour chaque itération 
 * deux paramètres: une indication que l'élément courant est composite ou pas (simple), et le niveau d'imbrication 
 * actuel de l'arborescance (commençant à 0 automatiquement).
 * 
 * Cette classe peut par exemple être utilisée avec un IterateurRecursif de la lib std.   
 */
class TPL_Block_ListeComposite
{
	var $looptag;					///< Balise qui entourait le bloc dans le template parent
	var $iNiv;						///< Niveau actuel d'indentation de l'arborescence

	var $aTplsListes = array();		///< Tableau qui contient les différents niveaux de listes imbriquées
	var $aTplsElements = array();   ///< Tableau qui contient les éléments de chacune des listes ci-dessus
	var $sContenuListe;				///< Sauvegarde du contenu (code) de la liste initiale, qui sera inséré pour chaque sous-liste (élément composite)
	
	/**
	 * Constructeur. Initialise le niveau d'imbrication des listes à 0, le nom de la balise de liste, et les deux blocs
	 * initiaux représentant la liste de départ, et ses éléments
	 */
	function TPL_Block_ListeComposite($looptag, &$template)
	{
		$this->iNiv = 0;
		$this->looptag = $looptag;
		
		$this->aTplsListes[$this->iNiv] = new TPL_Block($looptag, $template);
		$this->sContenuListe = $this->aTplsListes[$this->iNiv]->retDonnees();
		$this->aTplsElements[$this->iNiv] = new TPL_Block($looptag.'_el', $this->aTplsListes[$this->iNiv]);
	}
	
	/**
	 * Voir TPL_Block#beginLoop()
	 */
	function beginLoop()
	{
		$this->aTplsElements[$this->iNiv]->beginLoop();
	}
	
	/**
	 * Voir TPL_Block#nextLoop()
	 */
	function nextLoop($bComposite, $iNiv)
	{
		if ($iNiv <= $this->iNiv)
			$this->_afficherNivsPrec($iNiv);
		else
			$this->iNiv = $iNiv;
		
		$this->aTplsElements[$this->iNiv]->nextLoop();

		if ($bComposite)
			$this->_prepNivSuiv();
		else
			$this->aTplsElements[$this->iNiv]->remplacer('[@'.$this->looptag.']', '');
	}
	
	/**
	 * Voir TPL_Block#afficher()
	 */
	function afficher()
	{
		$this->_afficherNivsPrec(0);
		
		$this->aTplsElements[$this->iNiv]->afficher();
		$this->aTplsListes[$this->iNiv]->afficher();
	}
	
	/**
	 * Voir TPL_Block#remplacer()
	 */
	function remplacer($in, $out)
	{
		$this->aTplsListes[$this->iNiv]->remplacer($in, $out);
		$this->aTplsElements[$this->iNiv]->remplacer($in, $out);
	}

	/**
	 * Vérifie s'il faut afficher des niveaux antérieurs de l'arborescence, dont le traitement est terminé.
	 * 
	 * (appelée à partir de #nextLoop() lorsqu'on reste au même niveau mais qu'une sous-liste précédente pourrait
	 * s'avérer vide de tout élément => on l'efface; ou si on est descendu d'un ou plusieurs niveaux depuis le dernier
	 * appel, auquel cas il faudra afficher les sous-listes terminées)
	 * 
	 * @param	iNivMin	le niveau jusqu'auquel il faut redescendre. Tous les niveaux supérieurs résultant des
	 * 			        précédentes itérations seront affichés 
	 */
	function _afficherNivsPrec($iNivMin)
	{
		if (isset($this->aTplsListes[$this->iNiv+1]))
			$this->aTplsListes[$this->iNiv+1]->effacer();
		
		for ($i = $this->iNiv; $i > $iNivMin; $i--)
		{
			$this->aTplsElements[$i]->afficher();
			$this->aTplsListes[$i]->afficher();

			unset($this->aTplsElements[$i]);
			unset($this->aTplsListes[$i]);
		}
		
		$this->iNiv = $iNivMin;
	}
	
	/**
	 * Lorsqu'un élément est un composite (sous-liste), il faut insérer et initialiser dans le bloc de l'élément au
	 * niveau courant, un bloc pour une nouvelle liste et également un nouveau bloc enfant de celle-ci pour ses éléments  
	 */
	function _prepNivSuiv()
	{
		$this->aTplsElements[$this->iNiv]->remplacer
		(
			'[@'.$this->looptag.']',
			'['.$this->looptag.'+]'.$this->sContenuListe.'['.$this->looptag.'-]'
		);
		$this->aTplsListes[$this->iNiv+1] = new TPL_Block($this->looptag, $this->aTplsElements[$this->iNiv]);
		$this->aTplsElements[$this->iNiv+1] = new TPL_Block($this->looptag.'_el', $this->aTplsListes[$this->iNiv+1]);
		$this->aTplsElements[$this->iNiv+1]->beginLoop();
	}
}

?>
