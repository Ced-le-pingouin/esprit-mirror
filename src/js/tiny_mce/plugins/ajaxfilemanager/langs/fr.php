<?php
    /**
     * language pack
     * @author dovik
     * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
     * @link www.phpletter.com
     * @since 26/oct/2007
     *
     */

    define('DATE_TIME_FORMAT', "d/M/Y H:i:s");
	//Common
	//Menu
	
	
	
	
	define('MENU_SELECT', 'S&eacute;lectionner');
	define('MENU_DOWNLOAD', 'T&eacute;l&eacute;charger');
	define('MENU_PREVIEW', 'Aper&ccedil;u');
	define('MENU_RENAME', 'Renommer');
	define('MENU_EDIT', '&Eacute;diter');
	define('MENU_CUT', 'Couper');
	define('MENU_COPY', 'Copier');
	define('MENU_DELETE', 'Supprimer');
	define('MENU_PLAY', 'Jouer');
	define('MENU_PASTE', 'Coller');
    //Label
        //Top Action
	define('LBL_ACTION_REFRESH', "Rafra&icirc;chir");
	define('LBL_ACTION_DELETE', "Supprimer");
	define('LBL_ACTION_CUT', "Couper");
	define('LBL_ACTION_COPY', "Copier");
	define('LBL_ACTION_PASTE', "Coller");
	define('LBL_ACTION_CLOSE', "Fermer");
	define('LBL_ACTION_SELECT_ALL', 'Tout S&eacute;lectionner');
        //File Listing
    define('LBL_NAME', "Nom");
    define('LBL_SIZE', "Taille");
    define('LBL_MODIFIED', "Modifi&eacute; le");
        //File Information
    define('LBL_FILE_INFO', "Information sur le fichier :");
    define('LBL_FILE_NAME', "Nom :");
    define('LBL_FILE_OWNER', "Propri&eacute;taire :");
    define('LBL_FILE_CREATED', "D&eacute;pos&eacute; le :");
    define('LBL_FILE_MODIFIED', "Modifi&eacute; le :");
    define('LBL_FILE_SIZE', "Taille :");
    define('LBL_FILE_TYPE', "Type :");
    define('LBL_FILE_WRITABLE', "Modifiable ?");
    define('LBL_FILE_READABLE', "Lisible ?");
        //Folder Information
    define('LBL_FOLDER_INFO', "Information du dossier");
    define('LBL_FOLDER_PATH', "Dossier :");
    define('LBL_CURRENT_FOLDER_PATH', 'Chemin actuel:');
    define('LBL_FOLDER_CREATED', "Cr&eacute;&eacute; Le :");
    define('LBL_FOLDER_MODIFIED', "Modifi&eacute; Le :");
    define('LBL_FOLDER_SUDDIR', "Sous-dossiers :");
    define('LBL_FOLDER_FIELS', "Fichiers :");
    define('LBL_FOLDER_WRITABLE', "Modifiable ?");
    define('LBL_FOLDER_READABLE', "Lisible ?");
    define('LBL_FOLDER_ROOT', 'Racine');
        //Preview
    define('LBL_PREVIEW', "Aper&ccedil;u");
    define('LBL_CLICK_PREVIEW', "Cliquer ici pour avoir un aper&ccedil;u.");
    //Buttons
    define('LBL_BTN_SELECT', "Choisir");
    define('LBL_BTN_CANCEL', "Annuler");
    define('LBL_BTN_UPLOAD', "D&eacute;poser");
    define('LBL_BTN_CREATE', "Cr&eacute;er");
    define('LBL_BTN_CLOSE', "Fermer");
    define('LBL_BTN_NEW_FOLDER', "Nouveau Dossier");
    define('LBL_BTN_NEW_FILE', 'Nouveau fichier');
    define('LBL_BTN_EDIT_IMAGE', 'Modifier');
    define('LBL_BTN_VIEW', 'Type de Vue');
    define('LBL_BTN_VIEW_TEXT', 'Texte');
    define('LBL_BTN_VIEW_DETAILS', 'D&eacute;tails');
    define('LBL_BTN_VIEW_THUMBNAIL', 'Miniatures');
    define('LBL_BTN_VIEW_OPTIONS', 'Vue :');
    //pagination
    define('PAGINATION_NEXT', 'Suivant');
    define('PAGINATION_PREVIOUS', 'Pr&eacute;c&eacute;dent');
    define('PAGINATION_LAST', 'Fin');
    define('PAGINATION_FIRST', 'D&eacute;but');
    define('PAGINATION_ITEMS_PER_PAGE', 'Afficher %s &eacute;l&eacute;ments par page');
    define('PAGINATION_GO_PARENT', 'Dossier Parent');
    //System
    define('SYS_DISABLED', 'Permission refus\u00E9e: Le syst\u00E8me est d\u00E9sactiv\u00E9.');
    	
    //Cut
    define('ERR_NOT_DOC_SELECTED_FOR_CUT', "Aucun document(s) selectionn&eacute; pour couper.");
    //Copy
    define('ERR_NOT_DOC_SELECTED_FOR_COPY', "Aucun document(s) selectionn&eacute; pour copier.");
    //Paste
    define('ERR_NOT_DOC_SELECTED_FOR_PASTE', "Aucun document(s) selectionn&eacute; pour coller.");
    define('WARNING_CUT_PASTE', "Voulez-vous vraiment d&eacute;placer les documents selectionn&eacute;s dans le dossier courant ?");
    define('WARNING_COPY_PASTE', "Voulez-vous vraiment copier les documents selectionn&eacute;s dans le dossier courant ?");
	define('ERR_NOT_DEST_FOLDER_SPECIFIED', 'Aucun dossier cible sp&eacute;cifi&eacute;.');
	define('ERR_DEST_FOLDER_NOT_FOUND', 'Aucun dossier cible trouv&eacute;.');
