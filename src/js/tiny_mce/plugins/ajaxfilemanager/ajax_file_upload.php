<?php
	/**
	 * processing the uploaded files
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */	
	sleep(3);
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	echo "{";
	$error = "";
	$info = "";
	
	include_once(CLASS_UPLOAD);
	$upload = new Upload();
								
	$upload->setInvalidFileExt(explode(",", CONFIG_UPLOAD_INVALID_EXTS));
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_UPLOAD)
	{
		//$error = SYS_DISABLED;
		$error = "disabled";
	}
	elseif(empty($_GET['folder']) || !isUnderRoot($_GET['folder']))
	{
		//$error = ERR_FOLDER_PATH_NOT_ALLOWED;
		$error = "folder not allowed";
	}else	if(!$upload->isFileUploaded('file'))
	{
		//$error = ERR_FILE_NOT_UPLOADED;
		$error = "file not uploaded";
	}else if(!$upload->moveUploadedFile($_GET['folder']))
	{
		//$error = ERR_FILE_MOVE_FAILED;
		$error = "file move failed";
	}	
	elseif(!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_EXTS)))
	{		
		//$error = ERR_FILE_TYPE_NOT_ALLOWED;
		$error = "file type not allowed";
	}elseif((strpos($_GET['folder'],CONFIG_IMAGE_PATH) !== false) && (!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_IMAGE))))
	{		
		// si on se trouve dans le répertoire IMAGE (configuré dans 'CONFIG_IMAGE_PATH'), on vérifie que le type de fichier est bien une image définie dans 'CONFIG_UPLOAD_VALID_IMAGE'
		//$error = ERR_FILE_NOT_IMG;
		$error = "le fichier n'est pas une image";
	}elseif((strpos($_GET['folder'],CONFIG_MEDIA_PATH) !== false) && (!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_MEDIA))))
	{		
		// si on se trouve dans le répertoire MEDIA (configuré dans 'CONFIG_MEDIA_PATH'), on vérifie que le type de fichier est bien une video définie dans 'CONFIG_UPLOAD_VALID_MEDIA'
		//$error = ERR_FILE_NOT_MEDIA;
		$error = "le fichier n'est pas un media";
	}elseif(defined('CONFIG_UPLOAD_MAXSIZE') && CONFIG_UPLOAD_MAXSIZE && $upload->isSizeTooBig(CONFIG_UPLOAD_MAXSIZE))
	{		
		 //$error = sprintf(ERROR_FILE_TOO_BID, transformFileSize(CONFIG_UPLOAD_MAXSIZE));
		 $error = "too big";
	}else
	{
							include_once(CLASS_FILE);
							require_once("globals.inc.php");
							$oProjet = new CProjet();
							$path = $upload->getFilePath();
							$obj = new file($path);
							$tem = $obj->getFileInfo();							
							if(sizeof($tem))
							{	
								include_once(CLASS_MANAGER);
							
								$manager = new manager($upload->getFilePath(), false);			
															
								$fileType = $manager->getFileType($upload->getFileName());

								foreach($fileType as $k=>$v)
								{
									$tem[$k] = $v;
								}							
								//$tem['name'] = preg_replace('/ /', '_', $tem['name']);
								$tem['path'] = backslashToSlash($path);		
								$tem['type'] = "file";
								$tem['size'] = transformFileSize($tem['size']);
								$tem['ctime'] = date(DATE_TIME_FORMAT, $tem['ctime']);
								$tem['mtime'] = date(DATE_TIME_FORMAT, $tem['mtime']);
								$tem['short_name'] = shortenFileName($tem['name']);						
								$tem['flag'] = 'noFlag';
								$obj->close();
								foreach($tem as $k=>$v)
								{
										$info .= sprintf(", %s:'%s'", $k, $v);									
								}

								$info .= sprintf(", url:'%s'",  getFileUrl($path));
								$info .= sprintf(", tipedit:'%s'",  TIP_DOC_RENAME);

								// insertion des informations dans le log
								$sUtilisateur = $oProjet->oUtilisateur->retPrenom()." ".$oProjet->oUtilisateur->retNom();
								$sCheminFichier = str_replace('../', '', $tem['path']);
								$sCheminFichier = preg_replace("/([[:alnum:]_\- ]+\/)[[:alnum:]_\- ]+\.[[:alpha:]]{3,4}/", "$1", $sCheminFichier); // on enlève le nom du fichier
								$sFichierLog = CONFIG_LOG_PATH;
								$sFichierXml = CONFIG_LOGXML_PATH;
								$sMd5Fichier = md5_file($tem['path']); // md5 du fichier
								$sDonneesCSV = $tem['ctime'].";".$sUtilisateur.";".$tem['name'].";".$sCheminFichier.";".$sMd5Fichier."\r\n";
								$sDonneesXML = 	"	<entree>\r\n"
												."		<date>".$tem["ctime"]."</date>\r\n"
												."		<utilisateur>".$sUtilisateur."</utilisateur>\r\n"
												."		<fichier>".$tem["name"]."</fichier>\r\n"
												."		<chemin>".$sCheminFichier."</chemin>\r\n"
												."		<md5>".$sMd5Fichier."</md5>\r\n"
												."	</entree>\r\n"
												."</log>";
								/**
								 * insertion de données dans un fichier csv et xml en tant que log :
								 * date d'ajout du fichier, nom de la personne, nom du fichier, chemin, md5 du fichier
								 */
// mis en commentaire, je crois que c'est ce qui fait buggé le plugin.
/*								$fp = fopen($sFichierLog, 'a');
									fwrite($fp, $sDonneesCSV);
								fclose($fp);
								$fpxml = fopen($sFichierXml, 'r+');
									fseek($fpxml, '-6', SEEK_END); // on se place à la fin du fichier, juste devant la balise de fin global.
									fwrite($fpxml, $sDonneesXML); // on écrit les données en ajoutant la balise finale globale écrasée
								fclose($fpxml);
*/
							}else 
							{
								//$error = ERR_FILE_NOT_AVAILABLE;
								$error = 'file not availbale';
							}
	}
	echo "error:'" . $error . "'";
	echo $info;
	echo "}";
	
?>