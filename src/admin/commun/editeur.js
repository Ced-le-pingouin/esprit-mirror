function initEditeur( Mode, Elements, tableauDeBord ) {
	var contentCallback, onchangeCallback;
	if ('function' == typeof mySetContent) {
		contentCallback = "mySetContent";
	} else {
		contentCallback = "";
	}
	if ('function' == typeof editeurOnChangeHandler) {
		onchangeCallback = "editeurOnChangeHandler";
	} else {
		onchangeCallback = "";
	}
	
tinyMCE.init({
	theme : "advanced",
	plugins : "save,advhr,advimage,advlink,insertdatetime,searchreplace,contextmenu,directionality,paste,safari"
		+(tableauDeBord?",tableaubord":""),

	theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,pasteword,separator,search,replace,separator",
	theme_advanced_buttons2_add : "separator,ltr,rtl,separator,"
		+(tableauDeBord?"tableaubordi,tableauborde,separator,":"")
		+"iespell,hr,removeformat,sub,sup,charmap",
	theme_advanced_disable : "image",
	theme_advanced_buttons3: "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",

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
	convert_fonts_to_spans : false,
	fix_content_duplication : true,
	fix_list_elements : true,
	fix_table_elements : true,
	fix_nesting : true,
	preformatted : false,
	
	plugin_insertdate_dateFormat : "%Y-%m-%d",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	invalid_elements : "p[style]",
	mode : Mode,
	elements : Elements,
	setupcontent_callback : contentCallback,
	onchange_callback : onchangeCallback,
	language : "fr",
	docs_language : "fr"
});
}

function convertWord(type, content) {
	content = content.replace(/-moz-use-text-color/gi, ""); // on enlève les balises spéciales créées par Mozilla/FireFox
	content = content.replace(/( line-height: )[a-z]*(;){1,}/gi, "");
	content = content.replace(/\s?mso-[^\"]*/gi, ""); // on enlève les balises spéciales créées par IE
	
	// remplace les balises 'paragraphe' vide dans le tableau par <br/>
	var recherche_p = /<p\s?[^>]*>(<[^>]*>)*(\&nbsp\;)+((<\/[^>]*>)*)<\/p>/gi;
	recherche_p.exec(content);
	content = content.replace(recherche_p,"<br />");
	
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