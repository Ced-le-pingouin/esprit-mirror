<?php

class CCell_CSS extends CTable_CSS
{
	var $m_iEtirerColonne;
	var $m_bRetourLigne;
	var $m_iEtirerLigne;
	
	var $m_iHauteur;
	var $m_iLargeur;
		
	//var $m_sStyle;
	

	/* ********************************************************************** */
	/* Constructeur                                                           */
	/* ********************************************************************** */

	function CCell_CSS ($v_sNom=NULL,$v_sAlignement=NULL,$v_sVAlignement=NULL,$v_sEtirerColonne=NULL,$v_sEtirerLigne=NULL)
	{
		// Type de l'�l�ment
		$this->ID_ELEMENT = ID_TD;
		
		// D�terminer si le nom est de type classe ou identifiant
		if (strstr ($v_sNom,".") || strstr ($v_sNom,"#"))
			$this->m_sNom = "TD{$v_sNom}";
		else if (strlen ($v_sNom))
			$this->m_sNom = "TD.{$v_sNom}";
		else
			$this->m_sNom = "TD";
				
		// Initialiser les variables communs
		$this->init ();
		
		// Initialiser les variables des cellules
		$this->Alignement ($v_sAlignement);
		$this->VAlignement ($v_sVAlignement);
		$this->EtirerColonne ($v_sEtirerColonne);
		$this->EtirerLigne ($v_sEtirerLigne);
	}


	/* ********************************************************************** */
	/* D�finir les attributs des cellules                                     */
	/* ********************************************************************** */
	
	/*
	** EtirerColonne
	**
	** Description
	**
	** 		Cette fonction assigne � une variable l'�tendue de la cellule.
	**
	** Entr�e
	**
	** 		$v_sEtirerColonne: Valeur enti�re.Chiffre qui indique le nombre de cellule
	** 		� �tendre.
	**
	*/
	
	function EtirerColonne ($v_iEtirerColonne=NULL)
	{
		if ($v_iEtirerColonne === NULL)
			return $this->m_iEtirerColonne;

		$this->m_iEtirerColonne = $v_iEtirerColonne;
	}

	/*
	** RetourLigne
	**
	** Description
	**
	** 		Cette fonction permet d'indiquer aux navigateurs qu'il devra
	** 		emp�cher les retours � la ligne.
	**
	** Entr�e
	**
	** 		$v_bRetourLigne: Valeur bool�enne.
	**
	** Remarque
	**
	** 		Cet attribut a �t� d�clar� obsol�te par le W3C. Il est conseill�
	** 		d'utiliser les styles tels que "white-space" (faut-il encore que 
	** 		les navigateurs les supportes).
	**
	*/
	
	function RetourLigne ($v_bRetourLigne=NULL)
	{
		if ($v_bRetourLigne === NULL)
			return $this->m_bRetourLigne;
		
		$this->m_bRetourLigne = $v_bRetourLigne;
	}
	
	function Hauteur ($v_iHauteur=NULL)
	{
		if ($v_iHauteur === NULL)
			return $this->m_iHauteur;
		
		$this->m_iHauteur = $v_iHauteur;
	}
	
	function EtirerLigne ($v_iEtirerLigne=NULL)
	{
		if ($v_iEtirerLigne === NULL)
			return $this->m_iEtirerLigne;

		$this->m_iEtirerLigne = $v_iEtirerLigne;
	}
	

	/* ********************************************************************** */
	/* Afficher les attributs                                                 */
	/* ********************************************************************** */

	function Attributs ($v_bStyles=TRUE)
	{
		$sAttributs = NULL;
		
		if ($v_bStyles === TRUE)
			$bStyles = $this->Style ();
						
		$ImageFond = $this->Fond->Image ();

		// Attributs de l'�l�ment
		$sAttributs .= aFond ($ImageFond)
			.aAlignerHorizontalement ($this->m_sAlignement)
			.aAlignerVerticalement ($this->m_sVAlignement)
			.aEtendreColonne ($this->m_iEtirerColonne)
			.aEtendreLigne ($this->m_iEtirerLigne)
			.aLargeur ($this->m_iLargeur)
			.aHauteur ($this->m_iHauteur);
						
		if (strlen ($sAttributs))
			echo (($bStyles) ? " " : NULL).trim ($sAttributs);

	}
	

	/* ********************************************************************** */
	/* Afficher les styles                                                    */
	/* ********************************************************************** */

	function Style ()
	{
		$sStyle = NULL;
		
		// Styles de l'�l�ment
		$tmp = $this->Fond->Couleur->Couleur ();

		if (!isset ($tmp))
			$tmp = $this->Fond->Couleur ();	

		if (isset ($tmp)) 
			$sStyle .= CouleurFond ($tmp);

		$sStyle .= CouleurTexte ($this->m_sColor);
			//.sAlignerHorizontalement ($this->m_sAlignement)	// :NE PAS SUPPRIMER: Sera utilis� plus tard
			//.sAlignerVerticalement ($this->m_sVAlignement)		// Idem
			//.sHauteur ($this->m_iHauteur);					// Idem

		if (isset ($sStyle))
			$sStyles .= " style=\"".trim ($sStyle)."\"";
		
		echo $sStyles;
		
		return strlen ($sStyles);
	}
	

	/* ********************************************************************** */
	/* Inclure dans les feuilles de style                                     */
	/* ********************************************************************** */

	function inclure ()
	{
		if (!isset ($this->m_sNom))
			return;
			
		$sStyle = $this->m_sNom."\n"
			."{\n"
			."\t".AlignementTexte ($this->Texte->Alignement ())."\n"
			."}\n";
		
		echo str_replace ("\t\n",NULL,$sStyle);
	}	
}

?>
