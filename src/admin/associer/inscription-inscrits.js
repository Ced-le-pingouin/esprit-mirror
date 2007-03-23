function visible(v) {
	var v_elem = v.parentNode;
	
	for (var i=0; i<v_elem.childNodes.length; i++)
	{
		if (v_elem.childNodes.item(i).nodeName == "DT"
			|| v_elem.childNodes.item(i).nodeName == "DD") {
			if (v_elem.childNodes.item(i).className == "replier")
				v_elem.childNodes.item(i).className = "deplier";
			else
				v_elem.childNodes.item(i).className = "replier";
		}
		
		if (v_elem.childNodes.item(i).nodeName == "DD")
			break;
	}
}

function init() {
	var elems = document.getElementsByTagName('input');
	for (var i=0; i<elems.length; i++)
		if ('checkbox' == elems.item(i).type) 
			elems.item(i).onclick = function(event) {
				event = event ? event : window.event;
				event.cancelBubble = true;
			}
}

window.onload = init;
