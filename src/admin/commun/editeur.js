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

	paste_create_paragraphs : true,
	paste_create_linebreaks : false,
	paste_remove_styles: false,
	paste_remove_spans: false,
	paste_use_dialog : true,
	paste_auto_cleanup_on_paste : false,
	paste_convert_middot_lists : true,
	paste_unindented_list_class : "unindentedList",
	paste_convert_headers_to_strong : false,
	paste_strip_class_attributes : 'mso',
	paste_insert_word_content_callback : "convertWord",
	
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

/* 	nettoyage des éléments du tableau au moment de la validation dans l'éditeur
	ce qui permet d'enlever les <p...>&nbsp;</p> du tableau ajoutés après insertion par 'pasteword'
	et évite les pertes des cadres du tableau causées par les paragraphes.
*/
function nettoyage(element_id, html, body) {
	var recherche_p = /<td[^>]*>([^<]*(<br \/>[^<]*)*<p\s?[^>]*>(<[^>]*>)*(\&nbsp\;)*((<\/[^>]*>)*)<\/p>\s?)*<\/td>/i;
	bool = recherche_p.test(html);
	while (bool)
	{
		html = html.replace(/<p\s?[^>]*>(<[^>]*>)*(\&nbsp\;)*((<\/[^>]*>)*)<\/p>/i, "<br />");
		bool = recherche_p.test(html);
	}
	return html;
}

function convertWord(type, content) {
	content = content.replace(/-moz-use-text-color/gi, ""); // on enlève les balises spéciales créées par Mozilla/FireFox
	content = content.replace(/( line-height: )[a-z]*(;){1,}/gi, "");
	content = content.replace(/\s?mso-[^\"]*/gi, ""); // on enlève les balises spéciales créées par IE
	content = content.replace(/(<!--*[^>]*>\s*.*<![^>]*>)*/gi, ""); // supprime les commentaires pouvant être ajoutés
	content = content.replace(/(<table\s*.*)(border-collapse.\s[^;]*;)([^>]*>)/gi, "$1$3");
	
	content = content.replace(/(<a href[^>]*)/gi, "$1 class=\"lien_ext\""); // transformation des liens : on les affichera de base en 'liens externes'.
	
	recherche_td = content.match(/(td).+(border).{0,7}(:)[^;]*;\s?/gi); // recherche des <td...></td>
	if (recherche_td)
	{
		for(i=0;i<recherche_td.length;++i)
		{
			content = content.replace(/(border).{0,7}(:)[^;]*;\s?/gi, ""); // enleve les style 'border-color'... dans le td
		}
	}
	recherche_tab = content.match(/(table).+(border).{0,7}(:)[a-z0-9\s]*(;\s?)/gi); // recherche des <table...> contenant un style border
	if (recherche_tab)
	{
		for(i=0;i<recherche_tab.length;++i)
		{
			content = content.replace(/(border).{0,}(:)[a-z0-9\s]*((;)|(;\s?))/gi, ""); // enleve les style 'border-color'... dans la table
		}
	}

	return content;
}
function ajaxfilemanager(field_name, url, type, win) {
	var ajaxfilemanagerurl = "../ajaxfilemanager/ajaxfilemanager.php";
	switch (type) {
		case "image":
			ajaxfilemanagerurl+="?path=../../../../depot/images"; // essayer avec +GLOBALS["rep_images"]
			break;
		case "media":
			ajaxfilemanagerurl+="?path=../../../../depot/medias"; // essayer avec +GLOBALS["rep_medias"];
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