<?php

$url_sTitreGlossaire = $HTTP_GET_VARS["glossaire_titre"];

$oProjet = new CProjet();

$oProjet->oFormationCourante->ajouterGlossaire($url_sTitreGlossaire,$oProjet->oUtilisateur->retId());

$oProjet->terminer();
?>