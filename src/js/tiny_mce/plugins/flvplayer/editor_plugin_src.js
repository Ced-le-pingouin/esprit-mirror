/**
 *
 * @author Loïc
 * @info Plugin FlvPlayer
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

				if (top.recuperer) {
					ed.setContent(top.recuperer());
				}

				ed.selection.onBeforeSetContent.add(t._objectsToSpans, t);

				ed.selection.onSetContent.add(function() {
					t._spansToImgs(ed.getBody());
				});

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

			ed.onBeforeSetContent.add(t._objectsToSpans, t);

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
		_objectsToSpans : function(ed, o) {
			var t = this, h = o.content;

			h = h.replace(/<object([^>]*)>/gi, '<span class="mceItemObject" $1>');
			h = h.replace(/<param([^>]*)>/gi, function(a, b) {return '<span ' + b.replace(/value=/gi, '_mce_value=') + ' class="mceItemParam"></span>'});
			h = h.replace(/<embed([^>]*)>/gi, '<span class="mceItemEmbed" $1>');
			h = h.replace(/<\/(object)([^>]*)>/gi, '</span>');

			o.content = h;
		},

		_buildObj : function(o, n) {
			var ob, ed = this.editor, dom = ed.dom, p = this._parse(n.title);
			p.width = o.width = dom.getAttrib(n, 'width') || 100;
			p.height = o.height = dom.getAttrib(n, 'height') || 100;
			if (p.backcolor)
				p.backcolor = p.backcolor.replace("#", "0x");

			ob = dom.create('span', {
				mce_name : 'object',
				type : o.typeAppli,
				mt : o.type,
				data : 	GLOBALS["lecteur"]+'?file='+p.src +
						'&showstop=true&usefullscreen=false&autostart=' + p.autostart +
						'&repeat=' + p.repeat + '&backcolor=' + p.backcolor + 
						'&shownavigation=' + p.shownavigation,
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
			
			dom.add(ob, 'span', {style : 'border:#ccc dashed 1px;'},
				'L\'objet vid&eacute;o ou audio n\'a pas pu &ecirc;tre affich&eacute;e<br />'
				+ 'Le lecteur Flash n\'est pas &agrave; jour<br />'
				+ 'Pour t&eacute;l&eacute;charger et installer la derni&eagrave;re version, <a href="http://www.macromedia.com/go/getflashplayer" class="" target="_blank">Cliquez ici</a>.');

			return ob;
		},

		_spansToImgs : function(p) {
			var t = this, dom = t.editor.dom, im, ci;

			each(dom.select('span', p), function(n) {			
				// Convert embed or object into image
				if (dom.getAttrib(n, 'class') == 'mceItemObject') {
					switch (dom.getAttrib(n, 'mt')) {
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
						filetemp = v.split('mediaplayer.swf?');
						params = filetemp[1].split('&');
						for (i=0; i<params.length; i++) {
							var reg1=new RegExp("file=([^,]*)", "g");
							var reg2=new RegExp("backcolor=([^,]*)", "g");
							var reg3=new RegExp("src","g");
							params[i] = params[i].replace(reg1, 'src:\'$1\'');
							params[i] = params[i].replace(reg2, 'backcolor:\'$1\'');
							if (!reg3.exec(params[i]))
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