<html>
<head>
<title>Transfert de formations</title>
<script type="text/javascript" language="javascript">
<!--
var page = 1;
var autoriser_fermer_fenetre = false;

function fermer() {
	if (autoriser_fermer_fenetre)
		close();
}

function afficher_etape() { top.frames[0].document.writeln("<h4>Etape&nbsp;" + page + "</h4>"); }

function changer_id_form(v_iIdForm) {
	top.frames[0].document.forms[0].elements["ID_FORM_SELECT"].value = v_iIdForm;
}

function changer_page(v_iPage) {
	top.frames[1].location = "transfert_form-menu.php?page=-1";
	top.frames[0].document.forms[0].elements["page"].value = v_iPage;
	top.frames[0].document.forms[0].submit();
}

function precedent() { if (page>1) page--; changer_page(page); }

function suivant() {
	switch (page) {
		case 1:
			top.frames[0].document.forms[0].elements["ID_FORM_SELECT"].value = "0";
			
			if (top.frames[0].document.forms[0].elements["NOM_BDD_SRC"].value.length < 1) {
				alert("Avant de passer à l'étape suivante, sélectionnez une base de données source.");
				return;
			}
			break;
		case 2:
			if (top.frames[0].document.forms[0].elements["ID_FORM_SELECT"].value < 1) {
				alert("Avant de passer à l'étape suivante, sélectionnez une formation dans la liste.");
				return;
			}
			break;
	}
	
	if (page<=4) page++;
	
	changer_page(page);
}

function changer_menu() { top.frames[1].location = "transfert_form-menu.php?page=" + page; }
//-->
</script>
</head>
<frameset rows="*,23">
<frame name="principale" src="transfert_form.php?page=1" frameborder="0" marginwidth="0" marginheight="5" scrolling="no" noresize="noresize">
<frame name="menu" src="transfert_form-menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>