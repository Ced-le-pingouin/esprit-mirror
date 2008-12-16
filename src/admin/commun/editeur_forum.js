function initEditeur( Mode, Elements, tableauDeBord ) {
	var contentCallback, onchangeCallback;

	contentCallback = "mySetContent";

	if ('function' == typeof editeurOnChangeHandler) {
		onchangeCallback = "editeurOnChangeHandler";
	} else {
		onchangeCallback = "";
	}

tinyMCE.init({
	theme : "advanced",
	plugins : "advhr,advlink,contextmenu,directionality,insertdatetime,paste,safari,save,searchreplace",
	
	theme_advanced_buttons1_add : "forecolor,backcolor,",
	theme_advanced_buttons2_add_before: "cut,copy,pasteword,separator",
	theme_advanced_buttons2_add : "separator,ltr,rtl,separator,"
		+"iespell,hr,removeformat,sub,sup,charmap",
	theme_advanced_disable : "formatselect,anchor",
	theme_advanced_buttons3: "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing_use_cookie : false,
	theme_advanced_resizing : false,

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
	
	plugin_insertdate_dateFormat : "%d-%m-%Y",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	//invalid_elements : "p[style]",
	mode : Mode,
	elements : Elements,
	setupcontent_callback : contentCallback,
	onchange_callback : onchangeCallback,
	//save_callback : "nettoyage",
	language : "fr",
	docs_language : "fr"
});
}