/**
 * $Id: basé sur le plugin media
 *
 * @author Moxiecode
 * @info Plugin media modifié par Loïc
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.FlvPlayerPlugin', {
		init : function(ed, url) {
			var t = this;
			
			t.editor = ed;
			t.url = url;

			function isMediaElm(n) {
				return /^(mceItemFlash|mceItemFlashAudio|mceItemYoutubeVideo|mceItemGoogleVideo)$/.test(n.className);
			};

			// Register commands
			ed.addCommand('mceFlvPlayer', function() {
				ed.windowManager.open({
					file : url + '/flvplayer.php',
					width : 430 + parseInt(ed.getLang('flvplayer.delta_width', 0)),
					height : 470 + parseInt(ed.getLang('flvplayer.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('flvplayer', {title : 'flvplayer.desc', cmd : 'mceFlvPlayer'});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('flvplayer', n.nodeName == 'IMG' && isMediaElm(n));
			});

			ed.onInit.add(function() {
				var lo = {
					mceItemFlash : 'flash',
					mceItemFlashAudio : 'mp3',
					mceItemYoutubeVideo : 'youtube',
					mceItemGoogleVideo : 'google'
				};

				if (ed.settings.content_css !== false)
					ed.dom.loadCSS(url + "/css/content.css");

				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.name == 'img') {
							each(lo, function(v, k) {
								if (ed.dom.hasClass(o.node, k)) {
									o.name = v;
									o.title = ed.dom.getAttrib(o.node, 'title');
									return false;
								}
							});
						}
					});
				}

				if (ed && ed.plugins.contextmenu) {
					ed.plugins.contextmenu.onContextMenu.add(function(th, m, e) {
						if (e.nodeName == 'IMG' && /mceItem(Flash|FlashAudio|YoutubeVideo|GoogleVideo)/.test(e.className)) {
							m.add({title : 'flvplayer.edit', icon : 'flvplayer', cmd : 'mceFlvPlayer'});
						}
					});
				}
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				var h = o.content;

				h = h.replace(/<script[^>]*>\s*write(Flash|FlashAudio|YoutubeVideo|GoogleVideo)\(\{([^\)]*)\}\);\s*<\/script>/gi, function(a, b, c) {
					var o = t._parse(c);

					return '<img class="mceItem' + b + '" title="' + ed.dom.encode(c) + '" src="' + url + '/img/trans.gif" width="' + o.width + '" height="' + o.height + '" />'
				});
				
				h = h.replace(/<object([^>]*)>/gi, '<span class="mceItemObject" $1>');
				h = h.replace(/<param ([^>]*)>/gi, '<span class="mceItemParam" $1>');
				h = h.replace(/<embed([^>]*)>/gi, '<span class="mceItemEmbed" $1>');
				h = h.replace(/<\/(object|embed)([^>]*)>/gi, '</span>');
	
				o.content = h;
			});

// solution pour afficher correctement le contenu video en ouvrant l'editeur.
// on récupère directement le contenu de Econcept sans passer par la fonction 'mySetContent'
			ed.onInit.add(function(ed) {
				if (top.recuperer) {
					ed.setContent(top.recuperer());
					}
				else alert('Une erreur est survenue.\nMerci de contacter l\'administrateur du site!');
			});
		
			ed.onSetContent.add(function(ed) {
				t._spansToImgs(ed.getBody());
			});
			
			ed.onPreProcess.add(function(ed, o) {
				var dom = ed.dom;

				if (o.set) {
					t._spansToImgs(o.node);

					each(dom.select('IMG', o.node), function(n) {
						var p = {};

						if (isMediaElm(n)) {
							p = t._parse(n.title);
							dom.setAttrib(n, 'width', dom.getAttrib(n, 'width', p.width || 100));
							dom.setAttrib(n, 'height', dom.getAttrib(n, 'height', p.height || 100));
						}
					});
				}

				if (o.get) {
					each(dom.select('IMG', o.node), function(n) {
						var ci, cb, ty;

						switch (n.className) {
							case 'mceItemFlash':
								ci = 'd27cdb6e-ae6d-11cf-96b8-444553540000';
								cb = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
								mt = 'flash';
								ty = 'application/x-shockwave-flash';
								break;
							case 'mceItemFlashAudio':
								ci = 'd27cdb6e-ae6d-11cf-96b8-444553540000';
								cb = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
								mt = 'mp3';
								ty = 'application/x-shockwave-flash';
								break;
							case 'mceItemYoutubeVideo':
								ci = 'd27cdb6e-ae6d-11cf-96b8-444553540000';
								cb = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
								mt = 'youtube';
								ty = 'application/x-shockwave-flash';
								break;
							case 'mceItemGoogleVideo':
								ci = 'd27cdb6e-ae6d-11cf-96b8-444553540000';
								cb = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
								mt = 'google';
								ty = 'application/x-shockwave-flash';
								break;
						}
												
						if (ty) {
							dom.replace(t._buildObj({
								classid : ci,
								typeAppli : ty,
								codebase : cb,
								type : mt
							}, n), n);
						}
					});
				}
			});

			ed.onPostProcess.add(function(ed, o) {
				o.content = o.content.replace(/_value=/g, 'value=');
			});
		},

		getInfo : function() {
			return {
				longname : 'flvplayer',
				author : 'Lo&iuml;c',
				authorurl : 'http://flodi.grenet.fr/esprit/',
				infourl : 'http://flodi.grenet.fr/esprit/',
				version : '1.0'
			};
		},

		// Private methods

		_buildObj : function(o, n) {
			var ob, ed = this.editor, dom = ed.dom, p = this._parse(n.title);
			p.width = o.width = dom.getAttrib(n, 'width') || 100;
			p.height = o.height = dom.getAttrib(n, 'height') || 100;
			if (p.backcolor)
				p.backcolor = p.backcolor.replace("#", "0x");
				
			ob = dom.create('span', {
				mce_name : 'object',
				//classid : "clsid:" + o.classid,
				type : o.typeAppli,
				data : 	GLOBALS["lecteur"]+'?file='+p.src +
						'&showstop=true&usefullscreen=false&autostart=' + p.autostart +
						'&repeat=' + p.repeat + '&backcolor=' + p.backcolor + 
						'&shownavigation=' + p.shownavigation,
				//codebase : o.codebase,
				width : o.width,
				height : o.height
			});

			dom.add(ob, 'span', {
				mce_name : 'param',
				name : 'movie', 'value' : GLOBALS["lecteur"]+'?file='+p.src +
					'&showstop=true&usefullscreen=false&autostart=' + p.autostart +
					'&repeat=' + p.repeat + '&backcolor=' + p.backcolor + 
					'&shownavigation=' + p.shownavigation
			});
			
			dom.add(ob, 'span', {style : 'border:#ccc dashed 1px;color:red;'}, 'Flash Player n\'est pas &agrave; jour.<br /><a href="http://www.macromedia.com/go/getflashplayer" class="attention" target="_blank">Cliquez ici</a> pour t&eacute;l&eacutecharger la derni&egrave;re version.');
            
/*			dom.add(ob, 'span', {
				mce_name : 'embed',
				src : GLOBALS["lecteur"], // on récupère l'url du lecteur flash définit dans 'globals.js.php'
				width : p.width,
				type : o.type,
				height : p.height,
				allowfullscreen : 'true',
				allowscriptaccess : 'always',
				flashvars : 'file=' + p.src +
					'&showstop=true&usefullscreen=false&autostart=' + p.autostart + 
					'&repeat=' + p.repeat + '&backcolor=' + p.backcolor + 
					'&shownavigation=' + p.shownavigation
			});
*/
			return ob;
		},

		_spansToImgs : function(p) {
			var t = this, dom = t.editor.dom, im, ci;

			each(dom.select('span', p), function(n) {			
				// Convert embed or object into image
				if ((dom.getAttrib(n, 'class') == 'mceItemEmbed') || (dom.getAttrib(n, 'class') == 'mceItemObject')) {
					switch (dom.getAttrib(n, 'type')) {
						case 'flash':
							dom.replace(t._createImg('mceItemFlash', n), n);
							break;
						case 'mp3':
							dom.replace(t._createImg('mceItemFlashAudio', n), n);
							break;
						case 'youtube':
							dom.replace(t._createImg('mceItemYoutubeVideo', n), n);
							break;
						case 'google':
							dom.replace(t._createImg('mceItemGoogleVideo', n), n);
							break;
						default:
							dom.replace(t._createImg('mceItemFlash', n), n);
							break;						
					}
				}			
			});
		},

		_createImg : function(cl, n) {
			var im, dom = this.editor.dom, pa = {}, ti = '';
			// Create image
			im = dom.create('img', {
				src : this.url + '/img/trans.gif',
				width : dom.getAttrib(n, 'width') || 100,
				height : dom.getAttrib(n, 'height') || 100,
				'class' : cl
			});
			im.title = '';
			// Setup base parameters
			each(['id', 'name', 'width', 'height', 'backcolor', 'align', 'data'], function(na) {
				var v = dom.getAttrib(n, na);
				if (v) {
					if (na == 'data')
					{
						filetemp = v.split('?');
						params = filetemp[1].split('&');
						for (i=0; i<params.length; i++) {
							var reg1=new RegExp("file=([^,]*)", "g");
							var reg2=new RegExp("backcolor=([^,]*)", "g");
							params[i] = params[i].replace(reg1, 'src:\'$1\'');
							params[i] = params[i].replace(reg2, 'backcolor:\'$1\'');
							params[i] = params[i].replace('=', ':');
							if (i == 0) pa[na] = params[i];
							else pa[na] += ','+params[i];
						}
						im.title += pa[na];
					}
					else {
						pa[na] = v;
						im.title += na+':\''+pa[na]+'\',';
					}
				}
			});

			// Add optional parameters
			each(dom.select('span', n), function(n) {
				if (dom.hasClass(n, 'mceItemParam'))
					pa[dom.getAttrib(n, 'name')] = dom.getAttrib(n, '_value');
			});

			// Use src not movie
			if (pa.movie) {
				pa.src = pa.movie;
				delete pa.movie;
			}

			delete pa.width;
			delete pa.height;

			return im;
		},

		_parse : function(s) {
			return tinymce.util.JSON.parse('{' + s + '}');
		},
	
		_serialize : function(o) {
			return tinymce.util.JSON.serialize(o).replace(/[{}]/g, '');
		}
	});

	// Register plugin
	tinymce.PluginManager.add('flvplayer', tinymce.plugins.FlvPlayerPlugin);
})();