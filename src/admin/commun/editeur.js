function initEditeur( Mode, Elements, tableauDeBord ) {
/*	var contentCallback, onchangeCallback;
	if ('function' == typeof mySetContent) {
		contentCallback = "mySetContent";
	} else {
		contentCallback = "";
	}
*/
	if ('function' == typeof editeurOnChangeHandler) {
		onchangeCallback = "editeurOnChangeHandler";
	} else {
		onchangeCallback = "";
	}

tinyMCE.init({
	theme : "advanced",
	plugins : "advhr,advimage,advlink,contextmenu,directionality,flvplayer,insertdatetime,paste,safari,save,searchreplace"
		+(tableauDeBord?",tableaubord":""),
	
	theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,pasteword,separator,search,replace,separator",
	theme_advanced_buttons2_add : "separator,ltr,rtl,separator,"
		+(tableauDeBord?"tableaubordi,tableauborde,separator,":"")
		+"iespell,hr,removeformat,sub,sup,charmap,separator,image,flvplayer",
	theme_advanced_disable : "styleselect",
	theme_advanced_buttons3: "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing_use_cookie : false,
	theme_advanced_resizing : true,

	paste_auto_cleanup_on_paste : true,
	paste_convert_middot_lists : true,
	paste_retain_style_properties : "font-size,color",
	paste_strip_class_attributes : 'mso',
	paste_remove_spans: false,
    paste_preprocess : function(pl, o) {
		// transformation des liens : on les affichera de base en 'liens externes'.
        o.content = o.content.replace(/(<a href[^>]*)/gi, "$1 class=\"lien_ext\"");
    },
	
	force_p_newlines : true,
	force_br_newlines : false,
	forced_root_block : 'p',
	convert_fonts_to_spans : true,
	font_size_style_values : "8pt,10pt,12pt,14pt,18pt,24pt,36pt",
	fix_content_duplication : true,
	fix_list_elements : true,
	fix_table_elements : true,
	fix_nesting : true,
	preformatted : false,
	convert_urls : false,
	apply_source_formatting : true,
	
	file_browser_callback : "ajaxfilemanager",
	
	plugin_insertdate_dateFormat : "%d-%m-%Y",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick|class|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	invalid_elements : "p[style]",
	mode : Mode,
	elements : Elements,
	//setupcontent_callback : contentCallback,
	onchange_callback : onchangeCallback,
	save_callback : "nettoyage",
	language : "fr",
	docs_language : "fr"
});
}

/* 	
 * ajout d'une classe aux paragraphe afin qu'ils n'aient plus de marges (haute et basse)
*/
function nettoyage(element_id, html, body) {
	var recherche_paragraph_simple = /<p>/gi;
	html = html.replace(recherche_paragraph_simple, '<p class="paragraph_editeur">');
	
	var rechercher_paragraphe_style = /<p( style="[^"]*")>/gi;
	html = html.replace(rechercher_paragraphe_style, '<p class="paragraph_editeur"$1>');

	return html;
}

function ajaxfilemanager(field_name, url, type, win) {
	var ajaxfilemanagerurl = "../ajaxfilemanager/ajaxfilemanager.php";
	switch (type) {
		case "image":
			ajaxfilemanagerurl+="?path=../../../../depot/images";
			break;
		case "media":
			ajaxfilemanagerurl+="?path=../../../../depot/medias";
			break;
		default:
			return false;
	}
	tinyMCE.activeEditor.windowManager.open({
		url: ajaxfilemanagerurl,
		width: 782,
		height: 440,
		inline : "yes",
		close_previous : "no"
		},{
			window : win,
			input : field_name
			});
}