define('ERR_DEST_FOLDER_NOT_ALLOWED', 'Vous n\'&ecirc;tes pas autoris&eacute; &agrave; d&eacute;placer des fichiers dans ce dossier');
	define('ERR_UNABLE_TO_MOVE_TO_SAME_DEST', 'Impossible de d&eacute;placer ce fichier (%s): Le dossier source est identique au dossier cible.');
	define('ERR_UNABLE_TO_MOVE_NOT_FOUND', 'Impossible de d&eacute;placer ce fichier (%s): Le fichier d\'origine n\'existe pas.');
	define('ERR_UNABLE_TO_MOVE_NOT_ALLOWED', 'Impossible de d&eacute;placer ce fichier (%s): Acc&egrave; refus&eacute; au fichier d\'origine.');
 
	define('ERR_NOT_FILES_PASTED', 'Aucun fichier coll&eacute;.');

	//Search
	define('LBL_SEARCH', 'Rechercher');
	define('LBL_SEARCH_NAME', 'Nom de Fichier (Int&eacute;gral ou Partiel) :');
	define('LBL_SEARCH_FOLDER', 'Chercher dans :');
	define('LBL_SEARCH_QUICK', 'Recherche Rapide');
	define('LBL_SEARCH_MTIME', 'Date de Modification (P&eacute;riode) :');
	define('LBL_SEARCH_SIZE', 'Taille du Fichier :');
	define('LBL_SEARCH_ADV_OPTIONS', 'Options Avanc&eacute;es');
	define('LBL_SEARCH_FILE_TYPES', 'Types de Fichiers :');
	define('SEARCH_TYPE_EXE', 'Application');
	
	define('SEARCH_TYPE_IMG', 'Image');
	define('SEARCH_TYPE_ARCHIVE', 'Archive');
	define('SEARCH_TYPE_HTML', 'HTML');
	define('SEARCH_TYPE_VIDEO', 'Vid&eacute;o');
	define('SEARCH_TYPE_MOVIE', 'Film');
	define('SEARCH_TYPE_MUSIC', 'Musique');
	define('SEARCH_TYPE_FLASH', 'Flash');
	define('SEARCH_TYPE_PPT', 'PowerPoint');
	define('SEARCH_TYPE_DOC', 'Document');
	define('SEARCH_TYPE_WORD', 'Word');
	define('SEARCH_TYPE_PDF', 'PDF');
	define('SEARCH_TYPE_EXCEL', 'Excel');
	define('SEARCH_TYPE_TEXT', 'Texte');
	define('SEARCH_TYPE_UNKNOWN', 'Inconnu');
	define('SEARCH_TYPE_XML', 'XML');
	define('SEARCH_ALL_FILE_TYPES', 'Tous Types de Fichiers');
	define('LBL_SEARCH_RECURSIVELY', 'Recherche R&eacute;cursive :');
	define('LBL_RECURSIVELY_YES', 'Oui');
	define('LBL_RECURSIVELY_NO', 'Non');
	define('BTN_SEARCH', 'Rechercher');
	//thickbox
	define('THICKBOX_NEXT', 'Suivant&gt;');
	define('THICKBOX_PREVIOUS', '&lt;Pr&eacute;c&eacute;dent');
	define('THICKBOX_CLOSE', 'Fermer');
	//Calendar
	define('CALENDAR_CLOSE', 'Fermer');
	define('CALENDAR_CLEAR', 'Effacer');
	define('CALENDAR_PREVIOUS', '&lt;Pr&eacute;c&eacute;dent');
	define('CALENDAR_NEXT', 'Suivant&gt;');
	define('CALENDAR_CURRENT', 'Aujourd\\\'hui');
	define('CALENDAR_MON', 'Lun');
	define('CALENDAR_TUE', 'Mar');
	define('CALENDAR_WED', 'Mer');
	define('CALENDAR_THU', 'Jeu');
	define('CALENDAR_FRI', 'Ven');
	define('CALENDAR_SAT', 'Sam');
	define('CALENDAR_SUN', 'Dim');
	define('CALENDAR_JAN', 'Jan');
	define('CALENDAR_FEB', 'F&eacute;v');
	define('CALENDAR_MAR', 'Mar');
	define('CALENDAR_APR', 'Avr');
	define('CALENDAR_MAY', 'Mai');
	define('CALENDAR_JUN', 'Juin');
	define('CALENDAR_JUL', 'Juil');
	define('CALENDAR_AUG', 'Ao&ucirc;');
	define('CALENDAR_SEP', 'Sep');
	define('CALENDAR_OCT', 'Oct');
	define('CALENDAR_NOV', 'Nov');
	define('CALENDAR_DEC', 'D&eacute;c');
    //ERROR MESSAGES
        //deletion
    define('ERR_NOT_FILE_SELECTED', "Il faut choisir un fichier.");
    define('ERR_NOT_DOC_SELECTED', "Aucun document(s) selectionn&eacute; pour la suppression.");
    define('ERR_DELTED_FAILED', "Impossible de supprimer le(s) document(s) selectionn&eacute;.");
    define('ERR_FOLDER_PATH_NOT_ALLOWED', "Le chemin du dossier n'est pas autoris&eacute;.");
        //class manager
    define('ERR_FOLDER_NOT_FOUND', "Impossible de trouver le dossier sp&eacute;cifi&eacute; : ");
        //rename
    define('ERR_RENAME_FORMAT', "Il faut saisir un nom qui contient uniquement des lettres, chiffres, tirets et tirets-bas.");
    define('ERR_RENAME_EXISTS', "Il faut saisir un nom qui n'est pas d&eacute;j&agrave; pris dans ce dossier.");
    define('ERR_RENAME_FILE_NOT_EXISTS', "Le fichier/dossier n'existe pas.");
    define('ERR_RENAME_FAILED', "Impossible de le renommer, merci de recommencer.");
    define('ERR_RENAME_EMPTY', "Il faut pr&eacute;ciser un nom.");
    define('ERR_NO_CHANGES_MADE', "Aucun changement n'a &eacute;t&eacute effectu&eacute;.");
    define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', "Vous n'&ecirc;tes pas autoris&eacute; &agrave; changer de la sorte l'extension du fichier.");
        //folder creation
    define('ERR_FOLDER_FORMAT', "Il faut saisir un nom qui contient uniquement des lettres, chiffres, tirets et tirets-bas.");
    define('ERR_FOLDER_EXISTS', "Il faut saisir un nom qui n\'est pas pris dans ce dossier.");
    define('ERR_FOLDER_CREATION_FAILED', "Impossible de cr\u00E9er un dossier, merci de recommencer.");
    define('ERR_FOLDER_NAME_EMPTY', "Il faut pr\u00E9ciser un nom.");
	define('FOLDER_FORM_TITLE', 'Cr&eacute;er un Dossier');
	define('FOLDER_NEW_INFO', 'Un r&eacute;pertoire &agrave; votre nom va &ecirc;tre cr&eacute;&eacute;.');
	define('FOLDER_LBL_TITLE', 'Nom :');
	define('FOLDER_LBL_CREATE', 'Cr&eacute;er le Dossier');
	//New File
	define('NEW_FILE_FORM_TITLE', 'Cr&eacute;er un Fichier');
	define('NEW_FILE_LBL_TITLE', 'Nom :');
	define('NEW_FILE_CREATE', 'Cr&eacute;er le Fichier');
        //file upload
    define('ERR_FILE_NAME_FORMAT', "Il faut saisir un nom qui contient uniquement des lettres, chiffres, espaces, tirets et tirets-bas.");
    define('ERR_FILE_NOT_UPLOADED', "Aucun fichier n'a \u00E9t\u00E9 selectionn\u00E9 pour \u00EAtre envoy\u00E9.");
    define('ERR_FILE_TYPE_NOT_ALLOWED', "Vous n\'&ecirc;tes pas autoris&eacute;s &agrave; d&eacute;poser ce type de fichier.");
    define('ERR_FILE_NOT_IMG', "Ceci n\'est pas une image");
    define('ERR_FILE_NOT_MEDIA', "Ceci n\'est pas un m\u00E9dia valide. \\nAudio : *.mp3 \\nVid\u00E9o : *.swf ou *.flv");
    define('ERR_FILE_MOVE_FAILED', "Le d\u00E9placement du fichier a \u00E9chou\u00E9.");
    define('ERR_FILE_NOT_AVAILABLE', "Le fichier est indisponible.");
    define('ERROR_FILE_TOO_BID', "Le fichier est trop lourd. (max : %s)");
	define('FILE_FORM_TITLE', 'D&eacute;poser un fichier sur le serveur');
	define('FILE_LABEL_SELECT', 'Fichier :');
	define('FILE_LBL_MORE', 'D&eacute;poser un Autre fichier');
	define('FILE_CANCEL_UPLOAD', 'Annuler l\'envoi de fichiers');
	define('FILE_LBL_UPLOAD', 'D&eacute;poser');
	define('FILE_DISCLAIMER', '<p>Ce fichier sera d&eacute;pos&eacute; &agrave; votre nom et pourra &ecirc;tre utilis&eacute; par<br />d\'autres enseignants, dans d\'autres cours !</p><p>Assurez-vous d\'en avoir les droits d\'utilisation.</p>');
	define('MEDIA_UPLOAD_TIP', 'Les types de m&eacute;dias autoris&eacute;s : <br />*.swf et *.flv pour la vid&eacute;o, <br />*.mp3 pour l\'audio.');
	define('IMAGE_UPLOAD_TIP', 'Les types d\'images autoris&eacute;s : <br />*.png, *.gif et *.jpg');
    //file download
    define('ERR_DOWNLOAD_FILE_NOT_FOUND', "Aucun fichier selectionn\u00E9 pour \u00EAtre t\u00E9l\u00E9charg\u00E9.");
    	//Rename
	define('RENAME_FORM_TITLE', 'Renommer');
	define('RENAME_NEW_NAME', 'Nouveau Nom');
	define('RENAME_LBL_RENAME', 'Renommer');


    //Tips
    define('TIP_FOLDER_GO_DOWN', "Cliquer pour aller dans ce dossier ...");
    define('TIP_DOC_RENAME', "Double cliquer pour modifier ...");
    define('TIP_FOLDER_GO_UP', "Cliquer pour aller au dossier parent...");
    define('TIP_SELECT_ALL', "Tout selectionner");
    define('TIP_UNSELECT_ALL', "Tout d&eacute;selectionner");
    //WARNING
    define('WARNING_DELETE', "Voulez-vous vraiment effacer les fichiers selectionn\u00E9s.");
    define('WARNING_IMAGE_EDIT', "Merci de choisir une image &agrave; modifier.");
    define('WARNING_NOT_FILE_EDIT', "Merci de choisir un fichier &agrave; modifier.");
    define('WARING_WINDOW_CLOSE', "Voulez-vous vraiment fermer la fen&ecirc;tre ?");
    //Preview
    define('PREVIEW_NOT_PREVIEW', "Aucun aper&ccedil;u disponible.");
    define('PREVIEW_OPEN_FAILED', "Impossible d'ouvrir le fichier.");
    define('PREVIEW_IMAGE_LOAD_FAILED', "Impossible de charger l'image");

    //Login
    define('LOGIN_PAGE_TITLE', "Ajax File Manager : Formulaire d'authentification");
    define('LOGIN_FORM_TITLE', "Formulaire d'authentification");
    define('LOGIN_USERNAME', "Utilisateur :");
    define('LOGIN_PASSWORD', "Mot de passe :");
    define('LOGIN_FAILED', "Utilisateur/Mot de passe erron&eacute;.");
    
    
    //88888888888   Below for Image Editor   888888888888888888888
        //Warning 
        define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', "L\\'image n\\'a pas &eacute;t&eacute; modifi&eacute;e.");
        
        //General
        define('IMG_GEN_IMG_NOT_EXISTS', "L'image n'existe pas");
        define('IMG_WARNING_LOST_CHANAGES', "Toutes les modifications qui n\\'ont pas &eacute;t&eacute; sauvegard&eacute;es seront perdues, voulez-vous vraiment continuer ?");
        define('IMG_WARNING_REST', "Toutes les modifications qui n\\'ont pas &eacute;t&eacute; sauvegard&eacute;es seront perdues, voulez-vous vraiment remettre &agrave; z&eacute;ro ?");
        define('IMG_WARNING_EMPTY_RESET', "L\\'image n\\'a pas encore &eacute;t&eacute; modifi&eacute;e");
        define('IMG_WARING_WIN_CLOSE', "Voulez-vous vraiment fermer la fen&ecirc;tre ?");
		define('IMG_WARNING_UNDO', 'Voulez-vous vraiment restaurer l\\\'image dans son &eacute;tat pr&eacute;c&eacute;dent ?');
        define('IMG_WARING_FLIP_H', "Voulez-vous vraiment basculer l\\'image horizontalement ?");
        define('IMG_WARING_FLIP_V', "Voulez-vous vraiment basculer l\\'image verticalement ?");
        define('IMG_INFO', "Information sur l'image");
        
        //Mode
            define('IMG_MODE_RESIZE', "Redimensionner :");
            define('IMG_MODE_CROP', "D&eacute;couper :");
            define('IMG_MODE_ROTATE', "Rotation :");
            define('IMG_MODE_FLIP', "Basculer :");
        //Button
        
            define('IMG_BTN_ROTATE_LEFT', "90&deg; vers la gauche");
            define('IMG_BTN_ROTATE_RIGHT', "90&deg; vers la droite");
            define('IMG_BTN_FLIP_H', "Miroir Horizontal");
            define('IMG_BTN_FLIP_V', "Miroir Vertical");
            define('IMG_BTN_RESET', "Remise &agrave; z&eacute;ro");
            define('IMG_BTN_UNDO', "D&eacute;faire");
            define('IMG_BTN_SAVE', "Sauvegarder");
            define('IMG_BTN_CLOSE', "Fermer");
            define('IMG_BTN_SAVE_AS', "Sauvegarder sous");
            define('IMG_BTN_CANCEL', "Annuler");
        //Checkbox
            define('IMG_CHECKBOX_CONSTRAINT', "Contrainte ?");
        //Label
            define('IMG_LBL_WIDTH', "Largeur :");
            define('IMG_LBL_HEIGHT', "Hauteur :");
            define('IMG_LBL_X', "X :");
            define('IMG_LBL_Y', "Y :");
            define('IMG_LBL_RATIO', "Ratio :");
            define('IMG_LBL_ANGLE', "Angle :");
            define('IMG_LBL_NEW_NAME', "Nouveau nom :");
            define('IMG_LBL_SAVE_AS', "Sauvergarder sous");
            define('IMG_LBL_SAVE_TO', "Sauvegarder dans :");
            define('IMG_LBL_ROOT_FOLDER', "Dossier racine");
        //Editor
        //Save as 
        define('IMG_NEW_NAME_COMMENTS', "Ne pas mettre l'extension de l'image.");
        define('IMG_SAVE_AS_ERR_NAME_INVALID', "Il faut saisir un nom qui contient uniquement des lettres, chiffres, espaces, tirets et tirets-bas.");
        define('IMG_SAVE_AS_NOT_FOLDER_SELECTED', "Il faut pr&eacute;ciser le dossier de destination.");
        define('IMG_SAVE_AS_FOLDER_NOT_FOUND', "Le dossier de destination existe d&eacute;j&agrave;.");
        define('IMG_SAVE_AS_NEW_IMAGE_EXISTS', "Des images portent le m&ecirc;me nom.");

        //Save
        define('IMG_SAVE_EMPTY_PATH', "Le chemin de l'image est vide.");
        define('IMG_SAVE_NOT_EXISTS', "L\\'image n'existe pas.");
        define('IMG_SAVE_PATH_DISALLOWED', "Vous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der &agrave; ce fichier.");
        define('IMG_SAVE_UNKNOWN_MODE', "Mode inattendu d'op&eacute;ration d'image");
        define('IMG_SAVE_RESIZE_FAILED', "&Eacute;chec du redimensionnement de l'image.");
        define('IMG_SAVE_CROP_FAILED', "&Eacute;chec du d&eacute;coupage de l'image.");
        define('IMG_SAVE_FAILED', "&Eacute;chec de la sauvegarde de l'image.");
        define('IMG_SAVE_BACKUP_FAILED', "Impossible de sauvegarder l'image originale.");
        define('IMG_SAVE_ROTATE_FAILED', "Impossible d'effectuer la rotation de l'image.");
        define('IMG_SAVE_FLIP_FAILED', "Impossible de basculer l'image.");
        define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', "Impossible d'ouvrir l'image de session.");
        define('IMG_SAVE_IMG_OPEN_FAILED', "Impossible d'ouvrir l'image");
        
        
        //UNDO
        define('IMG_UNDO_NO_HISTORY_AVAIALBE', "Aucun historique d'annulation.");
        define('IMG_UNDO_COPY_FAILED', "Impossible de restaurer l'image.");
        define('IMG_UNDO_DEL_FAILED', "Impossible de supprimer l'image de session");
    
    //88888888888   Above for Image Editor   888888888888888888888
    
    //88888888888   Session   888888888888888888888
        define('SESSION_PERSONAL_DIR_NOT_FOUND', "Impossible de trouver le dossier d&eacute;di&eacute; qui aurait d&ucirc; &ecirc;tre cr&eacute;&eacute; dans le dossier session");
        define('SESSION_COUNTER_FILE_CREATE_FAILED', "Impossible d'ouvrir le fichier de comptage de session.");
        define('SESSION_COUNTER_FILE_WRITE_FAILED', "Impossible d'&eacute;crire dans le fichier de comptage de session.");
    //88888888888   Session   888888888888888888888
    
    //88888888888   Below for Text Editor   888888888888888888888
        define('TXT_FILE_NOT_FOUND', "Le fichier n'a pas &eacute;t&eacute; trouv&eacute;.");
        define('TXT_EXT_NOT_SELECTED', "Merci de choisir une extension au fichier.");
        define('TXT_DEST_FOLDER_NOT_SELECTED', "Merci de choisir un dossier de destination.");
        define('TXT_UNKNOWN_REQUEST', "Requ&ecirc;te inconnue.");
        define('TXT_DISALLOWED_EXT', "Vous n'&ecirc;tes pas autoris&eacute; &agrave; modifier/ajouter ce type de fichier.");
        define('TXT_FILE_EXIST', "Ce fichier existe d&eacute;j&agrave;.");
        define('TXT_FILE_NOT_EXIST', "Ce fichier n'existe pas.");
        define('TXT_CREATE_FAILED', "&Eacute;chec de la cr&eacute;ation du fichier.");
        define('TXT_CONTENT_WRITE_FAILED', "&Eacute;chec de l'&eacute;criture du contenu dans le fichier.");
        define('TXT_FILE_OPEN_FAILED', "&Eacute;chec de l'ouverture du fichier.");
        define('TXT_CONTENT_UPDATE_FAILED', "&Eacute;chec de la mise &agrave; jour du contenu du fichier.");
        define('TXT_SAVE_AS_ERR_NAME_INVALID', "Il faut saisir un nom qui contient uniquement des lettres, chiffres, espaces, tirets et tirets-bas.");
    //88888888888   Above for Text Editor   888888888888888888888
    
    
?>
