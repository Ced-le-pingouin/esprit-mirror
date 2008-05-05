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
	plugins : "save,advhr,advlink,emotions,insertdatetime,zoom,searchreplace,contextmenu,directionality,paste"
		+(tableauDeBord?",tableaubord":""),

	theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,paste,pasteword,separator,search,replace,separator",
	theme_advanced_buttons2_add : "separator,ltr,rtl,separator,"
		+(tableauDeBord?"tableaubordi,tableauborde,separator,":"")
		+"iespell,hr,removeformat,sub,sup,charmap,visualaidseparator,print",
	theme_advanced_disable : "image",
	theme_advanced_buttons3: "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",

	paste_create_paragraphs : true,
	paste_create_linebreaks : true,
	paste_remove_styles: false,
	paste_remove_spans: false,
	paste_use_dialog : false,
	paste_auto_cleanup_on_paste : true,
	paste_convert_middot_lists : true,
	paste_unindented_list_class : "unindentedList",
	paste_convert_headers_to_strong : false,
	//paste_insert_word_content_callback : "convertWord",


	plugin_insertdate_dateFormat : "%Y-%m-%d",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	mode : Mode,
	elements : Elements,
	setupcontent_callback : contentCallback,
	onchange_callback : onchangeCallback,
	language : "fr",
	docs_language : "fr"
});
}
