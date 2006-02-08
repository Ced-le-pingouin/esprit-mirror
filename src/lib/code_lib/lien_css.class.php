<?php

class CLink_CSS extends CCss
{
	var $m_sLink;
	var $m_sVisited;
	var $m_sHover;
		
	function CLink_CSS ($v_sNom=NULL)
	{
		$this->ID_ELEMENT = ID_LINK;
		
		$this->init ();
	
		// <-- A:LINK --------------------------------------------------------->
		if (!isset ($this->m_asLink))
			$this->m_asLink = array ();
			
		if (defined ("A_LINK_COLOR"))
			$this->m_asLink["Color"] = A_LINK_COLOR;
		
		if (defined ("A_LINK_BG_COLOR"))
			$this->m_asLink["BgColor"] = A_LINK_BG_COLOR;
			
		$this->m_asLink["Underline"] = (defined ("A_LINK_UNDERLINE")) ? A_LINK_UNDERLINE : FALSE;

		$this->m_asLink["Bold"] = (defined ("A_LINK_BOLD")) ? ((A_LINK_BOLD) ? "bold" : "normal") : NULL;

		// <-- A:VISITED ------------------------------------------------------>
		if (!isset ($this->m_asVisited))
			$this->m_asVisited = array ();

		if (defined ("A_VISITED_COLOR"))
			$this->m_asVisited["Color"] = A_VISITED_COLOR;

		if (defined ("A_VISITED_BG_COLOR"))
			$this->m_asVisited["BgColor"] = A_VISITED_BG_COLOR;
		
		$this->m_asVisited["Underline"] = (defined ("A_VISITED_UNDERLINE")) ? A_VISITED_UNDERLINE : FALSE;
	
		$this->m_asVisited["Bold"] = (defined ("A_VISITED_BOLD")) ? ((A_VISITED_BOLD) ? "bold" : "normal") : NULL;

		// <-- A:HOVER -------------------------------------------------------->
		if (!isset ($this->m_asHover))
			$this->m_asHover = array ();

		if (defined ("A_HOVER_COLOR"))
			$this->m_asHover["Color"] = A_HOVER_COLOR;

		if (defined ("A_HOVER_BG_COLOR"))
			$this->m_asHover["BgColor"] = A_HOVER_BG_COLOR;
		
		// Souligner le texte
		$this->m_asHover["Underline"] = (defined ("A_HOVER_UNDERLINE")) ? A_HOVER_UNDERLINE : FALSE;
		
		$this->m_asHover["Bold"] = (defined ("A_HOVER_BOLD")) ? ((A_HOVER_BOLD) ? "bold" : "normal") : NULL;
	}
	
	function inclure ()
	{
		// <-- A:LINK --------------------------------------------------------->
		$this->A_Link ();
	
		// <-- A: VISITED ----------------------------------------------------->
		$this->A_Visited ();
		
		// <-- A:HOVER -------------------------------------------------------->
		$this->A_Hover ();
	}
	
	function A_Link ()
	{
		$sStyle = "\na:link\n"
			."{\n"
			."\t".CouleurFond ($this->m_asLink["BgColor"])."\n"
			."\t".CouleurTexte ($this->m_asLink["Color"])."\n"
			."\t".sTexteSouligne ($this->m_asLink["Underline"])."\n"
			."\t".LargeurPolice ($this->m_asLink["Bold"])."\n"
			."}\n";
		
		$sRet = str_replace ("\t\n",NULL,$sStyle);
		
		echo trim ($sRet)."\n\n";
	}
	
	function A_Visited ()
	{
		$sStyle = "a:visited\n"
			."{\n\n"
			."\t".CouleurFond ($this->m_asVisited["BgColor"])."\n"
			."\t".CouleurTexte ($this->m_asVisited["Color"])."\n"
			."\t".sTexteSouligne ($this->m_asVisited["Underline"])."\n"
			."\t".LargeurPolice ($this->m_asVisited["Bold"])."\n"
			."}\n";
		
		$sRet = str_replace ("\t\n",NULL,$sStyle);
		
		echo trim ($sRet)."\n\n";
	}
	
	function A_Hover ()
	{
		$sStyle = "a:hover\n"
			."{\n\n"
			."\t".CouleurFond ($this->m_asHover["BgColor"])."\n"
			."\t".CouleurTexte ($this->m_asHover["Color"])."\n"
			."\t".sTexteSouligne ($this->m_asHover["Underline"])."\n"
			."\t".LargeurPolice ($this->m_asHover["Bold"])."\n"
			."}\n";
		
		$sRet = str_replace ("\t\n",NULL,$sStyle);
		
		echo trim ($sRet)."\n";
	}
}

?>
