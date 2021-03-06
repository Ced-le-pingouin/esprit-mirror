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
	require_once("globals.inc.php");
	echo "{";
	$error = "";
	$info = "";

	include_once(CLASS_UPLOAD);
	$upload = new Upload();
							
	$upload->setInvalidFileExt(explode(",", CONFIG_UPLOAD_INVALID_EXTS));
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_UPLOAD)
	{
		$error = SYS_DISABLED;
	}
	elseif(empty($_GET['folder']) || !isUnderRoot($_GET['folder']))
	{
		$error = ERR_FOLDER_PATH_NOT_ALLOWED;
	}elseif(!$upload->isFileUploaded('file'))
	{
		$error = ERR_FILE_NOT_UPLOADED;
	}elseif(!$upload->moveUploadedFile($_GET['folder']))
	{
		$error = ERR_FILE_MOVE_FAILED;
	}	
	elseif(!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_EXTS)))
	{		
		$error = ERR_FILE_TYPE_NOT_ALLOWED;
	}elseif((strpos($_GET['folder'],CONFIG_IMAGE_PATH) !== false) && (!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_IMAGE))))
	{		
		// si on se trouve dans le r�pertoire IMAGE (configur� dans 'CONFIG_IMAGE_PATH'), on v�rifie que le type de fichier est bien une image d�finie dans 'CONFIG_UPLOAD_VALID_IMAGE'
		$error = ERR_FILE_NOT_IMG;
	}elseif((strpos($_GET['folder'],CONFIG_MEDIA_PATH) !== false) && (!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_MEDIA))))
	{		
		// si on se trouve dans le r�pertoire MEDIA (configur� dans 'CONFIG_MEDIA_PATH'), on v�rifie que le type de fichier est bien une video d�finie dans 'CONFIG_UPLOAD_VALID_MEDIA'
		$error = ERR_FILE_NOT_MEDIA;
	}elseif(defined('CONFIG_UPLOAD_MAXSIZE') && CONFIG_UPLOAD_MAXSIZE && $upload->isSizeTooBig(CONFIG_UPLOAD_MAXSIZE))
	{		
		 $error = sprintf(ERROR_FILE_TOO_BID, transformFileSize(CONFIG_UPLOAD_MAXSIZE));
	}else
	{
							include_once(CLASS_FILE);
							$path = $upload->getFilePath();
							$obj = new file($path);
							$tem = $obj->getFileInfo();							
							if(sizeof($tem))
							{	
								include_once(CLASS_MANAGER);
							
								//$manager = new manager($upload->getFilePath(), false);
								$manager = new manager('', false);
																		
								$fileType = $manager->getFileType($upload->getFileName());

								foreach($fileType as $k=>$v)
								{
									$tem[$k] = $v;
								}
								
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

								$info .= sprintf(", url:'%s'",  getRelativeFileUrl($path));
								$info .= sprintf(", tipedit:'%s'",  TIP_DOC_RENAME);		

								// insertion des informations dans le log
								$sCheminFichier = str_replace('../', '', $tem['path']);
								$sCheminFichier = preg_replace("/([[:alnum:]_\-\s]+\/)[[:alnum:]_\-\s]+\.[[:alnum:]]{3,4}/", "$1", $sCheminFichier); // on enl�ve le nom du fichier
								$sCheminFichier = preg_replace("/.*\/(depot\/(images|medias){1}\/)([[:alnum:]_\-\s]*)/", "$1$3", $sCheminFichier); // on garde juste la fin du chemin : depot/images|medias/xxx

								$sFichierLog = CONFIG_LOGCSV_PATH;
								$sFichierXml = CONFIG_LOGXML_PATH;
								//$sFichierLog = dir_root_plateform().'depot/log_upload.csv';
								//$sFichierXml = dir_root_plateform().'depot/log_upload.xml';
								$sMd5Fichier = md5_file($tem['path']); // md5 du fichier
								$sDonneesCSV = $sDonneesXML = "";

								if (!file_exists($sFichierLog)) {
									$sDonneesCSV = "Date;Nom de l'image;Chemin;Md5\r\n";
								}
								$sDonneesCSV .= $tem['ctime'].";".$sUtilisateur.";".$tem['name'].";".$sCheminFichier.";".$sMd5Fichier."\r\n";

								// si le fichier n'existe pas, on en cr�e un avec les balises <log>.
								if (!file_exists($sFichierXml)) {
									$fpxml = fopen($sFichierXml, 'w+');
										fwrite($fpxml, "<log>\r\n</log>");
									fclose($fpxml);
								}
								
								$sDonneesXML = 		"	<entree>\r\n"
													."		<date>".$tem["ctime"]."</date>\r\n"
								//					."		<utilisateur>".$sUtilisateur."</utilisateur>\r\n"
													."		<fichier>".$tem["name"]."</fichier>\r\n"
													."		<chemin>".$sCheminFichier."</chemin>\r\n"
													."		<md5>".$sMd5Fichier."</md5>\r\n"
													."	</entree>\r\n";
								//					."</log>";

								/**
								 * insertion de donn�es dans un fichier csv et xml en tant que log :
								 * date d'ajout du fichier, nom de la personne, nom du fichier, chemin, md5 du fichier
								 */
								$fp = fopen($sFichierLog, 'a');
									fwrite($fp, $sDonneesCSV);
								fclose($fp);
								
								$fpxml = fopen($sFichierXml, 'r+');
									fseek($fpxml, '-6', SEEK_END); // on se place � la fin du fichier, juste devant la balise de fin global.
									fwrite($fpxml, $sDonneesXML.'</log>'); // on �crit les donn�es en ajoutant la balise finale globale �cras�e
								fclose($fpxml);
																				
							}else 
							{
								$error = ERR_FILE_NOT_AVAILABLE;
							}


	}
	echo "error:'" . $error . "'";
	echo $info;
	echo "}";
	
?>