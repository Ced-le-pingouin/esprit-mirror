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
 * @file    sous_activite_form_options.tbl.php
 *
 * Contient la classe de gestion des options des formulaires, en rapport avec la DB
 *
 * @date    2001/06/01
 *
 * @author  Loïc TAULEIGNE
 */

require_once(dir_lib("std/FichierInfo.php", TRUE));


/**
 * Gestion des options de formulaires
 */
class CSousActivFormulOptions
{
    var $iId;                    ///< Utilisé dans le constructeur, pour indiquer l'id de la sous-activité à récupérer dans la DB

    var $oBdd;                    ///< Objet représentant la connexion à la DB
    var $oEnregBdd;                ///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici

    /**
     * Constructeur.
     * @see CPersonne#CPersonne()
     *
     */
    function CSousActivFormulOptions(&$v_oBdd,$v_iIdSousActiv=NULL)
    {
        $this->oBdd = &$v_oBdd;
        $this->iId = $v_iIdSousActiv;

        if ($this->iId > 0)
            $this->init();
    }

    /**
     * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
     * @see CPersonne#init()
     */
    function init ($v_oEnregExistant=NULL)
    {
        if (is_object($v_oEnregExistant))
        {
            $this->oEnregBdd = $v_oEnregExistant;
            $this->iId = $this->oEnregBdd->IdSousActiv;
        }
        else
        {
            $sRequeteSql = "SELECT * FROM SousActiv_Formulaire_Options"
                ." WHERE IdSousActiv='".$this->retId()."'"
                ." LIMIT 1";
            $hResult = $this->oBdd->executerRequete($sRequeteSql);
            $this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
            $this->oBdd->libererResult($hResult);
        }
    }

    /**
     * Insère une copie des options du formulaire
     *
     */
    function copierOptionsFormulaire()
    {
        
    }

    /**
     * Insère ou met à jour un champ de la table SousActiv_Formulaire_Options
     *
     * @param   v_sNomChamp       le nom du champ à mettre à jour
     * @param   v_mValeurChamp    la nouvelle valeur du champ
     * @param   v_iIdSousActiv    l'id de la sous-activité
     *
     * @return  \c true si il a mis à jour le champ dans la DB
     */
    function MàJ_OptionsFormulaire ($v_sNomChamp,$v_mValeurChamp,$v_iIdSousActiv=0)
    {
        if ($v_iIdSousActiv < 1)
            $v_iIdSousActiv = $this->retId();

        if ($v_iIdSousActiv < 1)
            return FALSE;

        $sRequeteSql = "INSERT INTO  SousActiv_Formulaire_Options"
                     . " (idSousActiv, {$v_sNomChamp})"
                     . " VALUES (" . $v_iIdSousActiv . ", '".MySQLEscapeString($v_mValeurChamp)."')"
                     . " ON DUPLICATE KEY UPDATE {$v_sNomChamp}='".MySQLEscapeString($v_mValeurChamp)."'";
        $this->oBdd->executerRequete($sRequeteSql);
        return TRUE;
    }

    /**
     * Récupère le type d'affichage pour l'étudiant.
     * par défaut en "nouvelle fenêtre"
     * 
     * @params  v_iIdSousActiv      l'id de la sous-activité
     * 
     * @return  le type d'affichage
     */
    function retAffichageEtudiant($v_iIdSousActiv=NULL)
    {
        if ($v_iIdSousActiv == NULL)
            $iIdSousActivCourante = $this->retId();
        else
            $iIdSousActivCourante = $v_iIdSousActiv;

        $v_sTypeAffichageEtudiant = $this->oEnregBdd->AffichageEtudiant;

        if (!$v_sTypeAffichageEtudiant)
        {
            $this->defAffichageEtudiant("popup");
            $v_sTypeAffichageEtudiant = "popup";
        }
        return $v_sTypeAffichageEtudiant;
    }
    //@}

    /**
     * Ajoute ou met à jour le type d'affichage des Sous-Activités si celles ci sont des formulaires
     * 
     * @param  v_sTypeAffichage     le type d'affichage : inline ou popup (défaut)
     */
    function defAffichageEtudiant($v_sTypeAffichage)
    {
        $v_iIdSousActiv = $this->retId();
        if ($v_iIdSousActiv < 1)
            return FALSE;

        $this->MàJ_OptionsFormulaire("AffichageEtudiant", $v_sTypeAffichage);
    }
    //@}
}

?>
