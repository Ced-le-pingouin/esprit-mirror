function exporter() {
	var elems = document.forms[0].elements["format"];
	var action = null;
	
	for (var i=0; i<elems.length; i++)
		if (elems[i].checked) {
			action = elems[i].value;
			break;
		}
	
	if (action != null)
	{
		document.forms[0].action = action;
		document.forms[0].submit();
		top.close();
	}
}

function init() {
	var elems = document.forms[0].elements["format"];
	for (var i=0; i<elems.length; i++)
		elems[i].onfocus = function() {
			this.blur();
		};
}

window.onload = init;
