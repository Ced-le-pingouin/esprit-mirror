<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#flvplayer_dlg.title}</title>
	<script type="text/javascript" src="../../../globals.js.php"></script>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/media.js"></script>
	<script type="text/javascript" src="../../utils/mctabs.js"></script>
	<script type="text/javascript" src="../../utils/validate.js"></script>
	<script type="text/javascript" src="../../utils/form_utils.js"></script>
	<script type="text/javascript" src="../../utils/editable_selects.js"></script>
	<link href="css/flvplayer.css" rel="stylesheet" type="text/css" />
	<base target="_self" />
</head>
<body style="display: none">
    <form onsubmit="insertMedia();return false;" action="#">
		<div class="tabs">
			<ul>
				<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');generatePreview();" onmousedown="return false;">{#flvplayer_dlg.general}</a></span></li>
				<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{#flvplayer_dlg.advanced}</a></span></li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="general_panel" class="panel current">
				<fieldset>
					<legend>{#flvplayer_dlg.general}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
							<tr>
							<td><label for="src">{#flvplayer_dlg.file}</label></td>
							  <td>
									<table border="0" cellspacing="0" cellpadding="0">
									  <tr>
										<td><input id="src" name="src" type="text" value="" class="mceFocus" onchange="switchType(this.value);generatePreview();" /></td>
										<td id="filebrowsercontainer">&nbsp;</td>
									  </tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><label for="media_type">{#flvplayer_dlg.type}</label></td>
								<td>
									<select id="media_type" name="media_type" onchange="changedType(this.value);generatePreview();">
										<option value="mp3">Audio</option>
										<option value="flash">Vid&eacute;o</option>
										<option value="youtube">YouTube</option>
										<!-- <option value="google">Vid&eacute;os Google</option> -->
									</select>
								</td>
							</tr>
							<tr id="linklistrow">
								<td><label for="linklist">{#flvplayer_dlg.list}</label></td>
								<td id="linklistcontainer">&nbsp;</td>
							</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend>{#flvplayer_dlg.preview}</legend>
					<div id="nopreview"></div>
					<div id="preview"></div>
				</fieldset>
			</div>

			<div id="advanced_panel" class="panel">
				<fieldset>
					<legend>{#flvplayer_dlg.example_flv}</legend>

					<table border="0" cellpadding="4" cellspacing="0" width="100%">
						<tr>
							<td><label for="width">{#flvplayer_dlg.size}</label></td>
							<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="text" id="width" name="width" value="" class="size" onchange="generatePreview('width');" /> x <input type="text" id="height" name="height" value="" class="size"  onchange="generatePreview('height');" /></td>
										<td>&nbsp;&nbsp;<input id="constrain" type="checkbox" name="constrain" class="checkbox" /></td>
										<td><label id="constrainlabel" for="constrain">{#flvplayer_dlg.constrain_proportions}</label></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><label for="backcolor">{#flvplayer_dlg.backcolor}</label></td>
							<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input id="backcolor" name="backcolor" type="text" value="#ffffff" size="9" onchange="updateColor('backcolor_pick','backcolor');generatePreview();" /></td>
										<td id="backcolor_pickcontainer">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset id="flash_options">
					<legend>{#flvplayer_dlg.flash_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_autostart" name="flash_play" onchange="generatePreview();" /></td>
										<td><label for="flash_autostart">{#flvplayer_dlg.autostart}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_repeat" name="flash_loop" onchange="generatePreview();" /></td>
										<td><label for="flash_repeat">{#flvplayer_dlg.repeat}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_shownavigation" name="flash_menu" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="flash_shownavigation">{#flvplayer_dlg.shownavigation}</label></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div class="mceActionPanel">
			<div style="float: left">
				<input type="submit" id="insert" name="insert" value="{#insert}" />
			</div>

			<div style="float: right">
				<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
			</div>
		</div>
	</form>
</body>
</html>
