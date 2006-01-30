<?php
require_once("globals.inc.php");
$oProjet = new CProjet();
//************************************************
//*       R�cup�ration des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{
	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
	$v_iIdAxeS = $HTTP_GET_VARS['axe_s'];
	$v_iIdAxeM = $HTTP_GET_VARS['axe_m'];
	$v_sDescAxeM = $HTTP_GET_VARS['axemodif'];
	$v_sDescAxeA = $HTTP_GET_VARS['axeajout'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
	$v_iIdAxeS = $HTTP_POST_VARS['axe_s'];
	$v_iIdAxeM = $HTTP_POST_VARS['axe_m'];
	$v_sDescAxeM = $HTTP_POST_VARS['axemodif'];
	$v_sDescAxeA = $HTTP_POST_VARS['axeajout'];
}
else
{
	$v_iIdFormulaire = 0;
	$v_iIdAxeS = 0;
	$v_iIdAxeM = 0;
	$v_sDescAxeM = "";
	$v_sDescAxeA = "";
}


if (isset($HTTP_GET_VARS['supprimer']))
{
	  echo "<html>\n";
	  echo "<head>\n";
	  echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
	  //CSS
	  echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
	  //FIN CSS
	  echo "</head>\n";
	  echo "<body class=\"popup\">";
	  
	  $oAxe = new CAxe($oProjet->oBdd,$v_iIdAxeS);
	  $oAxe->effacer(TRUE); //TRUE permet une v�rification des d�pendances avant effacement de l'axe
	  
	  echo "<p align=center><a href=\"gestion_axes.php\">Retour page pr�c�dente</a></p>";
	  echo "</body>\n";
	  echo "</html>\n";
}
else if (isset($HTTP_GET_VARS['modifier']))
		{
			  echo "<html>\n";
			  echo "<head>\n";
			  echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
			  //CSS
			  echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
			  //FIN CSS
			  echo "</head>\n";
			  echo "<body class=\"popup\">";
			  
			  if (strlen($v_sDescAxeM) > 0)
			  {	  
			  $oAxe = new CAxe($oProjet->oBdd,$v_iIdAxeM);
			  $oAxe->defDescAxe($v_sDescAxeM);
			  $oAxe->enregistrer();
			  echo "<h4 align=\"center\"><br>Le nom de l'axe a �t� correctement modifi�</h4>";
			  
			  $oAxe->verificationdependances();
			  }
			  else
			  {
					 echo "<h4 align=\"center\"><br>Le nom de l'axe n'est pas valide</h4>";
			  }
			  
		  	  echo "<p align=center><a href=\"gestion_axes.php\">Retour page pr�c�dente</a></p>";			  
			  echo "</body>\n";
			  echo "</html>\n";
		}
		
		else if (isset($HTTP_GET_VARS['ajouter']))
				{
					  echo "<html>\n";
					  echo "<head>\n";
					  echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
					  echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
					  echo "</script>\n";
					  //CSS
					  echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
					  //FIN CSS
					  echo "</head>\n";
					  echo "<body class=\"popup\">";
					  
					   if (strlen($v_sDescAxeA) > 0)
						{
							 $oAxe = new CAxe($oProjet->oBdd);
							 $oAxe->defDescAxe($v_sDescAxeA);
							 $oAxe->enregistrer();
							
							 echo "<h4 align=\"center\"><br>L'axe a  �t� correctement ajout�</h4>";
						}
						else
						{
							  echo "<h4 align=\"center\"><br>Le nom de l'axe n'est pas valide</h4>";
						}
					  
					  echo "<p align=center><a href=\"gestion_axes.php\">Retour page pr�c�dente</a></p>";
					  echo "</body>\n";
					  echo "</html>\n";
				}
				
				else // premier chargement de la page
					{
						  $hResult = $oProjet->oBdd->executerRequete("SELECT * FROM Axe order by IdAxe");
						  
						  $oTpl = new Template("gestion_axes.tpl");
						  
						  $oBlock = new TPL_Block("BLOCK_AXES",$oTpl);
						  
								 if(TRUE)
								 {
									 $oBlock->beginLoop();
									 
									 while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
									 {
										 $oBlock->nextLoop();
										 $oAxe = new CAxe($oProjet->oBdd); //Cr�e un objet objetformulaire "presque vide"
										 $oAxe->init($oEnreg); //Remplit l'objet cr�� ci-dessus avec l'enreg en cours
					
										 $oBlock->remplacer("{id_axe}",$oAxe->retId());
										 $oBlock->remplacer("{desc_axe}",$oAxe->retDescAxe());
										 
										
										 
									 }
									 
									 $oBlock->afficher();
								 }
								 else
								 {
									 $oBlock->effacer();
								 }
						  
						  $hResult = $oProjet->oBdd->executerRequete("SELECT * FROM Axe order by IdAxe");
						  $oBlock = new TPL_Block("BLOCK_AXES2",$oTpl);
						  
								 if(TRUE)
								 {
									 $oBlock->beginLoop();
									 
									 while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
									 {
										 $oBlock->nextLoop();
										 $oAxe = new CAxe($oProjet->oBdd); //Cr�e un objet objetformulaire "presque vide"
										 $oAxe->init($oEnreg); //Remplit l'objet cr�� ci-dessus avec l'enreg en cours
					
										 $oBlock->remplacer("{id_axe2}",$oAxe->retId());
										 $oBlock->remplacer("{desc_axe2js}",addslashes($oAxe->retDescAxe()));
										 $oBlock->remplacer("{desc_axe2}",$oAxe->retDescAxe());
										
										 
									 }
									 
									 $oBlock->afficher();
								 }
								 else
								 {
									 $oBlock->effacer();
								 }
						  
						  
					
						  $oTpl->afficher();	  
						  $oProjet->oBdd->libererResult($hResult);
						  $oProjet->terminer();  //Ferme la connection avec la base de donn�es
					 }

?>
