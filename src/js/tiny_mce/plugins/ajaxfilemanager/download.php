<?php
if (isset($_GET['fichier'])) $file = $_GET['fichier'] ;
else $file = null;
		// si le fichier existe et qu'il finit par .xml ou .csv
		// alors on le télécharge
        if(file_exists($file) && preg_match("/.xml$|.csv$/", $file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
        // sinon, on renvoie l'utilisateur sur la page précédente
        else header('Location: ' . $_SERVER['HTTP_REFERER'] );
?>