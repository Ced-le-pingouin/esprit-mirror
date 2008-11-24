function recharger() {
parent.location = parent.location.pathname + (arguments.length > 0 ? arguments[0] : "");
}

function chat_archives(v_oThis) {
	var sUrl = v_oThis.href;
	var iLargeurFen = screen.width-30;
	var iHauteurFen = screen.height-50;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winChatArchives",iLargeurFen,iHauteurFen,sOptionsFenetre);
	win.focus();
}

function corbeille() {
	var sUrl = GLOBALS["admin"] + "concept/corbeille.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winCorbeille",640,480,sOptionsFenetre);
	win.focus();
}

function console() {
	var sUrl = GLOBALS["admin"] + "console/console-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winConsole",640,480,sOptionsFenetre);
	win.focus();
}

function eConcept() {
	var sUrl = GLOBALS["admin"] + "concept/econcept-index.php";
	var iLargeurFen = screen.width-30;
	var iHauteurFen = screen.height-50;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winEConcept",iLargeurFen,iHauteurFen,sOptionsFenetre);
	win.focus();
}

function formulaire() {
	var sUrl = GLOBALS["admin"] + "formulaire/formulaire_index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winFormulaire",780,580,sOptionsFenetre);
	win.focus();
}

function outils() {
	var sUrl = "outils-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winOutils",320,405,sOptionsFenetre);
	win.focus();
}

function multilingue() {
	var sUrl = GLOBALS["admin"] + "multilingue/ml_index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winMultilingue",780,580,sOptionsFenetre);
	win.focus();
}

function equipes() {
	var sUrl = GLOBALS["admin"] + "equipe/equipes-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winEquipes",765,575,sOptionsFenetre);
	win.focus();
}

function liste_equipes(v_iIdEquipe,v_sListeStatuts) {
	var sUrl = GLOBALS["admin"] + "equipe/liste_equipes-index.php"
		+ "?idStatuts=" + v_sListeStatuts
		+ "&idEquipes=" + v_iIdEquipe
		+ "&typeCourriel=courriel-unite";
	var winEquipesSA = PopupCenter(sUrl,"winEquipesSA",600,410,",toolbar=no,resizable=yes,scrollbars=no");
	winEquipesSA.focus();
	return false;
}

function changer_statut() {
	var sUrl = GLOBALS["admin"] + "personne/changer_statut-index.php";
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var win = PopupCenter(sUrl,"winChangerStatutUtilisateur",500,300,sOptionsFenetre);
	win.focus();
}

function transfert_form() {
	var sUrl = GLOBALS["admin"] + "utilitaires/transfert/transfert_form-index.php";
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var win = PopupCenter(sUrl,"WinTransfertFormations",500,300,sOptionsFenetre);
	win.focus();
}

function exporter_liste_personnes() {
	var sUrl = GLOBALS["admin"] + "personne/exporter-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"WinExporterPersonnes",640,480,sOptionsFenetre);
	win.focus();
}

function importer_liste_personnes() {
	var sUrl = GLOBALS["admin"] + "personne/import-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"WinImporterPersonnes",500,480,sOptionsFenetre);
	win.focus();
}

function enlever_personne(v_iIdForm) {
	var sUrl = GLOBALS["admin"] + "personne/enlever_personne-index.php"
		+ "?idform=" + v_iIdForm;
	var win = PopupCenter(sUrl,"WinEnleverPersonne",300,150,"");
	win.focus();
}

function choix_formation(v_sTitre) {
	var sUrl = GLOBALS["admin"] + "commun/choix_formation-index.php"
		+ (v_sTitre.length > 0 ? "?tp=" + v_sTitre : "");
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winChoixFormations",640,480,sOptionsFenetre);
	win.focus();
}

function gestion_utilisateur(v_iIdForm) {
	var sUrl = GLOBALS["admin"] + "associer/inscription-index.php"
		+ "?idform=" + v_iIdForm;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winInscriptions",870,630,sOptionsFenetre);
	win.location = sUrl; 	// NE PAS EFFACER CETTE LIGNE SVP
	win.focus();
}

function permissions() {
	var sUrl = GLOBALS["admin"] + "permis/permissions-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winPermissions",640,480,sOptionsFenetre);
	win.focus();
}

