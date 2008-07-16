tinyMCEPopup.requireLangPack();

var oldWidth, oldHeight, ed, url;

if (url = tinyMCEPopup.getParam("media_external_list_url"))
	document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');

function init() {
	var pl = "", f, val;
	var type = "flash", fe, i;

	ed = tinyMCEPopup.editor;

	tinyMCEPopup.resizeToInnerSize();
	f = document.forms[0]

	fe = ed.selection.getNode();
	if (/mceItem(Flash|FlashAudio|YoutubeVideo|GoogleVideo)/.test(ed.dom.getAttrib(fe, 'class'))) {
		pl = fe.title;

		switch (ed.dom.getAttrib(fe, 'class')) {
			case 'mceItemFlash':
				type = 'flash';
				break;
			case 'mceItemFlashAudio':
				type = 'mp3';
				break;
			case 'mceItemYoutubeVideo':
				type = 'youtube';
				break;
			case 'mceItemGoogleVideo':
				type = 'google';
				break;
		}

		document.forms[0].insert.value = ed.getLang('update', 'Insert', true); 
	}

	document.getElementById('filebrowsercontainer').innerHTML = getBrowserHTML('filebrowser','src','media','media');
	document.getElementById('backcolor_pickcontainer').innerHTML = getColorPickerHTML('backcolor_pick','backcolor');

	var html = getMediaListHTML('medialist','src','media','media');
	if (html == "")
		document.getElementById("linklistrow").style.display = 'none';
	else
		document.getElementById("linklistcontainer").innerHTML = html;

	// Resize some elements
	if (isVisible('filebrowser'))
		document.getElementById('src').style.width = '230px';

	// Setup form
	if (pl != "") {
		pl = tinyMCEPopup.editor.plugins.flvplayer._parse(pl);

		setBool(pl, 'flash', 'autostart');
		setBool(pl, 'flash', 'repeat');
		setBool(pl, 'flash', 'shownavigation');

		setStr(pl, null, 'src');
		setStr(pl, null, 'id');
		setStr(pl, null, 'name');
		setStr(pl, null, 'backcolor');
		setStr(pl, null, 'align');
		setStr(pl, null, 'width');
		setStr(pl, null, 'height');

		if ((val = ed.dom.getAttrib(fe, "width")) != "")
			pl.width = f.width.value = val;

		if ((val = ed.dom.getAttrib(fe, "height")) != "")
			pl.height = f.height.value = val;

		oldWidth = pl.width ? parseInt(pl.width) : 0;
		oldHeight = pl.height ? parseInt(pl.height) : 0;
	} else
		oldWidth = oldHeight = 0;

	selectByValue(f, 'media_type', type);
	changedType(type);
	updateColor('backcolor_pick', 'backcolor');

	TinyMCE_EditableSelects.init();
	generatePreview();
}

function insertMedia() {
	var fe, f = document.forms[0], h;

	tinyMCEPopup.restoreSelection();

	if (!AutoValidator.validate(f)) {
		alert(ed.getLang('invalid_data'));
		return false;
	}

	f.width.value = f.width.value == "" ? 200 : f.width.value;
	f.height.value = f.height.value == "" ? 150 : f.height.value;

	fe = ed.selection.getNode();
	if (fe != null && /mceItem(Flash|FlashAudio|YoutubeVideo|GoogleVideo)/.test(ed.dom.getAttrib(fe, 'class'))) {
		switch (f.media_type.options[f.media_type.selectedIndex].value) {
			case "flash":
				fe.className = "mceItemFlash";
				break;
			case "mp3":
				fe.className = "mceItemFlashAudio";
				f.height.value = 20;
				break;
			case "youtube":
				fe.className = "mceItemYoutubeVideo";
				break;
			case "google":
				fe.className = "mceItemGoogleVideo";
				break;
		}

		if (fe.width != f.width.value || fe.height != f.height.height)
			ed.execCommand('mceRepaint');

		fe.title = serializeParameters();
		fe.width = f.width.value;
		fe.height = f.height.value;
		fe.style.width = f.width.value + (f.width.value.indexOf('%') == -1 ? 'px' : '');
		fe.style.height = f.height.value + (f.height.value.indexOf('%') == -1 ? 'px' : '');
		fe.align = f.align.options[f.align.selectedIndex].value;
	} else {
		h = '<img src="' + tinyMCEPopup.getWindowArg("plugin_url") + '/img/trans.gif"' ;

		switch (f.media_type.options[f.media_type.selectedIndex].value) {
			case "flash":
				h += ' class="mceItemFlash"';
				break;
			case "mp3":
				h += ' class="mceItemFlashAudio"';
				f.height.value = 20;
				break;
			case "youtube":
				h += ' class="mceItemYoutubeVideo"';
				break;
			case "google":
				h += ' class="mceItemGoogleVideo"';
				break;
		}

		h += ' title="' + serializeParameters() + '"';
		h += ' width="' + f.width.value + '"';
		h += ' height="' + f.height.value + '"';
		h += ' align="' + f.align.options[f.align.selectedIndex].value + '"';

		h += ' />';

		ed.execCommand('mceInsertContent', false, h);
	}

	tinyMCEPopup.close();
}

