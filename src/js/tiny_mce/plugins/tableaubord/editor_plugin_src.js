/**
 * @author Silecs
 * @copyright Copyright © 2007, Silecs.
 */
 (function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('tableaubord');

	tinymce.create('tinymce.plugins.TableauBordPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed) {
			// Register example button
			var imgtab = '../../js/tiny_mce/plugins/tableaubord/img/tableaubord.gif';
			ed.addButton('tableauborde', {
				title : 'tableaubord.tableauborde_desc',
				cmd : 'mceTableauBorde',
				image : imgtab,
				onclick : function(editor_id, element, command, user_interface, value) {
					tinyMCE.execCommand('mceInsertContent', false, '[tableaudebord /e]');
					return false;
				}
			});
			ed.addButton('tableaubordi', {
				title : 'tableaubord.tableaubordi_desc',
				cmd : 'mceTableauBordi',
				image : imgtab,
				onclick : function(editor_id, element, command, user_interface, value) {
					tinyMCE.execCommand('mceInsertContent', false, '[tableaudebord /i]');
					return false;
				}
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Tableau de bord',
				author : 'Silecs',
				authorurl : 'http://www.silecs.info',
				infourl : 'http://sourcesup.cru.fr/projects/esprit/',
				version : '2.0 (mise à jour pour v3 de tiny)'
			};
		},
	});

	// Register plugin
	tinymce.PluginManager.add('tableaubord', tinymce.plugins.TableauBordPlugin);
})();