function profil(v_sParamUrl) {
	var sUrl = GLOBALS["admin"] + "personne/profil-index.php";
	
	if (typeof(v_sParamUrl) != "undefined")
		sUrl += v_sParamUrl;
	
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var win = PopupCenter(sUrl,"winProfil",640,500,sOptionsFenetre);
	win.focus();
}

function informations() {
	var sUrl = GLOBALS["admin"] + "plateforme/infos-index.php";
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var win = PopupCenter(sUrl,"wInformations",640,500,sOptionsFenetre);
	win.focus();
}

function ouvrir_fich_statut() {
	var sUrl = GLOBALS["admin"] + "permis/creer_fich_statut.php";
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var win = PopupCenter(sUrl,"winCreationStatuts",200,100,sOptionsFenetre);
	win.focus();
}

function composer_glossaire(v_iIdForm) {
	var sUrl = GLOBALS["admin"] + "glossaire/glossaire_composer-index.php"
		+ "?idForm=" + v_iIdForm;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winComposerGlossaire",780,580,sOptionsFenetre);
	win.focus();
}

function connexion() {
	var sParamsUrl = "";
	if (arguments.length == 1) sParamsUrl = "?idPers=" + arguments[0];
	var sUrl = GLOBALS["admin"] + "espion/connexion-index.php" + sParamsUrl;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"WinConnexion",780,580,sOptionsFenetre);
	win.focus();
}

function editeur(v_sNomFormulaire,v_sNomElementFormulaire,v_sNomFichierExporter) {
	var sUrl = GLOBALS["admin"] + "commun/editeur-index.php"
		+ "?formulaire=" + v_sNomFormulaire
		+ "&element=" + v_sNomElementFormulaire
		+ "&nfexport=" + v_sNomFichierExporter + ".txt";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var win = PopupCenter(sUrl,"winOutilsEditeur",750,480,sOptionsFenetre);
	win.focus();
}

function liste_inscrits() {
	var sUrl = GLOBALS["admin"] + "personne/liste_inscrits-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var win = PopupCenter(sUrl,"winListeInscrits",455,545,sOptionsFenetre);
	win.focus();
}

function liste_connectes() { top.frames["AWARENESS"].voir_liste_connectes(); }

function choix_courriel(v_sParamsUrl) {
	var sUrl = GLOBALS["admin"] + "mail/choix_courriel-index.php"
		+ (v_sParamsUrl ? v_sParamsUrl : "");
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winChoixCourriel",480,585,sOptionsFenetre);
	oWin.focus();
	return false;
}

function courriel(v_sParamsUrl) {
	var sUrl = GLOBALS["admin"] + "mail/mail-index.php"
		+ (v_sParamsUrl ? v_sParamsUrl : "");
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=yes";
	var oWin = PopupCenter(sUrl,"winCourriel",640,480,sOptionsFenetre);
	oWin.focus();
}

function dossiers() {
	var sUrl = GLOBALS["admin"] + "dossierforms/changer_dossier-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winDossiers",700,525,sOptionsFenetre,top.opener.top.frames["AWARENESS"]);
	oWin.focus();
}

function composer_dossiers_formations() {
	var sUrl = GLOBALS["admin"] + "dossierforms/dossier_formations-index.php";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winDossierFormations",700,525,sOptionsFenetre);
	oWin.focus();
}

function page_accueil() {
	var sUrl = GLOBALS["admin"] + "accueil/accueil-index.php";
	var sOptionsFenetre = ",status=no,resizable=no,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winAccueilLogin",700,440,sOptionsFenetre);
	oWin.focus();
}

function tableau_de_bord(v_oObj) {
	var sUrl = v_oObj.href;
	var iLargeurFen = screen.width-30;
	var iHauteurFen = screen.height-60;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winTableauDeBord",iLargeurFen,iHauteurFen,sOptionsFenetre,top.frames["AWARENESS"]);
	oWin.focus();
	return false;
}

function gestionnaire() {
	var sUrl = GLOBALS["gestionnaire"] + "/ajaxfilemanager.php?path=racine&mode=racine&editor=form";
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winAccueilLogin",950,600,sOptionsFenetre);
	oWin.focus();
}