function updatePreview() {
	var f = document.forms[0], type;

	f.width.value = f.width.value || '320';
	f.height.value = f.height.value || '240';

	type = getType(f.src.value);
	selectByValue(f, 'media_type', type);
	changedType(type);
	generatePreview();
}

function getMediaListHTML() {
	if (typeof(tinyMCEMediaList) != "undefined" && tinyMCEMediaList.length > 0) {
		var html = "";

		html += '<select id="linklist" name="linklist" style="width: 250px" onchange="this.form.src.value=this.options[this.selectedIndex].value;updatePreview();">';
		html += '<option value="">---</option>';

		for (var i=0; i<tinyMCEMediaList.length; i++)
			html += '<option value="' + tinyMCEMediaList[i][1] + '">' + tinyMCEMediaList[i][0] + '</option>';

		html += '</select>';

		return html;
	}

	return "";
}

function getType(v) {
	var fo, i, c, el, x, f = document.forms[0];

	fo = ed.getParam("media_types", "flash=swf,flv;mp3=mp3,mp4;qt=mov,qt,mpg,mpeg;wmp=avi,wmv,wm,asf,asx,wmx,wvx").split(';');

	// YouTube
	if (v.match(/watch\?v=(.+)(.*)/)) {
		//f.width.value = '425';
		//f.height.value = '350';
		f.src.value = v;
		return 'youtube';
	}

	// Google video
	if (v.match(/videoplay?docid=/)) {
		//f.width.value = '425';
		//f.height.value = '326';
		alert(v);
		f.src.value = 'http://video.google.com/googleplayer.swf?docId=' + v.substring('http://video.google.com/videoplay?docid='.length) + '&hl=en';
		return 'google';
	}
	
	for (i=0; i<fo.length; i++) {
		c = fo[i].split('=');

		el = c[1].split(',');
		for (x=0; x<el.length; x++)
		if (v.indexOf('.' + el[x]) != -1)
			return c[0];
	}

	return null;
}

function switchType(v) {
	var t = getType(v), d = document, f = d.forms[0];

	if (!t)
		return;

	selectByValue(d.forms[0], 'media_type', t);
	changedType(t);
	generatePreview();
}

function changedType(t) {
	var d = document;
	d.getElementById('flash_options').style.display = 'block';
	if (t == 'mp3') {
		d.getElementById('height').disabled = true;
		d.getElementById('height').value = 20;
	}
	else {
		d.getElementById('height').disabled = false;
		d.getElementById('height').value = '';
	}
}

function serializeParameters() {
	var d = document, f = d.forms[0], s = '';
	s += getBool('flash', 'autostart', true);
	s += getBool('flash', 'repeat', true);
	s += getBool('flash', 'shownavigation', true);
	s += getStr(null, 'id');
	s += getStr(null, 'name');
	s += getStr(null, 'src');
	s += getStr(null, 'align');
	s += getStr(null, 'backcolor');
	s += getStr(null, 'width');
	s += getStr(null, 'height');
	
	s = s.length > 0 ? s.substring(0, s.length - 1) : s;

	return s;
}

function setBool(pl, p, n) {
	if (typeof(pl[n]) == "undefined")
		return;

	document.forms[0].elements[p + "_" + n].checked = pl[n];
}

