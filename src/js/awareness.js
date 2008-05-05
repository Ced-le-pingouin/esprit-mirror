/**
 * Awareness
 *
 * Informe le serveur Awareness de la position de l'utilisateur par rapport
 * de la plate-forme.
 *
 * Remarque:
 * - Pour que ca fonctionne ajoutez la ligne si-dessous dans chaque page html.
 *   <body onfocus="notifyServer('nom_applet')">
 *
 * - Ne fonctionne pas sous Macintosh.
 *
 */

function notifyServer(v_sNameApplet)
{
	var obj = document.applet[v_sNameApplet];
	
	if (typeof(obj) == "undefined")
		return;
	
	if (typeof(obj.notifyServer) != "undefined")
		obj.notifyServer();
}

function notifyAwareness() { notifyServer("AwarenessSpy"); }
function notifyAwarenessSpy() { notifyServer("AwarenessSpy"); }
function notifyAwarenessApplet() { notifyServer("AwarenessApplet"); }

function getBadVersionPluginJava()
{
	if (navigator.appName.indexOf("Netscape") != -1)
		for (i=0; i<navigator.plugins.length; i++)
			if (navigator.plugins[i].description.search(/^.*java.*plug.?in.*1\.3.*$/i) != -1)
				return true;
	
	return false;
}

function IntercepterPlantagePluginJava()
{
	return true;
}