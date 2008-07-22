<?php
	/**
	 * file manager platform
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once("globals.inc.php");
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$session->gc();
	require_once(CLASS_MANAGER);
	require_once(CLASS_SESSION_ACTION);
	$sessionAction = new SessionAction();

	$manager = new manager();
	$manager->setSessionAction($sessionAction);
	$fileList = $manager->getFileList();
	$folderInfo = $manager->getFolderInfo();
	$views = array(
		'detail'=>LBL_BTN_VIEW_DETAILS,
		'thumbnail'=>LBL_BTN_VIEW_THUMBNAIL,
	);
	if(!empty($_GET['view']))
	{
		switch($_GET['view'])
		{
			case 'detail':
			case 'thumbnail':
				$view = $_GET['view'];
				break;
			default:
				$view = CONFIG_DEFAULT_VIEW;
		}
	}else 
	{
		$view = CONFIG_DEFAULT_VIEW;
	}

	// ajout de la fonction pour vérifier sur quel lien on a cliqué depuis TinyMCE
	(isset($_GET['mode'])) ? $sManagerMode = $_GET['mode'] : $sManagerMode = '';

// récupération du prenom/nom de l'utilisateur
	$oProjet = new CProjet();
	$bEstAdmin = $oProjet->verifAdministrateur();
	$sPrenom_Nom = $oProjet->oUtilisateur->retPrenom($bBool=TRUE)." ".$oProjet->oUtilisateur->retNom($bBool=TRUE);

	//on enlève les accents
	// problème avec les noms en arabe ou en cyrillique
	$sPrenom_Nom = preg_replace("/&(.)(acute|cedil|circ|grave|ring|tilde|uml);/", "$1", $sPrenom_Nom);
	$sPrenom_Nom = strtolower($sPrenom_Nom);
	//echo $sPrenom_Nom;

/**
 *		on vérifie la liste complète des répertoires,
 *		si aucun n'a le nom du tuteur, on le créé.
 * 
 * 		L'utilisation du rafaîchissement peut entrainer des erreurs (si on se trouve déjà dans un répertoire).
 * 		On doit donc vérifier aussi si le nom du répertoire est présent dans l'url en mode 'GET'
 * 
 *		@author Loïc Tauleigne
 */

	$compteur = 0;
	$bTrouve = false;
	$sUtilisateur = str_replace(' ','_',$sPrenom_Nom);
	
	if (isset($_GET['path'])) {
		(eregi($sUtilisateur, $_GET['path'])) ? $bTrouve = true : $bTrouve = false;
	}

	foreach($fileList as $file)
	{
		foreach($file as $k=>$v)
		{
			if($k == 'name' && $v == $sUtilisateur)
			{
				$bTrouve = true;
			}
		}	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" debug="true">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ajax File Manager</title>
<script type="text/javascript" src="jscripts/jquery.js"></script>
<script type="text/javascript" src="jscripts/form.js"></script>
<script type="text/javascript" src="jscripts/select.js"></script>
<script type="text/javascript" src="jscripts/thickbox.js"></script>
<script type="text/javascript" src="jscripts/calendar.js"></script>
<script type="text/javascript" src="jscripts/contextmenu.js"></script>
<!--<script type="text/javascript" src="jscripts/jeditable.js"></script>
<script type="text/javascript" src="jscripts/file_manager_general.js"></script>-->
<script type="text/javascript" src="jscripts/media.js"></script>
<script type="text/javascript" src="jscripts/ajaxfileupload.js"></script>
<script type="text/javascript" src="jscripts/file_manager_general.js"></script>

<script type="text/javascript">

	var queryString = '<?php echo makeQueryString(array('path')); ?>';
	var paths = {'root':'<?php echo addTrailingSlash(backslashToSlash(CONFIG_SYS_ROOT_PATH))."', 'root_title':'".LBL_FOLDER_ROOT."'};\n";?>
	var parentFolder = {};
	var urls = {
			'upload':'<?php echo CONFIG_URL_UPLOAD; ?>',
			'preview':'<?php echo CONFIG_URL_PREVIEW; ?>',
			'cut':'<?php echo CONFIG_URL_CUT; ?>',
			'copy':'<?php echo CONFIG_URL_COPY; ?>',
			'paste':'<?php echo CONFIG_URL_FILE_PASTE; ?>',
			'delete':'<?php echo CONFIG_URL_DELETE ?>',
			'rename':'<?php echo CONFIG_URL_SAVE_NAME; ?>',
			'thumbnail':'<?php echo CONFIG_URL_IMG_THUMBNAIL;  ?>',
			'create_folder':'<?php echo CONFIG_URL_CREATE_FOLDER; ?>',
			'text_editor':'<?php echo  CONFIG_URL_TEXT_EDITOR; ?>',
			'image_editor':'<?php echo  CONFIG_URL_IMAGE_EDITOR; ?>',
			'download':'<?php echo CONFIG_URL_DOWNLOAD ?>',
			'present':'<?php echo getCurrentUrl(); ?>',
			'home':'<?php echo CONFIG_URL_HOME; ?>',
			'view':'<?php echo CONFIG_URL_LIST_LISTING; ?>'			
		};
	var permits = {'del':<?php echo (CONFIG_OPTIONS_DELETE?1:0); ?>, 'cut':<?php echo (CONFIG_OPTIONS_CUT?'1':'0'); ?>, 'copy':<?php echo (CONFIG_OPTIONS_COPY?1:0); ?>, 'newfolder':<?php echo (CONFIG_OPTIONS_NEWFOLDER?1:0); ?>, 'rename':<?php echo (CONFIG_OPTIONS_RENAME?1:0); ?>, 'upload':<?php echo (CONFIG_OPTIONS_UPLOAD?1:0); ?>, 'edit':<?php echo (CONFIG_OPTIONS_EDITABLE?1:0); ?>, 'view_only':<?php echo (CONFIG_SYS_VIEW_ONLY?1:0); ?>};
	var currentFolder = {};
	var commandes = {'boolNF':<?php echo (CONFIG_OPTIONS_NEWFOLDER?1:0) ?>, 'boolUeA':<?php echo ($bEstAdmin?'1':'0') ?>, 'utilisateur':'<?php echo $sPrenom_Nom ?>', 'trouve':'<?php echo $bTrouve ?>'};
	var warningDelete = '<?php echo WARNING_DELETE; ?>';
	var newFile = {'num':1, 'label':'<?php echo FILE_LABEL_SELECT; ?>', 'upload':'<?php echo FILE_LBL_UPLOAD; ?>'};
	var counts = {'new_file':1};
	var thickbox = {'width':'<?php echo CONFIG_THICKBOX_MAX_WIDTH; ?>', 
									'height':'<?php echo CONFIG_THICKBOX_MAX_HEIGHT; ?>',
									'next':'<?php echo THICKBOX_NEXT; ?>',
									'previous':'<?php echo THICKBOX_PREVIOUS; ?>',
									'close':'<?php echo THICKBOX_CLOSE; ?>' 
		
	};
	
	var tb_pathToImage = "theme/<?php echo CONFIG_THEME_NAME; ?>/images/loadingAnimation.gif";
	var msgInvalidFolderName = '<?php echo ERR_FOLDER_FORMAT; ?>';
	var msgInvalidFileName = '<?php echo ERR_FILE_NAME_FORMAT; ?>';
	var msgInvalidExt = '<?php echo ERR_FILE_TYPE_NOT_ALLOWED; ?>';
	var msgNotPreview = '<?php echo PREVIEW_NOT_PREVIEW; ?>';

	var warningCutPaste = '<?php echo WARNING_CUT_PASTE; ?>';
	var warningCopyPaste = '<?php echo WARNING_COPY_PASTE; ?>';
	var warningDel = '<?php echo WARNING_DELETE; ?>';
	var warningNotDocSelected = '<?php echo ERR_NOT_DOC_SELECTED; ?>';
	var noFileSelected = '<?php echo ERR_NOT_FILE_SELECTED; ?>';
    var unselectAllText = '<?php echo TIP_UNSELECT_ALL; ?>';
    var selectAllText = '<?php echo TIP_SELECT_ALL; ?>';
	var action = '<?php echo $sessionAction->getAction(); ?>';
	var numFiles = <?php echo $sessionAction->count(); ?>;
	var warningCloseWindow = '<?php echo WARING_WINDOW_CLOSE; ?>';
	var numRows = 0; 

	var wordCloseWindow = '<?php echo LBL_ACTION_CLOSE; ?>';
	var wordPreviewClick = '<?php echo LBL_CLICK_PREVIEW; ?>';

	var searchRequired = false;
	var supporedPreviewExts = '<?php echo CONFIG_VIEWABLE_VALID_EXTS ?>'; 
	var supportedUploadExts = '<?php echo CONFIG_UPLOAD_VALID_EXTS; ?>'
	var elementId = <?php  echo (!empty($_GET['elementId'])?"'" . $_GET['elementId'] . "'":'null'); ?>;
	var files = {};
$(document).ready(
	function()
	{
		
		if(typeof(cancelSelectFile) != 'undefined')
		{
			$('#linkClose').show();
		}
		$('input[@name=view]').each(
			function()
			{

				if(this.value == '<?php echo $view; ?>')
				{
					this.checked = true;
				}else
				{
					this.checked = false;
				}
			}
		);
		
		popUpCal.clearText = '<?php echo CALENDAR_CLEAR; ?>';
		popUpCal.closeText = '<?php echo CALENDAR_CLOSE; ?>';
		popUpCal.prevText = '<?php echo CALENDAR_PREVIOUS; ?>';
		popUpCal.nextText = '<?php echo CALENDAR_NEXT; ?>';
		popUpCal.currentText = '<?php echo CALENDAR_CURRENT; ?>';
		popUpCal.buttonImageOnly = true;
		popUpCal.dayNames = new Array('<?php echo CALENDAR_SUN; ?>','<?php echo CALENDAR_MON; ?>','<?php echo CALENDAR_TUE; ?>','<?php echo CALENDAR_WED; ?>','<?php echo CALENDAR_THU; ?>','<?php echo CALENDAR_FRI; ?>','<?php echo CALENDAR_SAT; ?>');
		popUpCal.monthNames = new Array('<?php echo CALENDAR_JAN; ?>','<?php echo CALENDAR_FEB; ?>','<?php echo CALENDAR_MAR; ?>','<?php echo CALENDAR_APR; ?>','<?php echo CALENDAR_MAY; ?>','<?php echo CALENDAR_JUN; ?>','<?php echo CALENDAR_JUL; ?>','<?php echo CALENDAR_AUG; ?>','<?php echo CALENDAR_SEP; ?>','<?php echo CALENDAR_OCT; ?>','<?php echo CALENDAR_NOV; ?>','<?php echo CALENDAR_DEC; ?>');
		popUpCal.dateFormat = 'YMD-';
		$('.inputMtime').calendar({autoPopUp:'both', buttonImage:'theme/<?php echo CONFIG_THEME_NAME; ?>/images/date_picker.png'});
		
		
		initAfterListingLoaded();
		addMoreFile();

	} );

</script>
<?php
	if(file_exists(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'jscripts' . DIRECTORY_SEPARATOR . 'for_' . CONFIG_EDITOR_NAME . ".js")
	{
	?>
	<script type="text/javascript" src="jscripts/<?php echo 'for_' . CONFIG_EDITOR_NAME . '.js'; ?>"></script>
	<?php
	}
?>
<link rel="stylesheet" type="text/css" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/<?php echo CONFIG_EDITOR_NAME; ?>.css" />
<link rel="stylesheet" type="text/css" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/jquery-calendar.css" />
<link rel="stylesheet" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/thickbox.css" type="text/css" media="screen" />

</head>
<?php 
if (!$bTrouve && !$bEstAdmin) 	// Si le répertoire du tuteur n'est pas trouvé ou que la personne n'est pas admin,
								// alors on crée le répertoire
	echo "<body onload=\"return newUserWin(window.document.getElementById('actionNewFolder'));\"";
	//echo "<body onload=\"return newUserWin(window.document.actionNewFolder);\"";
else echo "<body>";
?>
	<div id="wrapper">
  	<div id="header">
  		<dl id="currentFolderInfo">
  			<dt><?php echo LBL_CURRENT_FOLDER_PATH; ?></dt>
  			<dd id="currentFolderPath"><?php echo $folderInfo['path']; ?></dd>
  		</dl>
    	<div id="viewList">
    			<label><?php echo LBL_BTN_VIEW_OPTIONS; ?></label>
					<?php 
						foreach($views as $k=>$v)
						{
							?>
							<input type="radio" name="view"  class="radio" onclick="changeView(this);" value="<?php echo $k; ?>" <?php echo ($k==$view?'checked':''); ?>> <?php echo $v; ?> &nbsp;&nbsp;
							
							<?php
						}
					?></div>
				<ul id="actionHeader">
					<li><a href="#" id="actionRefresh" onclick="return windowRefresh();"><span><?php echo LBL_ACTION_REFRESH; ?></span></a></li>
					<li><a href="#" id="actionSelectAll" class="check_all" onclick="return checkAll(this);"><span><?php echo LBL_ACTION_SELECT_ALL; ?></span></a></li>
					<?php 
						if(CONFIG_OPTIONS_DELETE && $bEstAdmin)
						{
							?>
							<li><a href="#" id="actionDelete" onclick="return deleteDocuments();"><span><?php echo LBL_ACTION_DELETE; ?></span></a></li>
							<?php
						}
					?>
					<?php 
						if(CONFIG_OPTIONS_CUT && $bEstAdmin)
						{
							?>
							<li><a href="#" id="actionCut" onclick="return cutDocuments('<?php echo ERR_NOT_DOC_SELECTED_FOR_CUT; ?>');"><span><?php echo LBL_ACTION_CUT; ?></span></a></li>			
							<?php
						}
					?>
					<?php 
						if(CONFIG_OPTIONS_COPY && $bEstAdmin)
						{
							?>
							<li><a href="#" id="actionCopy" onclick="return copyDocuments('<?php echo ERR_NOT_DOC_SELECTED_FOR_COPY; ?>');"><span><?php echo LBL_ACTION_COPY; ?></span></a></li>
							<?php
						}
					?>
					<?php 
						if((CONFIG_OPTIONS_CUT || CONFIG_OPTIONS_COPY) && $bEstAdmin)
						{
							?>
							<li><a href="#" id="actionPaste" onclick="return pasteDocuments('<?php echo ERR_NOT_DOC_SELECTED_FOR_PASTE; ?>');"><span><?php echo LBL_ACTION_PASTE; ?></span></a></li>
							<?php
						} 
						//if(CONFIG_OPTIONS_NEWFOLDER || $bEstAdmin)
						{
							?>
							<li id="ajoutDossier"><a id="actionNewFolder" href="#" onclick="return newFolderWin(this);"><span><?php echo LBL_BTN_NEW_FOLDER; ?></span></a></li>
							<?php
						} 
						//if(CONFIG_OPTIONS_UPLOAD)
						{
							?>
							<li id="envoiFichier"><a  id="actionUpload" href="#" onclick="return uploadFileWin(this);"><span><?php echo LBL_BTN_UPLOAD; ?></span></a></li>
							<?php
						}
					?>
					<li><a href="#" id="actionClose" onclick="closeWindow('<?php echo IMG_WARING_WIN_CLOSE; ?>');"><?php echo IMG_BTN_CLOSE; ?></a></li>
					<li><a href="#" class="thickbox" id="actionInfo" onclick="return infoWin(this);"><span>Info</span></a></li>
				</ul>    
<form action="" method="post" name="formAction" id="formAction"><input type="hidden" name="currentFolderPath" id="currentFolderPathVal" value="" /><select name="selectedDoc[]" id="selectedDoc" style="display:none;" multiple="multiple"></select><input type="hidden" name="action_value" value="" id="action_value" /></form>				  
    </div>
    
    <div id="body">
         
         
      <div id="rightCol">
	      	<?php include_once(CONFIG_URL_LIST_LISTING); ?>
      </div> 
      
      <div id="leftCol">
      


				<fieldset id="folderFieldSet" >
					
				<legend><?php echo LBL_FOLDER_INFO; ?></legend>
				<table cellpadding="0" cellspacing="0" class="tableSummary" id="folderInfo">
					<tbody>
						<tr>
							<th><?php echo LBL_FOLDER_PATH; ?></th>
							<td colspan="3" id="folderPath"><?php echo transformFilePath($folderInfo['path']); ?></td>
						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_CREATED; ?></th>
							<td colspan="3" id="folderCtime"><?php echo date(DATE_TIME_FORMAT,$folderInfo['ctime']); ?></td>

						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_MODIFIED; ?></th>
							<td colspan="3" id="folderMtime"><?php echo date(DATE_TIME_FORMAT,$folderInfo['mtime']); ?></td>
						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_SUDDIR; ?></th>
							<td  colspan="3" id="folderSubdir"><?php echo $folderInfo['subdir']; ?></td>

						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_FIELS; ?></th>
							<td  colspan="3" id="folderFile"><?php echo $folderInfo['file']; ?></td>						
						</tr>
						
						<tr>
							<th><?php echo LBL_FOLDER_WRITABLE; ?></th>
							<td id="folderWritable"><span class="<?php echo ($folderInfo['is_readable']?'flagYes':'flagNo'); ?>">&nbsp;</span></td>
							<th><?php echo LBL_FOLDER_READABLE; ?></th>
							<td  id="folderReadable"><span class="<?php echo ($folderInfo['is_writable']?'flagYes':'flagNo'); ?>">&nbsp;</span></td>						
						
						</tr>



					</tbody>
				</table>
				</fieldset>
			<fieldset id="fileFieldSet" style="display:none" >
				<legend><?php echo LBL_FILE_INFO; ?></legend>
				<table cellpadding="0" cellspacing="0" class="tableSummary" id="fileInfo">
					<tbody>
						<tr>
							<th><?php echo LBL_FILE_NAME; ?></th>
							<td colspan="3" id="fileName"></td>
						</tr>
						<tr>
							<th><?php echo LBL_FILE_CREATED; ?></th>
							<td colspan="3" id="fileCtime"></td>

						</tr>
						<tr>
							<th><?php echo LBL_FILE_MODIFIED; ?></th>
							<td colspan="3" id="fileMtime"></td>
						</tr>
						<tr>
							<th><?php echo LBL_FILE_SIZE; ?></th>
							<td  colspan="3" id="fileSize"></td>

						</tr>
						<tr>
							<th><?php echo LBL_FILE_TYPE; ?></th>
							<td  colspan="3" id="fileType"></td>						
						</tr>
						<tr>
							<th><?php echo LBL_FILE_WRITABLE; ?></th>
							<td id="fileWritable"><span class="flagYes">&nbsp;</span></td>
							<th><?php echo LBL_FILE_READABLE; ?></th>
							<td id="fileReadable"><span class="flagNo">&nbsp;</span></td>		
						</tr>

					</tbody>
				</table>
<?php
// si on passe par le menu outils,
// le bouton pour envoyer dans l'éditeur est désactivé
	if ($sManagerMode !== 'racine') {		
        echo "<p class=\"searchButtons\" id=\"returnCurrentUrl\">
  
        	<span class=\"right\" id=\"linkSelect\">
        		<input type=\"button\" value=\"".MENU_SELECT."\"  id=\"selectCurrentUrl\" class=\"button\">
        	</span>
        	
        </p>";
	}
?>							
			</fieldset>

<?php
/**
 * Vérifie si la personne est 'reponsable de plateforme'.
 * Permet : 
 * soit de visualiser directement les logs
 * soit de les télécharger au format csv ou xml.
 * 
 * @date	2008/06/29
 * 
 * @author	Loïc TAULEIGNE
 */
/*if ($bEstAdmin)
{
echo "
<fieldset id=\"VoirLog\" class=\"boxCSV\">
<legend>Historique des envois</legend>
<p>
	Vous pouvez t&eacute;l&eacute;charger les fichiers logs, ou les afficher directement.
</p>
<p>&nbsp;</p>
<p class=\"center\"><a href=\"#\" id=\"actionVoirLogs\" onclick=\"return infoLog(this);\">Afficher les logs</a></p>
<p>&nbsp;</p>
<p>T&eacute;l&eacute;chargement du log :</p>

<ul>
	<li>Fichier csv : <a href=\"download.php?fichier=".CONFIG_LOG_PATH."\">CSV</a></li>
	<li>Fichier xml : <a href=\"download.php?fichier=".CONFIG_LOGXML_PATH."\">XML</a></li>
</ul>
</fieldset>
";
}
*/
 ?>
      </div>
      
      <div class="clear"></div>
    </div>
  </div>
  <div class="clear"></div>

  <div id="ajaxLoading" style="display:none"><img class="ajaxLoadingImg" src="theme/<?php echo CONFIG_THEME_NAME; ?>/images/ajaxLoading.gif" /></div>

<?php
/**
 * Si la personne est 'reponsable de plateforme',
 * elle a accès aux fichiers logs.
 * 
 * @date	2008/06/29
 * 
 * @author	Loïc TAULEIGNE
 */
if ($bEstAdmin)
{ 
?>
  <div id="winLog" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="divLog">
  		<p class="Log">Historique des envois de fichiers sur la plateforme</p>
   	<table id="tableVoirLog" class="tableLog" cellpadding="0" cellspacing="0">

  	</table>
  		</div>
  	</div>
  </div>
<script type="text/javascript">
var xmlDoc=null;
var tempDonnees=null;
if (window.ActiveXObject)
{// code for IE
xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
}
else if (document.implementation.createDocument)
{// code for Mozilla, Firefox, Opera, etc.
xmlDoc=document.implementation.createDocument("","",null);
}
else
{
alert('Ce script ne fonctionne pas avec votre navigateur.\n Essayez de t\u00E9l\u00E9charger les fichiers log.');
}
if (xmlDoc!=null) 
{
xmlDoc.async=false;
xmlDoc.load("<?php echo CONFIG_LOGXML_PATH; ?>");
var x=xmlDoc.getElementsByTagName("entree");
var table=document.getElementById("tableVoirLog");

var bodyNode = document.createElement("tbody");
var trNodeDebut = document.createElement("tr");
var thNode = document.createElement("th");
thNode.appendChild(document.createTextNode("Date"));
trNodeDebut.appendChild(thNode);
thNode = document.createElement("th");
thNode.appendChild(document.createTextNode("Utilisateur"));
trNodeDebut.appendChild(thNode);
thNode = document.createElement("th");
thNode.appendChild(document.createTextNode("Fichier"));
trNodeDebut.appendChild(thNode);
thNode = document.createElement("th");
thNode.appendChild(document.createTextNode("Chemin"));
trNodeDebut.appendChild(thNode);
thNode = document.createElement("th");
thNode.appendChild(document.createTextNode("Md5"));
trNodeDebut.appendChild(thNode);
bodyNode.appendChild(trNodeDebut);
for (var i=0;i<x.length;i++)
{
var utilisateurXML =	x[i].getElementsByTagName("utilisateur")[0].childNodes[0].nodeValue;
var fichierXML = 		x[i].getElementsByTagName("fichier")[0].childNodes[0].nodeValue;
var cheminXML = 		x[i].getElementsByTagName("chemin")[0].childNodes[0].nodeValue;
var md5XML = 			x[i].getElementsByTagName("md5")[0].childNodes[0].nodeValue;
md5XML = md5XML.substring(0, md5XML.length/2)+"\n"+md5XML.substring(md5XML.length/2); // on coupe le md5 en 2
cheminXML = cheminXML.substring(0, cheminXML.search(/images|medias/i)+6)+""+cheminXML.substring(cheminXML.search(/images|medias\//i)+6);

var trNode = document.createElement("tr");
var tdNode = document.createElement("td");
tdNode.appendChild(document.createTextNode(x[i].getElementsByTagName("date")[0].childNodes[0].nodeValue));
trNode.appendChild(tdNode);

tdNode = document.createElement("td");
tdNode.appendChild(document.createTextNode(utilisateurXML));
trNode.appendChild(tdNode);

tdNode = document.createElement("td");
tdNode.appendChild(document.createTextNode(fichierXML));
trNode.appendChild(tdNode);

tdNode = document.createElement("td");
tdNode.appendChild(document.createTextNode(cheminXML));
trNode.appendChild(tdNode);

tdNode = document.createElement("td");
tdNode.appendChild(document.createTextNode(md5XML));
trNode.appendChild(tdNode);

bodyNode.appendChild(trNode);
}
table.appendChild(bodyNode);
}
<?php
echo"
</script>
";
}
?>
 
  <div id="winUpload" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
		  	<form id="formUpload" name="formUpload" method="post" enctype="multipart/form-data" action="">
		  	<table class="tableForm" cellpadding="0" cellspacing="0">
		  		<thead>
		  			<tr>
		  				<span>&nbsp;</span>
		  				<th colspan="2"><?php echo FILE_FORM_TITLE; ?></th>
		  			</tr>
		  			<tr>
		  				<td>&nbsp;</td>
		  			</tr>
		  		</thead>
		  		<tbody id="fileUploadBody">
		  			<tr style="display:none">
		  				<th><label><?php echo FILE_LABEL_SELECT; ?></label></th>
		  				<td><input type="file" class="input" name="file"  /> <input type="button" class="button" value="<?php echo FILE_LBL_UPLOAD; ?>" /> <a href="#" class="action" title="Annuler" style="display:none" ><span class="cancel">&nbsp;</span></a>  <span class="uploadProcessing" style="display:none">&nbsp;<span></td>
		  			</tr>		
		  		</tbody>
		  		<tfoot>
		  			<tr>
		  				<td>&nbsp;</td>
		  			</tr>
		  			<tr style="text-align: left; color:red;">
		  				<th>&nbsp;</th>
		  				<td><?php echo FILE_DISCLAIMER; ?></td>
		  			</tr>
		  		</tfoot>
		  	</table>
		  	</form>  		
  		</div>
  	</div>
  </div> 
  
<?php
/**
 * Si il n'y a pas de répertoire au nom de la personne
 * on ouvre un popup l'indiquant.
 * Une fois validé, le répertoire est créé et on recharge la page
 * 
 * @date	2008/07/11
 * 
 * @author	Loïc TAULEIGNE
 */
if (!$bTrouve && !$bEstAdmin)
{
echo "
  <div id=\"winNewUser\" style=\"display:none\">
  	<div class=\"jqmContainer\">
  		<div class=\"jqmHeader\">
  		</div>
  		<div class=\"jqmBody\">
	    	<form id=\"formNewFolder\" name=\"formNewFolder\" method=\"post\" action=\"\">
	  	<input type=\"hidden\" name=\"currentFolderPath\" value=\"\" id=\"currentNewfolderPath\" />
	  	<input type=\"hidden\" name=\"new_folder\" id=\"new_folder\" value=\"".$sUtilisateur."\" class=\"input\">
	  	<table class=\"tableForm\" cellpadding=\"0\" cellspacing=\"0\">
	  		<thead>
	  			<tr>
	  				<th colspan=\"2\">".FOLDER_FORM_TITLE."</th>
	  			</tr>
	  			<tr>
	  				<td colspan=\"2\">".FOLDER_NEW_INFO."</td>
	  			</tr>
	  		</thead>
	  		<tbody>
	  			<tr>
		  			<td>&nbsp;</td>
		  		</tr>
	  			<tr>
	  				<th><label>".FOLDER_LBL_TITLE."</label></th>
	  				<td>".$sUtilisateur."</td>
	  			</tr> 
	  		</tbody>
	  		<tfoot>
	  			<tr>
	  				<th>&nbsp;</th>
	  				<td><input type=\"button\" value=\"".FOLDER_LBL_CREATE."\" class=\"button\" onclick=\"return doCreateFolder(true);\"  /></td>
	  			</tr>
	  		</tfoot>
	  	</table>	
	  	</form>	
  		</div>
  	</div>
  </div>
";
}
else
echo "
  <div id=\"winNewFolder\" style=\"display:none\">
  	<div class=\"jqmContainer\">
  		<div class=\"jqmHeader\">
  			<a href=\"#\" onclick=\"tb_remove();\">".LBL_ACTION_CLOSE."</a>
  		</div>
  		<div class=\"jqmBody\">
	    	<form id=\"formNewFolder\" name=\"formNewFolder\" method=\"post\" action=\"\">
	  	<input type=\"hidden\" name=\"currentFolderPath\" value=\"\" id=\"currentNewfolderPath\" />
	  	<table class=\"tableForm\" cellpadding=\"0\" cellspacing=\"0\">
	  		<thead>
	  			<tr>
	  				<th colspan=\"2\">".FOLDER_FORM_TITLE."</th>
	  			</tr>
	  		</thead>
	  		<tbody>
	  			<tr>
	  				<th><label>".FOLDER_LBL_TITLE."</label></th>
	  				<td><input type=\"text\" name=\"new_folder\" id=\"new_folder\" value=\"\" class=\"input\"></td>
	  			</tr>    			
	  		</tbody>
	  		<tfoot>
	  			<tr>
	  				<th>&nbsp;</th>
	  				<td><input type=\"button\" value=\"".FOLDER_LBL_CREATE."\" class=\"button\" onclick=\"return doCreateFolder(false);\"  /></td>
	  			</tr>
	  		</tfoot>
	  	</table>	
	  	</form>	
  		</div>
  	</div>
  </div>
";
?>
  
  <div id="winPlay" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="closeWinPlay();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
  			<div id="playGround"></div>
  		</div>
  	</div>
  </div>
  <div id="winRename" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
		  	<form id="formRename" name="formRename" method="POST" action="">
		  	<input type="hidden" name="original_path" id="original_path" />
		  	<input type="hidden" name="num" id="renameNum" value="" />
		  	<table class="tableForm" cellpadding="0" cellspacing="0">
		  		<thead>
		  			<tr>
		  				<th colspan="2"><?php echo RENAME_FORM_TITLE; ?></th>
		  			</tr>
		  		</thead>
		  		<tbody>
		  			<tr>
		  				<th><label><?php echo RENAME_NEW_NAME; ?></label></th>
		  				<td><input type="name" id="renameName" class="input" name="name" /> 
		          </td>
		  			</tr>
		  		</tbody>
		  		<tfoot>
		  			<tr>
		  				<th>&nbsp;</th>
		  				<td><input type="button" value="<?php echo RENAME_LBL_RENAME; ?>" class="button" onclick="return doRename();"  /></td>
		  			</tr>
		  		</tfoot>
		  	</table>
		  	</form>
  		</div>  	

  	</div>
  	
  </div>        
  <div id="winInfo" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
   	<table class="tableInfo" cellpadding="0" cellspacing="0">
<!--  		<thead>
  			<tr>
  				<th colspan="2">
  				Ajax File/Image Manager
  				</th>
  			</tr>
  		</thead>-->
  		<tbody>
  			<tr>
	  			<th>
	  				<label>Author:</label>
	  			</th>
	  			<td>
	  				<a href="&#109;a&#105;l&#116;&#111;:&#99;&#97;&#105;&#108;&#111;&#110;&#103;&#113;&#117;&#110;&#64;&#121;&#97;&#104;&#111;&#111;&#46;&#99;&#111;&#109;&#46;&#99;&#110;">Logan Cai</a>
	  			</td>
  			</tr>
  			<tr>
  				<th>
  					<label>Official Website:</label>
  				</th>
  				<td>
  					<a href="http://www.phpletter.com">http://www.phpletter.com</a>
  				</td>
  			</tr>
  			<tr>
  				<th>
  					<label>Support Forum:</label>
  				</th>
  				<td>
  					<a href="http://www.phpletter.com/forum/">http://www.phpletter.com/forum/</a>
  				</td>
  			</tr>
  		</tbody>
  	</table>
  		</div>  	


  	</div>
  </div>
  <div id="contextMenu" style="display:none">
  	<ul>
  		<li><a href="#" id="menuSelect"><?php echo MENU_SELECT; ?></a></li>
  		<li><a href="#" id="menuPreview"><?php echo MENU_PREVIEW; ?></a></li>
  		<li><a href="#" id="menuDownload"><?php echo MENU_DOWNLOAD; ?></a></li>
  		<li><a href="#" id="menuRename"><?php echo MENU_RENAME; ?></a></li>
  		<li><a href="#" id="menuEdit"><?php echo MENU_EDIT; ?></a></li>
  		<li><a href="#" id="menuCut"><?php echo MENU_CUT; ?></a></li>
  		<li><a href="#" id="menuCopy"><?php echo MENU_COPY; ?></a></li>
  		<li><a href="#" id="menuPaste"><?php echo MENU_PASTE; ?></a></li>
  		<li><a href="#" id="menuDelete"><?php echo MENU_DELETE; ?></a></li>
  		<li><a href="#" id="menuPlay"><?php echo MENU_PLAY; ?></a></li>
  	</ul>
  </div>
</body>
</html>