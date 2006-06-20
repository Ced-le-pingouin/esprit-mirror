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
	var convert_pinyin = new Object;
	convert_pinyin["a"] = String("ā á ǎ à").split(" ");
	convert_pinyin["e"] = String("ē é ě è").split(" ");
	convert_pinyin["i"] = String("ī í ǐ ì").split(" ");
	convert_pinyin["o"] = String("ō ó ǒ ò").split(" ");
	convert_pinyin["u"] = String("ū ú ǔ ù").split(" ");
	convert_pinyin["ü"] = String("ǖ ǘ ǚ ǜ").split(" ");
	var charcode = window.event ? event.keyCode : event.which;
	if (charcode == String("v").charCodeAt(0)) {
		insertAtCursor(area,"ü");
		if (event.preventDefault) event.preventDefault();
		if (event.stopPropagation) event.stopPropagation();
		event.returnValue=false; // MSIE
		return false;
	}
	var char;
	if ((charcode >= 49 ) && (charcode <= 52 )) {
		char = String.fromCharCode(charcode);
		var lastchar = charBeforeCursor(area);
		if (lastchar in convert_pinyin) {
			replaceLastChar(area, convert_pinyin[lastchar][charcode-49]);
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
var convert_arabic = setconvert("a z e r t y u i o p ^ $ "+"q s d f g h j k l m ù "+"w x c v  b n , ; : !",
				"ض ص ث ق ف غ ع ه خ ح ج د "+"ش س ي ب ل ا ت ن م ك ط "+"ئ ء ؤ رل ا ى ة و ز ظ" );

/*
function showkeyboard (touches) {
	if (touches.length < 3) {
		alert('Il faut au moins 3 lignes pour définir le clavier.');
		return false;
	}
	if (touches.length == 3) {
		decalage = new Array(0, 1, 2.9);
	}
	if (touches.length == 4) {
		decalage = new Array(0, 2, 3, 4.9);
	}
	if (touches.length == 5) {
		decalage = new Array(0, 2, 3, 4.9, 0);
	}
   
	var result = "";
	for (var i=0; i<touches.length; ++i) {
		var ligne = touches[i].split(" ");
		result += '<div class="ligne">';
		if (decalage[i] > 0) {
			result += '<span style="float:left; width:'+decalage[i]+'ex;">&nbsp;</span>';
		}
		for (var j=0; j<ligne.length; ++j) {
			result += '<a href="#" onclick="insertAtCursor(document.claviervirtuel.saisie,\''+ligne[j]+'\')">' + ligne[j] + '</a>';
		}
		result += '</div>';
	}
	document.write('<div class="claviervirtuel">'+result+'</div>');
}
*/

function transcrire(area) {
   var text=area.value;
   for (var inputchar in tabconvert) {
	  text = text.replace(inputchar, tabconvert[inputchar]);
   }
   area.value=text;
}

function replaceLastChar(myField, character) {
	// IE support
	if (document.selection) {
		myField.focus();
		// get empty selection range since its just a cursor
		var sel = document.selection.createRange();
		sel.moveStart("character",-1);
//		sel.moveEnd("character",0);
		sel.select();
		sel.text = character;
//		sel.select();
	} else {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos-1)
		              + character
		              + myField.value.substring(endPos, myField.value.length);
	}
}
function charBeforeCursor(myField) {
	// IE support
	if (document.selection && document.selection.createRange) {
		var r = document.selection.createRange();
		r.moveStart('character',-1);
		return r.text;
	}
	// MOZILLA and Co support
	else if (myField.selectionStart || myField.selectionStart == 0) {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		return myField.value.substring(startPos-1, startPos);
	} else {
		return "";
	}
}
function insertAtCursor(myField, myValue) {
	// IE support
	if (document.selection && document.selection.createRange) {
		myField.focus();
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
			}
		} else {
			block.style.display="none";
		}
	}
}
