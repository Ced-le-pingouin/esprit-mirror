/**
 * @author Silecs
 * @copyright Copyright © 2007, Silecs.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('tableaubord');

var TinyMCE_TableauBordPlugin = {
	getInfo : function() {
		return {
			longname : 'Tableau de bord',
			author : 'Silecs',
			authorurl : 'http://www.silecs.info',
			infourl : 'http://sourcesup.cru.fr/esprit/',
			version : '1.0'
		};
	},

	/**
	 * Returns the HTML code for a specific control or empty string if this plugin doesn't have that control.
	 * A control can be a button, select list or any other HTML item to present in the TinyMCE user interface.
	 * The variable {$editor_id} will be replaced with the current editor instance id and {$pluginurl} will be replaced
	 * with the URL of the plugin. Language variables such as {$lang_somekey} will also be replaced with contents from
	 * the language packs.
	 *
	 * @param {string} cn Editor control/button name to get HTML for.
	 * @return HTML code for a specific control or empty string.
	 * @type string
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "tableaubordi":
				return tinyMCE.getButtonHTML(cn, 'lang_tableaubordi_desc', '{$pluginurl}/images/tableaubord.gif', 'mceTableauBordI', false);
			case "tableauborde":
				return tinyMCE.getButtonHTML(cn, 'lang_tableauborde_desc', '{$pluginurl}/images/tableaubord.gif', 'mceTableauBordE', false);
		}

		return "";
	},

	/**
	 * Executes a specific command, this function handles plugin commands.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that issued the command.
	 * @param {HTMLElement} element Body or root element for the editor instance.
	 * @param {string} command Command name to be executed.
	 * @param {string} user_interface True/false if a user interface should be presented.
	 * @param {mixed} value Custom value argument, can be anything.
	 * @return true/false if the command was executed by this plugin or not.
	 * @type
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		var inst = tinyMCE.getInstanceById(editor_id), h;

		switch (command) {
			case "mceTableauBordI":
				tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, '[tableaudebord /i]');
				return true;
			case "mceTableauBordE":
				tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, '[tableaudebord /e]');
				return true;
		}

		return false;
	}

};

tinyMCE.addPlugin("tableaubord", TinyMCE_TableauBordPlugin);
