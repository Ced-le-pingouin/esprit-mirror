<?php
require_once("globals.inc.php");
$oProjet = new CProjet();
//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{
	$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];

	//formulaire ci-dessous
	$v_iIdTypeObj = $HTTP_GET_VARS['idtypeobj'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdObjForm = $HTTP_POST_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];

	//formulaire ci-dessous
	$v_iIdTypeObj = $HTTP_POST_VARS['idtypeobj'];
}
else
{
	$v_iIdObjForm = 0;
	$v_iIdFormulaire = 0;
	$v_iIdTypeObj = 0;
}
	  
	 
	 //echo "<br>v_iIdTypeObj".$v_iIdTypeObj;
	 //echo "<br>v_iIdFormulaire : ".$v_iIdFormulaire;



echo "<html>\n";
echo "<head>\n";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
//FIN CSS

echo "</head>\n";


if (isset($HTTP_GET_VARS['ajouter']))
{


if ($v_iIdTypeObj > 0)
	  {
			 		  
					  $oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$oObjetFormulaire); //$oObjetFormulaire a vérifier ????
					  $oObjetFormulaire->defIdTypeObj($v_iIdTypeObj);
					  $oObjetFormulaire->defIdForm($v_iIdFormulaire);
					  
					  $iOrdreMaxObjForm = $oObjetFormulaire->OrdreMaxObjForm($oObjetFormulaire->retIdForm());
					 // echo "Nb obj : ".$iNbObjFormActuel;
					  $iOrdreMaxObjForm = $iOrdreMaxObjForm + 1; //pour placer l'objet a la fin du formulaire
					  
					  $oObjetFormulaire->defOrdreObjForm($iOrdreMaxObjForm);
					  
					  $oObjetFormulaire->enregistrer();
					  $iIdObjetFormActuel = $oObjetFormulaire->retId();


					switch($oObjetFormulaire->retIdTypeObj())
							 {
							 case 1:
							 	 //echo "Objet de type 1<br>";
									$oQTexteLong = new CQTexteLong($oProjet->oBdd);
									$oQTexteLong->ajouter($iIdObjetFormActuel);
									unset($oQTexteLong);
							 
							 break;
							 case 2:
								 //echo "Objet de type 2<br>";
							 $oQTexteCourt = new CQTexteCourt($oProjet->oBdd);
							 $oQTexteCourt->ajouter($iIdObjetFormActuel);
							 unset($oQTexteCourt);
							 
							 break;
							 case 3:
								 //echo "Objet de type 3<br>";
							 $oQNombre = new CQNombre($oProjet->oBdd);
							 $oQNombre->ajouter($iIdObjetFormActuel);
							 unset($oQNombre);
							 
							 break;
							 case 4:
								 //echo "Objet de type 4<br>";
							 $oQListeDeroul = new CQListeDeroul($oProjet->oBdd);
							 $oQListeDeroul->ajouter($iIdObjetFormActuel);
							 unset($oQListeDeroul);
							 
							 break;
							 case 5:
								 //echo "Objet de type 5<br>";
							 $oQRadio = new CQRadio($oProjet->oBdd);
							 $oQRadio->ajouter($iIdObjetFormActuel);
							 unset($oQRadio);
							 
							 break;
							 case 6:
								 //echo "Objet de type 6<br>";
							 $oQCocher = new CQCocher($oProjet->oBdd);
							 $oQCocher->ajouter($iIdObjetFormActuel);
							 unset($oQCocher);
							 
							 break;
							 case 7:
								 //echo "Objet de type 7<br>";
							 $oMPTexte = new CMPTexte($oProjet->oBdd);
							 $oMPTexte->ajouter($iIdObjetFormActuel);
							 unset($oMPTexte);
							 
							 break;
							 case 8:
								 //echo "Objet de type 8<br>";
							 $oMPSeparateur = new CMPSeparateur($oProjet->oBdd);
							 $oMPSeparateur->ajouter($iIdObjetFormActuel);
							 unset($oMPSeparateur);
							 
							 break;			   
							 default:
								 echo "Il est où le chiffre hein ?<br>";
							 }
	  }
echo "<body class=\"popup\" onLoad=\"popupajout($iIdObjetFormActuel,$v_iIdFormulaire); window.close ();\">";
echo "</body>\n";
}
else
	  {

	  $hResult = $oProjet->oBdd->executerRequete("SELECT * FROM TypeObjetForm");
	  $oTpl = new Template("formulaire_modif_ajout.tpl");
	  
	  $oBlock = new TPL_Block("BLOCK_MODIF_AJOUT",$oTpl);
	  
			 if(TRUE)
			 {
				 $oBlock->beginLoop();
				 
				 while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
				 {
					 $oBlock->nextLoop();
					 
					 $oTypeObjForm = new CTypeObjetForm($oProjet->oBdd); //Crée un objet formulaire "presque vide"
					 $oTypeObjForm->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
					 
					 $oBlock->remplacer("{desc_type_obj}",$oTypeObjForm->retDescTypeObj());
					 $oBlock->remplacer("{id_type_obj}",$oTypeObjForm->retId());
					 //$oBlock->remplacer("{id_formulaire}",$v_iIdFormulaire);
				 }
				 
				 $oBlock->afficher();
			 }
			 else
			 {
				 $oBlock->effacer();
			 }
	  
	  $oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);
	  $oTpl->afficher();	  
	  $oProjet->oBdd->libererResult($hResult);
	  $oProjet->terminer();  //Ferme la connection avec la base de données
	  }

?>
