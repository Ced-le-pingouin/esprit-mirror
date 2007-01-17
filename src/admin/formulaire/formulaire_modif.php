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

require_once("globals.inc.php");
$oProjet = new CProjet();
$oTpl = new Template("formulaire_modif.tpl");
// Déclaration des différents blocks html
$oBlockIntro = new TPL_Block("BLOCK_INTRO",$oTpl);
$oBlockModifFormul = new TPL_Block("BLOCK_MODIF_FORMUL",$oTpl);
$oBlockModifTxtLong = new TPL_Block("BLOCK_MODIF_TXTLONG",$oTpl);
$oBlockModifTxtCourt = new TPL_Block("BLOCK_MODIF_TXTCOURT",$oTpl);
$oBlockModifNombre = new TPL_Block("BLOCK_MODIF_NOMBRE",$oTpl);
$oBlockModifListeDer = new TPL_Block("BLOCK_MODIF_LISTEDER",$oTpl);
$oBlockModifRadio = new TPL_Block("BLOCK_MODIF_RADIO",$oTpl);
$oBlockModifCocher = new TPL_Block("BLOCK_MODIF_COCHER",$oTpl);
$oBlockModifMPTexte = new TPL_Block("BLOCK_MODIF_MPTEXTE",$oTpl);
$oBlockModifMPSep = new TPL_Block("BLOCK_MODIF_MPSEP",$oTpl);
// Vérification de la permission d'utiliser le concepteur de formulaire
if($oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{
	// Récupération des variables passées en paramètres
	if(isset($_GET['idobj']))
	{
		$v_iIdObjForm = $_GET['idobj'];
		$v_iIdFormulaire = $_GET['idformulaire'];
		$v_iNouvPos = $_POST['ordreobj'];
	}
	else 
	{
			$v_iIdObjForm = 0;
			$v_iIdFormulaire = 0;
	}
	if(isset($_GET['bMesForms']))
		$bMesForms = $_GET['bMesForms'];
	else
		$bMesForms = 0;
	// Définitions de variable
	$bFlagErreur = false;
	$sRecharger = "";
	$sMessageErreur1 = $sMessageErreur2 = $sMessageErreur3 = $sMessageErreur4 = "";
	if($v_iIdObjForm > 0) //--- cas où un objet de formulaire est sélectionné ---
	{
		$oBlockIntro->effacer();
		$oBlockModifFormul->effacer();
		$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
		$iIdObjActuel = $oObjetFormulaire->retId();
		if(isset($_POST['envoyer']))
			$oObjetFormulaire->DeplacerObjet($v_iNouvPos);
		$iOrdreObjFormDepart = $oObjetFormulaire->retOrdreObjFormul();
		$oTypeObj = new CTypeObjetFormul($oProjet->oBdd, $oObjetFormulaire->retIdType());
		$oTpl->remplacer("{Titre_page}","Elément ".$iOrdreObjFormDepart." &gt;&gt; ".$oTypeObj->retDescTypeObj());
		switch($oObjetFormulaire->retIdTypeObj())
		{
			// question ouverte de type "texte long"
			case 1:	$oBlockModifTxtLong->afficher();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->effacer(); 
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->effacer(); 
					$oQTexteLong = new CQTexteLong($oProjet->oBdd,$v_iIdObjForm);
					if(isset($_POST['envoyer']))
					{
						// Récupération des variables transmises par le formulaire
						$oQTexteLong->defEnonQTL( stripslashes($_POST['Enonce']) );
						$oQTexteLong->defAlignEnonQTL( $_POST['AlignEnon'] );
						$oQTexteLong->defAlignRepQTL( $_POST['AlignRep'] );
						$oQTexteLong->defLargeurQTL( $_POST['Largeur'] );
						$oQTexteLong->defHauteurQTL( $_POST['Hauteur'] );		
						// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
						if(strlen($_POST['Enonce']) < 1) { $sMessageErreur1="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!(int)$_POST['Largeur']) { $sMessageErreur2="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!(int)$_POST['Hauteur']){ $sMessageErreur3="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!$bFlagErreur) //si pas d'erreur, enregistrement physique
						{
							$oQTexteLong->enregistrer();
							$sRecharger = "<script>\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)\n</script>\n";
						}
					}
					$oTpl->remplacer("{EnonQTL}",$oQTexteLong->retEnonQTL());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oQTexteLong->retAlignEnonQTL(),$oQTexteLong->retAlignRepQTL());
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{ar1}",$ar1);
					$oTpl->remplacer("{ar2}",$ar2);
					$oTpl->remplacer("{ar3}",$ar3);
					$oTpl->remplacer("{ar4}",$ar4);
					$oTpl->remplacer("{LargeurQTL}",$oQTexteLong->retLargeurQTL());
					$oTpl->remplacer("{HauteurQTL}",$oQTexteLong->retHauteurQTL());
					$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
					$oTpl->remplacer("{sMessageErreur2}",$sMessageErreur2);
					$oTpl->remplacer("{sMessageErreur3}",$sMessageErreur3);
					break;
			
			// question ouverte de type "texte court"
			case 2:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->afficher();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->effacer();
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->effacer();
					$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
					if(isset($_POST['envoyer'])) 
					{
						// Récupération des variables transmises par le formulaire
						$oQTexteCourt->defEnonQTC( stripslashes($_POST['Enonce']) );
						$oQTexteCourt->defAlignEnonQTC( $_POST['AlignEnon'] );
						$oQTexteCourt->defAlignRepQTC( $_POST['AlignRep'] );
						$oQTexteCourt->defTxtAvQTC( stripslashes($_POST['TxtAv']) );
						$oQTexteCourt->defTxtApQTC( stripslashes($_POST['TxtAp']) );
						$oQTexteCourt->defLargeurQTC( $_POST['Largeur'] );
						$oQTexteCourt->defMaxCarQTC( $_POST['MaxCar'] );		
						// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
						if(!(int)$_POST['Largeur']) { $sMessageErreur1="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!((int)$_POST['MaxCar'] || strlen($_POST['MaxCar']) < 1 )) { $sMessageErreur2="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!$bFlagErreur) //si pas d'erreur, enregistrement physique
						{
							$oQTexteCourt->enregistrer();
							$sRecharger = "<script>\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)\n</script>\n";
						}
					}
					$oTpl->remplacer("{EnonQTC}",$oQTexteCourt->retEnonQTC());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oQTexteCourt->retAlignEnonQTC(),$oQTexteCourt->retAlignRepQTC());
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{ar1}",$ar1);
					$oTpl->remplacer("{ar2}",$ar2);
					$oTpl->remplacer("{ar3}",$ar3);
					$oTpl->remplacer("{ar4}",$ar4);
					$oTpl->remplacer("{TxtAvQTC}",emb_htmlentities($oQTexteCourt->retTxtAvQTC()));
					$oTpl->remplacer("{TxtApQTC}",emb_htmlentities($oQTexteCourt->retTxtApQTC()));
					$oTpl->remplacer("{LargeurQTC}",$oQTexteCourt->retLargeurQTC());
					$oTpl->remplacer("{MaxCarQTC}",$oQTexteCourt->retMaxCarQTC());
					$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
					$oTpl->remplacer("{sMessageErreur2}",$sMessageErreur2);
					break;
			
			// question semi-ouverte de type "nombre"
			case 3:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->afficher();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->effacer(); 
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->effacer(); 
					$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
					if(isset($_POST['envoyer'])) 
					{
						// Récupération des variables transmises par le formulaire
						$oQNombre->defEnonQN( stripslashes($_POST['Enonce']) );
						$oQNombre->defAlignEnonQN( $_POST['AlignEnon'] );
						$oQNombre->defAlignRepQN( $_POST['AlignRep'] );
						$oQNombre->defTxtAvQN( stripslashes($_POST['TxtAv']) );
						$oQNombre->defTxtApQN( stripslashes($_POST['TxtAp']) );
						$oQNombre->defNbMinQN( $_POST['NbMin'] );
						$oQNombre->defNbMaxQN( $_POST['NbMax'] );
						$oQNombre->defMultiQN( $_POST['Multi'] );
						// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
						if(!is_numeric($_POST['NbMin'])) { $sMessageErreur1="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!is_numeric($_POST['NbMax'])) {$sMessageErreur2="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!is_numeric($_POST['Multi'])) { $sMessageErreur3="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!$bFlagErreur) //si pas d'erreur, enregistrement physique
						{
							$oQNombre->enregistrer();
							$sRecharger = "<script>\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)\n</script>\n";
						}
					}
					$oTpl->remplacer("{EnonQN}",$oQNombre->retEnonQN());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oQNombre->retAlignEnonQN(),$oQNombre->retAlignRepQN());
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{ar1}",$ar1);
					$oTpl->remplacer("{ar2}",$ar2);
					$oTpl->remplacer("{ar3}",$ar3);
					$oTpl->remplacer("{ar4}",$ar4);
					$oTpl->remplacer("{TxtAvQN}",emb_htmlentities($oQNombre->retTxtAvQN()));
					$oTpl->remplacer("{TxtApQN}",emb_htmlentities($oQNombre->retTxtApQN()));
					$oTpl->remplacer("{NbMinQN}",$oQNombre->retNbMinQN());
					$oTpl->remplacer("{NbMaxQN}",$oQNombre->retNbMaxQN());
					$oTpl->remplacer("{MultiQN}",$oQNombre->retMultiQN());
					$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
					$oTpl->remplacer("{sMessageErreur2}",$sMessageErreur2);
					$oTpl->remplacer("{sMessageErreur3}",$sMessageErreur3);
					break;
			
			// question fermée de type "liste déroulante"
			case 4:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->afficher();
					$oBlockModifRadio->effacer(); 
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->effacer(); 
					$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
					$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
					if(isset($_POST['envoyer']))
					{
						// Récupération des variables transmises par le formulaire
						$oQListeDeroul->defEnonQLD( stripslashes($_POST['Enonce']) );
						$oQListeDeroul->defAlignEnonQLD( $_POST['AlignEnon'] );
						$oQListeDeroul->defAlignRepQLD( $_POST['AlignRep'] );
						$oQListeDeroul->defTxtAvQLD( stripslashes($_POST['TxtAv']) );
						$oQListeDeroul->defTxtApQLD( stripslashes($_POST['TxtAp']) );
						// Enregistrement des réponses et de leurs poids pour les differents axes
						if(isset($_POST["rep"])) 	// on doit verifier car lorsque l'on appuie la premiere fois, apres avoir cree l'objet, 
						{							// sur ajouter, $_POST["rep"] n'existe pas 
							$aiOrdre = $_POST["selOrdreProposition"];
							foreach ($_POST["rep"] as $v_iIdReponse => $v_sTexteTemp) 
							{
								// initialisation avec la proposition de réponse courante (faut-il le faire, ou définir l'IdPropRep et IdFormul?)
								$oPropositionReponse = new CPropositionReponse($oProjet->oBdd,$v_iIdReponse);
								$oPropositionReponse->defTextePropRep(stripslashes($v_sTexteTemp));
								$oPropositionReponse->defOrdrePropRep($aiOrdre[$v_iIdReponse]);
								if($oFormulaire->retAutoCorrection())
								{
									$oPropositionReponse->defScorePropRep($_POST["correctionRep"][$v_iIdReponse]);
									$oPropositionReponse->defFeedbackPropRep($_POST["feedbackRep"][$v_iIdReponse]);
								}
								$oPropositionReponse->enregistrer();
								if(isset($_POST["repAxe"])) 	// Vérifier pour ne pas effectuer le traitement si aucun axe n'est défini pour ce formulaire
								{
									$tab = $_POST["repAxe"];
									foreach ($tab[$v_iIdReponse] as $v_iIdAxe => $v_iPoids)
									{
										if(($v_iPoids != "") && (is_numeric($v_iPoids)))
										{
											$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
											$oReponse_Axe->defIdPropRep($v_iIdReponse);
											$oReponse_Axe->defIdAxe($v_iIdAxe);
											$oReponse_Axe->defPoids($v_iPoids);
											$oReponse_Axe->enregistrer();
										}
									}
								}
							}
						}
						// Enregistrement de l'objet oQListeDeroul actuel dans la BD
						$oQListeDeroul->enregistrer();
						$sRecharger = "<script>rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)</script>\n";
						// Ajout d'une réponse
						// Attention lorsque l'on clique sur le lien 'Ajouter' cela implique également 
						// un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.(si pas d'erreur) 
						if($_POST['typeaction'] == 'ajouter')
						{
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
							$iOrdreMax = 1 + $oPropositionReponse->retMaxOrdre($iIdObjActuel);
							$oPropositionReponse->defIdObjFormul($v_iIdObjForm);
							$oPropositionReponse->defOrdrePropRep($iOrdreMax);
							/* 	La réponse qui sera créée ici contiendra : 	le numero de l'objet auquel elle appartient
																			l'ordre dans lequel elle sera affichée (toujours en dernière place)
																			son numéro d'identifiant sera attribué automatiquement par MySql */
							$oPropositionReponse->enregistrer();
						}
						// Suppression d'une réponse
						// Attention lorsque l'on clique sur le lien 'supprimer' cela implique également 
						// un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.(si pas d'erreur)
						if($_POST['typeaction'] == 'supprimer')
						{
							$v_iIdReponse = $_POST['parametre'];
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd,$v_iIdReponse);
							$oPropositionReponse->effacer();
						}
					}
					$oTpl->remplacer("{EnonQLD}",$oQListeDeroul->retEnonQLD());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oQListeDeroul->retAlignEnonQLD(),$oQListeDeroul->retAlignRepQLD());
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{ar1}",$ar1);
					$oTpl->remplacer("{ar2}",$ar2);
					$oTpl->remplacer("{ar3}",$ar3);
					$oTpl->remplacer("{ar4}",$ar4);
					$oTpl->remplacer("{TxtAvQLD}",emb_htmlentities($oQListeDeroul->retTxtAvQLD()));
					$oTpl->remplacer("{TxtApQLD}",emb_htmlentities($oQListeDeroul->retTxtApQLD()));
					$oTpl->remplacer("{RetourReponseQLDModif}",$oQListeDeroul->RetourReponseQLDModif($v_iIdObjForm,$v_iIdFormulaire,$oFormulaire->retAutoCorrection()));
					break;
			
			// question fermée de type "radio"
			case 5:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->afficher(); 
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->effacer(); 
					$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
					$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
					// réception d'un formulaire
					if(isset($_POST['envoyer']))
					{
						// Récupération des variables transmises par le formulaire
						$oQRadio->defEnonQR( stripslashes($_POST['Enonce']) );
						$oQRadio->defAlignEnonQR( $_POST['AlignEnon'] );
						$oQRadio->defAlignRepQR( $_POST['AlignRep'] );
						$oQRadio->defTxtAvQR( stripslashes($_POST['TxtAv']) );
						$oQRadio->defTxtApQR( stripslashes($_POST['TxtAp']) );
						$oQRadio->defDispQR( $_POST['Disp'] );
						// Enregistrement des réponses et de leurs poids pour les differents axes
						if(isset($_POST["rep"])) 	//on doit verifier car lorsque l'on appuie la premiere fois sur ajouter apres avoir créé l'objet 
						{							// $_POST["rep"] n'existe pas 
							$aiOrdre = $_POST["selOrdreProposition"];
							foreach ($_POST["rep"] as $v_iIdReponse => $v_sTexteTemp) 
							{
								$oPropositionReponse = new CPropositionReponse($oProjet->oBdd,$v_iIdReponse);
								$oPropositionReponse->defTextePropRep(stripslashes($v_sTexteTemp));
								$oPropositionReponse->defOrdrePropRep($aiOrdre[$v_iIdReponse]);
								if($oFormulaire->retAutoCorrection())
								{
									$oPropositionReponse->defScorePropRep($_POST["correctionRep"][$v_iIdReponse]);
									$oPropositionReponse->defFeedbackPropRep($_POST["feedbackRep"][$v_iIdReponse]);
								}
								$oPropositionReponse->enregistrer();
								if(isset($_POST["repAxe"])) 	//Vérifier pour ne pas effectuer le traitement si aucun axe n'est défini pour ce formulaire
								{
									$tab = $_POST["repAxe"];
									foreach ($tab[$v_iIdReponse] as $v_iIdAxe => $v_iPoids)
									{
										if(($v_iPoids != "") && (is_numeric($v_iPoids)))
										{
											$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
											$oReponse_Axe->defIdPropRep($v_iIdReponse);
											$oReponse_Axe->defIdAxe($v_iIdAxe);
											$oReponse_Axe->defPoids($v_iPoids);
											$oReponse_Axe->enregistrer();
										}
									}
								}
							}
						}
						// Enregistrement de l'objet QRadio actuel dans la BD
						$oQRadio->enregistrer();
						// Lorsque la question est bien enregistrée dans la BD, on rafraîchit la liste en cochant l'objet que l'on est en train de traiter
						$sRecharger = "<script>rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)</script>\n";
						// Ajout d'une réponse
						// Attention lorsque l'on clique sur le lien 'Ajouter' cela implique également 
						// un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.(si pas d'erreur) 
						if($_POST['typeaction'] == 'ajouter')
						{
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
							$iOrdreMax = 1 + $oPropositionReponse->retMaxOrdre($iIdObjActuel);
							$oPropositionReponse->defIdObjFormul($v_iIdObjForm);
							$oPropositionReponse->defOrdrePropRep($iOrdreMax);
							/* La réponse qui sera créée ici contiendra :	le numero de l'objet auquel elle appartient
																			l'ordre dans lequel elle sera affichée (toujours en dernière place)
																			son numéro d'identifiant sera attribué automatiquement par MySql
																			le texte de la réponse sera attribué par après.	*/
							$oPropositionReponse->enregistrer();
						}
						// Suppression d'une réponse
						// Attention lorsque l'on clique sur le lien 'supprimer' cela implique également 
						// un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.(si pas d'erreur)
						if($_POST['typeaction']=='supprimer')
						{
							$v_iIdReponse = $_POST['parametre'];
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd,$v_iIdReponse);
							$oPropositionReponse->effacer();
						}
					}
					$oTpl->remplacer("{EnonQR}",$oQRadio->retEnonQR());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oQRadio->retAlignEnonQR(),$oQRadio->retAlignRepQR());
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{ar1}",$ar1);
					$oTpl->remplacer("{ar2}",$ar2);
					$oTpl->remplacer("{ar3}",$ar3);
					$oTpl->remplacer("{ar4}",$ar4);
					if($oQRadio->retDispQR()=="Hor")
					{
						$oTpl->remplacer("{d1}","checked=\"checked\"");
						$oTpl->remplacer("{d2}","");
					}
					else
					{
						$oTpl->remplacer("{d1}","");
						$oTpl->remplacer("{d2}","checked=\"checked\"");
					}
					$oTpl->remplacer("{TxtAvQR}",emb_htmlentities($oQRadio->retTxTAvQR()));
					$oTpl->remplacer("{TxtApQR}",emb_htmlentities($oQRadio->retTxtApQR()));
					$oTpl->remplacer("{RetourReponseQRModif}",$oQRadio->RetourReponseQRModif($v_iIdObjForm,$v_iIdFormulaire,$oFormulaire->retAutoCorrection()));
					break;
			
			// question fermée de type "case à cocher"
			case 6:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->effacer(); 
					$oBlockModifCocher->afficher();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->effacer(); 
					$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
					$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
					if(isset($_POST['envoyer']))
					{
						// Récupération des variables transmises par le formulaire
						$oQCocher->defEnonQC( stripslashes($_POST['Enonce']) );
						$oQCocher->defAlignEnonQC( $_POST['AlignEnon'] );
						$oQCocher->defAlignRepQC( $_POST['AlignRep'] );
						$oQCocher->defTxtAvQC( stripslashes($_POST['TxtAv']) );
						$oQCocher->defTxtApQC( stripslashes($_POST['TxtAp']) );
						$oQCocher->defDispQC( $_POST['Disp'] );
						$oQCocher->defNbRepMaxQC( $_POST['NbRepMax'] );		
						$oQCocher->defMessMaxQC( $_POST['MessMax'] );
						// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
						if(!(int)$_POST['NbRepMax'])
						{ $sMessageErreur2 = "<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!$bFlagErreur) //si pas d'erreur, enregistrement physique dans la BD
						{
							// Enregistrement des réponses et de leurs poids pour les differents axes
							if(isset($_POST["rep"])) 	//on doit verifier car lorsque l'on appuie la premiere fois apres avoir cree l'objet 
							{							//sur ajouter, $_POST["rep"] n'existe pas 
								$aiOrdre = $_POST["selOrdreProposition"];
								foreach ($_POST["rep"] as $v_iIdReponse => $v_sTexteTemp) 
								{
									$oPropositionReponse = new CPropositionReponse($oProjet->oBdd,$v_iIdReponse);
									$oPropositionReponse->defTextePropRep(stripslashes($v_sTexteTemp));
									$oPropositionReponse->defOrdrePropRep($aiOrdre[$v_iIdReponse]);
									if($oFormulaire->retAutoCorrection())
									{
										$oPropositionReponse->defScorePropRep($_POST["correctionRep"][$v_iIdReponse]);
										$oPropositionReponse->defFeedbackPropRep($_POST["feedbackRep"][$v_iIdReponse]);
									}
									$oPropositionReponse->enregistrer();
									if(isset($_POST["repAxe"])) 	// Vérifier pour ne pas effectuer le traitement si aucun axe 
									{								// n'est défini pour ce formulaire
										$tab = $_POST["repAxe"];
										foreach ($tab[$v_iIdReponse] as $v_iIdAxe => $v_iPoids)
										{
											if(($v_iPoids != "") && (is_numeric($v_iPoids)))
											{
												$oReponse_Axe = new CReponse_Axe($oProjet->oBdd);
												$oReponse_Axe->defIdPropRep($v_iIdReponse);
												$oReponse_Axe->defIdAxe($v_iIdAxe);
												$oReponse_Axe->defPoids($v_iPoids);
												$oReponse_Axe->enregistrer();
											}
										}
									}
								}
							}
							// Enregistrement de l'objet QCocher actuel dans la BD
							$oQCocher->enregistrer();
							$sRecharger = "<script>\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)\n</script>\n";
						}
						// Ajout d'une réponse
						// Attention lorsque l'on clique sur le lien 'Ajouter' cela implique également 
						// un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.(si pas d'erreur) 
						if($_POST['typeaction'] == 'ajouter')
						{
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
							$iOrdreMax = 1 + $oPropositionReponse->retMaxOrdre($iIdObjActuel);
							$oPropositionReponse->defIdObjFormul($v_iIdObjForm);
							$oPropositionReponse->defOrdrePropRep($iOrdreMax);
							/* La réponse qui sera créée ici contiendra :	le numero de l'objet auquel elle appartient
																			l'ordre dans lequel elle sera affichée (toujours en dernière place)
																			son numéro d'identifiant sera attribué automatiquement par MySql
																			le texte de la réponse sera attribué par après.	*/
							$oPropositionReponse->enregistrer();
						}
						// Suppression d'une réponse
						// Attention lorsque l'on clique sur le lien 'supprimer' cela implique également 
						// un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.(si pas d'erreur)
						if($_POST['typeaction'] == 'supprimer')
						{
							$v_iIdReponse = $_POST['parametre'];
							$oPropositionReponse = new CPropositionReponse($oProjet->oBdd,$v_iIdReponse);
							$oPropositionReponse->effacer();
						}
					}
					$oTpl->remplacer("{EnonQC}",$oQCocher->retEnonQC());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oQCocher->retAlignEnonQC(),$oQCocher->retAlignRepQC());
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{ar1}",$ar1);
					$oTpl->remplacer("{ar2}",$ar2);
					$oTpl->remplacer("{ar3}",$ar3);
					$oTpl->remplacer("{ar4}",$ar4);
					$oTpl->remplacer("{TxtAvQC}",emb_htmlentities($oQCocher->retTxtAvQC()));
					$oTpl->remplacer("{TxtApQC}",emb_htmlentities($oQCocher->retTxtApQC()));
					if($oQCocher->retDispQC()=="Hor")
					{
						$oTpl->remplacer("{d1}","checked=\"checked\"");
						$oTpl->remplacer("{d2}","");
					}
					else
					{
						$oTpl->remplacer("{d1}","");
						$oTpl->remplacer("{d2}","checked=\"checked\"");
					}
					$oTpl->remplacer("{NbRepMaxQC}",$oQCocher->retNbRepMaxQC());
					$oTpl->remplacer("{MessMaxQC}",$oQCocher->retMessMaxQC());
					$oTpl->remplacer("{RetourReponseQCModif}",$oQCocher->RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire,$oFormulaire->retAutoCorrection()));
					$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
					$oTpl->remplacer("{sMessageErreur2}",$sMessageErreur2);
					$oTpl->remplacer("{sMessageErreur3}",$sMessageErreur3);
					break;
			
			case 7:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->effacer(); 
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->afficher();
					$oBlockModifMPSep->effacer(); 
					$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
					if(isset($_POST['envoyer'])) 
					{
						// Récupération des variables transmises par le formulaire
						$oMPTexte->defAlignMPT( $_POST['Align'] );
						$oMPTexte->defTexteMPT( stripslashes($_POST['Texte']) );
						// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
						if($oMPTexte->retTexteMPT() == "") { $sMessageErreur1="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!$bFlagErreur)  //si pas d'erreur, enregistrement physique
						{
							$oMPTexte->enregistrer();
							$sRecharger = "<script>\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)\n</script>\n";
						}
					}
					$oTpl->remplacer("{TexteMPT}",$oMPTexte->retTexteMPT());
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oMPTexte->retAlignMPT(),"");
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
					break;
			
			case 8:	$oBlockModifTxtLong->effacer();
					$oBlockModifTxtCourt->effacer();
					$oBlockModifNombre->effacer();
					$oBlockModifListeDer->effacer();
					$oBlockModifRadio->effacer(); 
					$oBlockModifCocher->effacer();
					$oBlockModifMPTexte->effacer();
					$oBlockModifMPSep->afficher(); 
					$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
					if(isset($_POST['envoyer'])) 
					{
						// Récupération des variables transmises par le formulaire
						$oMPSeparateur->defLargeurMPS( $_POST['Largeur'] );
						$oMPSeparateur->defTypeLargMPS( $_POST['TypeLarg'] );
						$oMPSeparateur->defAlignMPS( $_POST['Align'] );
						// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
						if(!(int)$_POST['Largeur']) { $sMessageErreur1="<font color =\"red\">*</font>"; $bFlagErreur = true; }
						if(!$bFlagErreur) //si pas d'erreur, enregistrement physique
						{
							$oMPSeparateur->enregistrer();
							$sRecharger = "<script>\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms)\n</script>\n";
						}
					}
					$oTpl->remplacer("{LargeurMPS}",$oMPSeparateur->retLargeurMPS());
					$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
					$sAR1 = $sAR2 = "";
					if($oMPSeparateur->retTypeLargMPS()=="P")
					{
						$oTpl->remplacer("{sAR1}","checked=\"checked\"");
						$oTpl->remplacer("{sAR2}","");
					}
					else
					{
						$oTpl->remplacer("{sAR1}","");
						$oTpl->remplacer("{sAR2}","checked=\"checked\"");
					}
					list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($oMPSeparateur->retAlignMPS()," ");
					$oTpl->remplacer("{ae1}",$ae1);
					$oTpl->remplacer("{ae2}",$ae2);
					$oTpl->remplacer("{ae3}",$ae3);
					$oTpl->remplacer("{ae4}",$ae4);
					break;
		}
		$sParam="?idobj=".$v_iIdObjForm."&amp;idformulaire=".$v_iIdFormulaire."&amp;bMesForms=".$bMesForms;
		$oTpl->remplacer("{sParam}",$sParam);
		$oTpl->remplacer("{sRecharger}",$sRecharger);
		// Gestion de l'affichage de la position de l'élément
		$oBlockPos = new TPL_Block("BLOCK_POSITION",$oTpl);
		$aoListeObjFormul = $oObjetFormulaire->retListeObjFormulaire($v_iIdFormulaire);
		if(!empty($aoListeObjFormul))
		{
			$oBlockPos->beginLoop();
			foreach($aoListeObjFormul AS $oObjetFormulaire)
			{
				$oBlockPos->nextLoop();
				$iOrdreObjForm = $oObjetFormulaire->retOrdreObjFormul();
				$oBlockPos->remplacer("{ordre_obj_form}",$iOrdreObjForm);
				if($iOrdreObjForm == $iOrdreObjFormDepart)
					$oBlockPos->remplacer("{obj_actuel}","selected=\"selected\"");
				else
					$oBlockPos->remplacer("{obj_actuel}","");
			}
			$oBlockPos->afficher();
		}
		else
		{
			$oBlockPos->effacer();
		}
	}
	else 
	{
		$oBlockModifTxtLong->effacer();
		$oBlockModifTxtCourt->effacer();
		$oBlockModifNombre->effacer();
		$oBlockModifListeDer->effacer();
		$oBlockModifRadio->effacer(); 
		$oBlockModifCocher->effacer();
		$oBlockModifMPTexte->effacer();
		$oBlockModifMPSep->effacer(); 
		if($v_iIdFormulaire != 0 ) //--- Cas où on a cliqué sur le titre du formulaire ---
		{
			$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
			$oBlockIntro->effacer();
			$oBlockModifFormul->afficher();
			$oTpl->remplacer("{Titre_page}","Options de l'activité en ligne");
			if(isset($_POST['envoyer'])) 
			{
				// Récupération des variables transmises par le formulaire et insertion ds l'objet formulaire
				$oFormulaire->defTitre(stripslashes($_POST['Titre']));
				$oFormulaire->defEncadrer($_POST['Encadrer']);
				$oFormulaire->defLargeur($_POST['Largeur']);
				$oFormulaire->defTypeLarg($_POST['TypeLarg']);
				$oFormulaire->defInterElem($_POST['InterElem']);
				$oFormulaire->defInterEnonRep($_POST['InterEnonRep']);
				$oFormulaire->defRemplirTout($_POST['RemplirTout']);
				$oFormulaire->defAutoCorrection($_POST['AutoCorrection']);
				$oFormulaire->defMethodeCorrection($_POST['Methode']);
				$oFormulaire->defStatut(1); //$_POST['Statut'];
				$oFormulaire->defType($_POST['Type']);
				// Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
				if(strlen($_POST['Titre']) < 1)
				{
					$sMessageErreur1 = "<font color =\"red\">*</font>";
					$bFlagErreur = true;
				}
				
				if(!((int)$_POST['Largeur'] || strlen($_POST['Largeur']) < 1 || $_POST['Largeur'] == "0")) 
				{
					$sMessageErreur2 = "<font color =\"red\">*</font>"; 
					$bFlagErreur = true;
				}
				
				if(!((int)$_POST['InterElem'] || strlen($_POST['InterElem']) < 1 || $_POST['InterElem'] == "0")) 
				{
					$sMessageErreur3 = "<font color =\"red\">*</font>"; 
					$bFlagErreur = true;
				}
				if(!((int)$_POST['InterEnonRep'] || strlen($_POST['InterEnonRep']) < 1 || $_POST['InterEnonRep'] == "0")) 
				{
					$sMessageErreur4 = "<font color =\"red\">*</font>"; 
					$bFlagErreur = true;
				}
				if(!$bFlagErreur) //si pas d'erreur, enregistrement physique
				{
					$oFormulaire->enregistrer();
					$sRecharger = "<script type=\"text/javascript\">\n rechargerliste($v_iIdFormulaire,$v_iIdObjForm,$bMesForms); \n</script>\n";
				} 
			}
			$oTpl->remplacer("{sRecharger}",$sRecharger);
			$oTpl->remplacer("{sMessageErreur1}",$sMessageErreur1);
			$oTpl->remplacer("{sMessageErreur2}",$sMessageErreur2);
			$oTpl->remplacer("{sMessageErreur3}",$sMessageErreur3);
			$oTpl->remplacer("{sMessageErreur4}",$sMessageErreur4);
			$sParam = "?idobj=".$v_iIdObjForm."&amp;idformulaire=".$v_iIdFormulaire."&amp;bMesForms=".$bMesForms;
			$oTpl->remplacer("{sParam}",$sParam);
			$oTpl->remplacer("{Titre}",emb_htmlentities($oFormulaire->retTitre()));
			$oTpl->remplacer("{Largeur}",$oFormulaire->retLargeur());
			$oTpl->remplacer("{InterElem}",$oFormulaire->retInterElem());
			$oTpl->remplacer("{InterEnonRep}",$oFormulaire->retInterEnonRep());
			if($oFormulaire->retEncadrer() == 1)
			{
				$oTpl->remplacer("{sEncadr1}","checked=\"checked\"");
				$oTpl->remplacer("{sEncadr2}","");
			}
			else
			{
				$oTpl->remplacer("{sEncadr1}","");
				$oTpl->remplacer("{sEncadr2}","checked=\"checked\"");
			}
			if($oFormulaire->retTypeLarg() == "P")
			{
				$oTpl->remplacer("{sTypeLargeur1}","checked=\"checked\"");
				$oTpl->remplacer("{sTypeLargeur2}","");
			}
			else
			{
				$oTpl->remplacer("{sTypeLargeur1}","");
				$oTpl->remplacer("{sTypeLargeur2}","checked=\"checked\"");
			}
			if($oFormulaire->retType() == "prive")
			{
				$oTpl->remplacer("{sType1}","checked=\"checked\"");
				$oTpl->remplacer("{sType2}","");
			}
			else
			{
				$oTpl->remplacer("{sType1}","");
				$oTpl->remplacer("{sType2}","checked=\"checked\"");
			}
			if($oFormulaire->retRemplirTout())
				$oTpl->remplacer("{sRemplirToutSel}","checked=\"checked\"");
			else
				$oTpl->remplacer("{sRemplirToutSel}","");
			if($oFormulaire->retAutoCorrection())
				$oTpl->remplacer("{sAutoCorrectionSel}","checked=\"checked\"");
			else
				$oTpl->remplacer("{sAutoCorrectionSel}","");
			if($oFormulaire->retMethodeCorrection() == 1)
			{
				$oTpl->remplacer("{sMethode_1}","checked=\"checked\"");
				$oTpl->remplacer("{sMethode_0}","");
			}
			else
			{
				$oTpl->remplacer("{sMethode_0}","checked=\"checked\"");
				$oTpl->remplacer("{sMethode_1}","");
			}
		}
		else	//--- Cas où aucune valeur n'a encore été envoyée (c-à-d chargement de la page) ---
		{
			$oBlockIntro->afficher();
			$oBlockModifFormul->effacer();
			$oTpl->remplacer("{Titre_page}","Options de l'activité en ligne");
		}
	}
	$oTpl->afficher();
}
$oProjet->terminer();
?>
