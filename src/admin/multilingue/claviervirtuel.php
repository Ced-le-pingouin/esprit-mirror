<?php

$globalid = 0;

function textarea($type, $cols=50, $rows=4) {
	global $globalid;
	if (!$type)
		return false;
	$globalid++;
	if ($type != "pinyin") {
		echo "La saisie peut se faire au clavier ou en cliquant sur les touches.<br />";
	}
?>
Vous pouvez utiliser la zone de texte pour copier-coller le texte ailleurs.

<form name="textarea<?php echo $globalid ?>" id="textarea<?php echo $globalid ?>" class="ml">
<p>
<textarea name="saisie" onkeypress="insert_<?php echo $type ?>(event,this)" cols="<?php echo $cols ?>" rows="<?php echo $rows ?>" class="<?php echo $type ?>"></textarea>
<input type="button" onClick="reset();this.form.saisie.focus()" value="Effacer">
</p>
</form>
<?php
	 return "textarea$globalid";
}

function showkeyboard($touches, $id, $chars=0) {
	if (count($touches) > 3) {
		$azerty = array( preg_split('//',' 1234567890)=',-1,PREG_SPLIT_NO_EMPTY),
						 preg_split('//','azertyuiop^$',-1,PREG_SPLIT_NO_EMPTY),
						 preg_split('//','qsdfghjklm',-1,PREG_SPLIT_NO_EMPTY),
						 preg_split('//','wxcvbn,;:!',-1,PREG_SPLIT_NO_EMPTY) );
	} else {
		$azerty = array( preg_split('//','azertyuiop^$',-1,PREG_SPLIT_NO_EMPTY),
						 preg_split('//','qsdfghjklm',-1,PREG_SPLIT_NO_EMPTY),
						 preg_split('//','wxcvbn,;:!',-1,PREG_SPLIT_NO_EMPTY) );
	}

	if (count($touches) < 3) {
		die('Il faut au moins 3 lignes pour d&eacute;finir le clavier et non '.count($touches).'.');
		return false;
	}
	switch (count($touches)) {
		case 3: $decalage = array(0, 1, 2.9); break;
		case 4: $decalage = array(0, 2, 3, 4.9); break;
		default: $decalage = array(0, 6+3, 6+4.5, 6+6.5);
	}

	for ($i=0; $i<count($touches); $i++) {
		$touches[$i] = explode(" ",$touches[$i]);
		if ($chars != 0) { $chars[$i] = explode(" ",$chars[$i]); }
	}
	if ($chars == 0) {
		$chars = $touches;
	}

//	$lsize = array(12, 12, 10);
	$result = "";
	for ($i=0; $i<count($touches); $i++) {
//		$ligne = explode(" ",$touches[$i]);
		/* if (count($ligne) != $lsize[$i]) {
			die("La ligne ".$i." n'a pas le bon nombre d'&eacute;l&eacute;ments :".count($ligne)."au lieu de ".$lsize[$i]);
			return false;
		}
		*/
		$result .= "<div class=\"ligne\">\n";
		if (isset($decalage[$i]) && $decalage[$i] > 0) {
			$result .= '<span style="float:left; width:'.$decalage[$i].'ex;">&nbsp;</span>';
		}
		$j = 0;
		foreach($touches[$i] as $char) {
			if ($chars[$i][$j] == "&nbsp;") { $chars[$i][$j]=" "; }
			if ($char == "&nbsp;") { 
				$result .= '<a onclick="insertAtCursor(document.'.$id.'.saisie,\' \')" style="width:12ex">&nbsp;';
			} else {
				$result .= '<a onclick="insertAtCursor(document.'.$id.'.saisie,\''.$chars[$i][$j].'\')">'. $char;
			}
			if ( isset($azerty[$i][$j]) ) {
				$result .= '<span class="azerty">'.$azerty[$i][$j].'</span>';
			}
			$result .= "</a>\n";
			$j++;
		}
		$result .= "</div>\n";
	}
	echo '<div class="claviervirtuel">', $result, "</div>\n<div class=\"ligne\">&nbsp;</div>\n";
	return true;
}

function keyboard($type, $id) {
	switch ($type) {
		case "russian":
//			return showkeyboard( array("ф я у к е н г ш щ з : ;", "й ы в а п р о л д ь ù *", "ц ч с м и т б ж Ж !"),
			return showkeyboard( array("Й Ц У К Е Н Г Ш Щ З Х Ъ", "Ф Ы В А П Р О Л Д Ж Э", "Я Ч С М И Т Ь Б Ю Ë"),
			                     $id,
								 array("й ц у к е н г ш щ з х ъ", "ф ы в а п р о л д ж э", "я ч с м и т ь б ю ë") );
		case "arabic":
//			return showkeyboard( array("د ج ح خ ه ع غ ف ق ث ص ض", "ش س ي ب ل ا ت ن ن م ك ط", "ء ؤ ر لا ى ة و ز ظ لآ"),
			return showkeyboard( array("ذ ١ ٢ ٣ ٤ ٥ ٦ ٧ ٨ ٩ ٠", "ض ص ث ق ف غ ع ه خ ح ج د", "ش س ي ب ل ا ت ن م ك ط *",
									   "ئ ء ؤ ر لا ى ة و ز ظ",
									   "آ آ , . ؟ &nbsp; أ أ ـ ، / ؛", "َ ً ُ ٌ ِ ٍ" ),
			                     $id );
		default:
			return false;
	} // switch
}

?>