function setStr(pl, p, n) {
	var f = document.forms[0], e = f.elements[(p != null ? p + "_" +n : '') + n];

	if (typeof(pl[n]) == "undefined")
		return;

	if (e.type == "text")
		e.value = pl[n];
	else
		selectByValue(f, (p != null ? p + "_" : '') + n, pl[n]);
}

function getBool(p, n, d, tv, fv) {
	var v = document.forms[0].elements[p + "_" + n].checked;

	tv = typeof(tv) == 'undefined' ? 'true' : "'" + jsEncode(tv) + "'";
	fv = typeof(fv) == 'undefined' ? 'false' : "'" + jsEncode(fv) + "'";

	//return (v == d) ? '' : n + (v ? ':' + tv + ',' : ':' + fv + ',');
	return n + (v ? ':' + tv + ',' : ':' + fv + ',');
}

function getStr(p, n, d) {
	var e = document.forms[0].elements[(p != null ? p + "_" : "") + n];
	var v = e.type == "text" ? e.value : e.options[e.selectedIndex].value;

	return ((n == d || v == '') ? '' : n + ":'" + jsEncode(v) + "',");
}

function getInt(p, n, d) {
	var e = document.forms[0].elements[(p != null ? p + "_" : "") + n];
	var v = e.type == "text" ? e.value : e.options[e.selectedIndex].value;

	return ((n == d || v == '') ? '' : n + ":" + v.replace(/[^0-9]+/g, '') + ",");
}

function jsEncode(s) {
	s = s.replace(new RegExp('\\\\', 'g'), '\\\\');
	s = s.replace(new RegExp('"', 'g'), '\\"');
	s = s.replace(new RegExp("'", 'g'), "\\'");

	return s;
}

function generatePreview(c) {
	var f = document.forms[0], p = document.getElementById('nopreview'), h = '', cls, pl, n, type, codebase, wp, hp, nw, nh;
	var type_media = document.forms[0].elements['media_type'];
	type = type_media.options[type_media.selectedIndex].value;
	
	nw = parseInt(f.width.value);
	nh = parseInt(f.height.value);

	if (f.width.value != "" && f.height.value != "") {
		if (f.constrain.checked) {
			if (c == 'width' && oldWidth != 0) {
				wp = nw / oldWidth;
				nh = Math.round(wp * nh);
				f.height.value = nh;
			} else if (c == 'height' && oldHeight != 0) {
				hp = nh / oldHeight;
				nw = Math.round(hp * nw);
				f.width.value = nw;
			}
		}
	}

	if (f.width.value != "")
		oldWidth = nw;

	if (f.height.value != "")
		oldHeight = nh;

	// After constrain
	pl = serializeParameters();
	
	if (pl == '') {
		p.innerHTML = '<img src="img/fr-nopreview.gif" alt="pas d\'aper&ccedil;u disponible" />';
		return;
	}

	pl = tinyMCEPopup.editor.plugins.flvplayer._parse(pl);

	if (!pl.src) {
		p.innerHTML = '<img src="img/fr-nopreview.gif" alt="pas d\'aper&ccedil;u disponible" /></p>';
		return;
	}
	
	p.style.display = 'none';
	document.getElementById('preview').style.display = 'block';

	pl.width = !pl.width ? 300 : pl.width;
	pl.height = (type == 'mp3') ? 20 : (!pl.height ? 200 : pl.height);
	pl.id = !pl.id ? 'obj' : pl.id;
	pl.name = !pl.name ? 'eobj' : pl.name;
	pl.align = !pl.align ? '' : pl.align;
	pl.backcolor = !pl.backcolor ? '0xffffff' : pl.backcolor.replace("#", "0x");
	pl.shownavigation = !pl.shownavigation ? 'true' : pl.shownavigation;

	h += '<embed src="'+GLOBALS["lecteur"]+'"';
	h += ' width="'+ pl.width +'" height="'+pl.height+'" allowscriptaccess="always" allowfullscreen="true"';
	h += 'flashvars="width='+ pl.width +'&height='+pl.height+'&file='+pl.src+'&shownavigation='+pl.shownavigation;
	h += '&searchbar=false&autostart=false';
	h += '&repeat=false&backcolor='+pl.backcolor+'&showstop=true&usefullscreen=false"';
	h += ' />';
	document.getElementById('preview').innerHTML = h ;
}

tinyMCEPopup.onInit.add(init);