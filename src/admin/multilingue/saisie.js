function zoom( id, factor ) {
	var ta = document.getElementById(id).saisie;
	var currentFontSize;
	if (document.defaultView) {
		currentFontSize = parseInt(document.defaultView.getComputedStyle(ta,'').fontSize);
	} else if (ta.currentStyle) {
		currentFontSize = parseInt(ta.currentStyle.fontSize);
	}
	currentFontSize = Math.round(currentFontSize * factor);
	var nodes = document.getElementById(id).getElementsByTagName('input');
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].getAttribute("name") == 'size') {
			nodes[i].value = Math.round(currentFontSize * factor);
		}
	}
	document.getElementById(id).submit();
}

function insert(event,area,tabconvert) {
	var charcode = window.event ? event.keyCode : event.which;
	var char;
	if ((charcode == 94) && event.shiftKey) {
		char='"';
	} else {
		char = String.fromCharCode(charcode);
	}
	if (!event.ctrlKey && !event.altKey && tabconvert[char]) {
		insertAtCursor(area,tabconvert[char]);
		if (event.preventDefault) event.preventDefault();
		if (event.stopPropagation) event.stopPropagation();
		event.returnValue=false; // MSIE
		return false;
	}
	return true;
}
function insert_russian(event,area) {
   insert(event,area,convert_russian);
}
function insert_arabic(event,area) {
   insert(event,area,convert_arabic);
}
function insert_pinyin(event,area) {
	var charcode = window.event ? event.keyCode : event.which;
	if (charcode == String("v").charCodeAt(0)) {
		insertAtCursor(area,"ü");
		if (event.preventDefault) event.preventDefault();
		if (event.stopPropagation) event.stopPropagation();
		event.returnValue=false; // MSIE
		return false;
	}
	var tone; /* 1-4 */
	if ((charcode >= 49 ) && (charcode <= 52 )) {
		tone = charcode-49;
		if (putToneOnLastWord(tone,area)) {
			if (event.preventDefault) event.preventDefault();
			if (event.stopPropagation) event.stopPropagation();
			event.returnValue=false; // MSIE
			return false;
		}
	}
	return true;
}

function setconvert(from, to) {
   var tabconvert = new Array();
   var ftab = new Array();
   ftab = from.split(/\s+/);
   var ttab = new Array();
   ttab = to.split(/\s+/);
   if (ftab.length != ttab.length) {
	  alert('Erreur, les tables de conversion sont de tailles différentes.');
	  return 0;
   }
   for (var i=0; i < ftab.length; ++i) {
	  tabconvert[ftab[i]] = ttab[i];
   }
   return tabconvert;
}

var convert_russian = setconvert('a z e r t y u i o p ^ $ '+'q s d f g h j k l m ù * '+'w x c v b n , ; : ! '+'A Z E R T Y U I O P " £ '+'Q S D F G H J K L M % µ '+'W X C V B N ? . / § ² ~',
				 'й ц у к е н г ш щ з х ъ '+'ф ы в а п р о л д ж э * '+'я ч с м и т ь б ю , '+'Й Ц У К Е Н Г Ш Щ З Х Ъ '+'Ф Ы В А П Р О Л Д Ж Э µ '+'Я Ч С М И Т Ь Б Ю . ë Ë');
var convert_arabic = setconvert("a z e r t y u i o p ^ $ "+"q s d f g h j k l m ù "+"w x c v  b n , ; : !"+" ²",
				"ض ص ث ق ف غ ع ه خ ح ج د "+"ش س ي ب ل ا ت ن م ك ط "+"ئ ء ؤ ر ا ى ة و ز ظ"+" ذ");

function transcrire(area) {
   var text=area.value;
   for (var inputchar in tabconvert) {
	  text = text.replace(inputchar, tabconvert[inputchar]);
   }
   area.value=text;
}

function putToneOnWord(tone, txt, pos) {
	var priority = new Array("a","o","e","i","u","ü");
	var convert_pinyin = new Object;
	convert_pinyin["a"] = String("ā á ǎ à").split(" ");
	convert_pinyin["o"] = String("ō ó ǒ ò").split(" ");
	convert_pinyin["e"] = String("ē é ě è").split(" ");
	convert_pinyin["iu"] = String("iū iú iǔ iù").split(" ");
	convert_pinyin["i"] = String("ī í ǐ ì").split(" ");
	convert_pinyin["u"] = String("ū ú ǔ ù").split(" ");
	convert_pinyin["ü"] = String("ǖ ǘ ǚ ǜ").split(" ");
	var charpos;
	charpos=txt.lastIndexOf("iu");
	if (charpos >= pos) {
		txt = txt.substring(0,charpos+1) + convert_pinyin["u"][tone] + txt.substring(charpos+2);
		return txt;
	}
	for (var i=0; i < priority.length; ++i) {
		charpos=txt.lastIndexOf(priority[i]);
		if (charpos >= pos) {
			txt = txt.substring(0,charpos) +
				 convert_pinyin[priority[i]][tone] + txt.substring(charpos+1);
			return txt;
		}
	}
	return false;
}

function putToneOnLastWord(tone, myField) {
	var modified = false;
	if (myField.selectionStart) {
		var startPos = myField.selectionStart;
		if (startPos != myField.selectionEnd) return false;
		var before = myField.value.substring(0, startPos);
		var pos_word = before.lastIndexOf(" ");
		if (pos_word == -1) pos_word = 0;
		if (modified = putToneOnWord(tone, before, pos_word)) {
			myField.value = modified + myField.value.substring(startPos, myField.value.length);
			// 2 lines for Mozilla
			myField.selectionStart = startPos;
			myField.selectionEnd = startPos;
			return true;
		}
	} else {
		//alert("Méthode MSIE");
		myField.focus();
		// get empty selection range since its just a cursor
 		var sel = document.selection.createRange();
		sel.moveStart("word",-1);
		sel.select();
		modified = putToneOnWord(tone, sel.text, 0);
		if (modified) {
			sel.text = modified;
			sel.select(); // focus !
			return true;
		}
		sel.collapse(false);
		sel.select();
	}
	return modified;
}
function insertAtCursor(myField, myValue) {
	myField.focus();
	// IE support
	if (document.selection && document.selection.createRange) {
		sel = document.selection.createRange();
		sel.text = myValue;
	}
	// MOZILLA support
	else if (myField.selectionStart || myField.selectionStart == 0) {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		              + myValue
		              + myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += myValue;
	}
	myField.focus();
}

function showblock(id) {
	var allids = new Array("cyrillique","arabe","pinyin");
	for (var i in allids) {
		var thisid = allids[i];
		var block = document.getElementById(thisid);
		if (thisid == id) {
			if (block.style.display == "block") {
				block.style.display="none";
			} else {
				block.style.display="block";
				block.getElementsByTagName("form")[0].getElementsByTagName("textarea")[0].focus();
			}
		} else {
			block.style.display="none";
		}
	}
}
