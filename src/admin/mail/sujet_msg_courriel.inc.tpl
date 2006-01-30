<!--
  -- sujet_msg_courriel.inc.tpl
  --
  -- Date de cr�ation .......: 04/02/2005
  -- Derni�re modification ..: 24/02/2005
  -- Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
  --
  -- Unit� de Technologie de l'Education
  -- 18, Place du Parc
  -- 7000 MONS
  -->
[BLOCK_ENVOI_COURRIEL+]
[ARRAY_ENDROIT+]Menu principal,Zone de cours "{rubrique.nom}",Forum "{forum.nom}",Collecticiel "{sousactivite.nom}",Formulaire "{sousactivite.nom}"[ARRAY_ENDROIT-]
[VAR_SUJET_COURRIEL+]Envoi courriel ({plateforme.nom} - {formation.nom})[VAR_SUJET_COURRIEL-]
[VAR_MESSAGE_COURRIEL+]
********************************************************************
***** Courriel envoy� depuis la plateforme {plateforme.nom}
*****
***** Par : {personne.nom} {personne.prenom} ({personne.pseudo})
***** Formation : {formation.nom}
***** Cours : {module.nom}
***** Niveau : {plateforme.niveau}
********************************************************************
[VAR_MESSAGE_COURRIEL-]
[BLOCK_ENVOI_COURRIEL-]

[BLOCK_COPIE_MESSAGE_FORUM+]
[VAR_ENDROIT+]Forum : {forum.nom}[VAR_ENDROIT-]
[VAR_SUJET_COURRIEL+]Copie message forum : {forum.nom} ({formation.nom})[VAR_SUJET_COURRIEL-]
[VAR_MESSAGE_COURRIEL+]
********************************************************************
***** Copie d'un message d�pos� sur un des forums de la plateforme {plateforme.nom}
*****
***** Par : {personne.nom} {personne.prenom} ({personne.pseudo})
***** Formation : {formation.nom}
***** Cours : {module.nom}
***** Forum : {forum.nom}
*****
***** NE PAS r�pondre � ce courriel, il a �t� envoy� depuis la plateforme {plateforme.nom}.
***** Pour r�pondre, connectez-vous � {plateforme.nom} ({plateforme.url}) et
***** d�posez un message sur le forum {forum.nom}.
********************************************************************
[VAR_MESSAGE_COURRIEL-]
[BLOCK_COPIE_MESSAGE_FORUM-]

