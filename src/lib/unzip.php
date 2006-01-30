<?php

function unzip_files($v_sNomFichierZip)
{	
	$zip = zip_open($v_sNomFichierZip);
	
	if ($zip)
	{	
	    while ($zip_entry = zip_read($zip)) 
	    {
	        $filename = zip_entry_name($zip_entry);
	         		
	  		if (($end = strrpos($filename,"/")) !== FALSE)
	  		{
	  			$tmp = NULL;
				
	  			$dirnames = explode("/",substr($filename,0,$end));
				
				foreach ($dirnames as $dirname)
	  			{
	  				$tmp .= "{$dirname}/";
	  				
	  				if (!is_dir($tmp))
	  					mkdir($tmp,0700);
	  			}
	  		}
			
        	$filesize = zip_entry_filesize($zip_entry);
	        
        	if (zip_entry_open($zip, $zip_entry, "rb"))
        	{
        		$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        		
				if ($buf)
				{
					$fp = fopen($filename,"wb");	
					fwrite($fp,$buf);
					fclose($fp);	
				}
				
				zip_entry_close($zip_entry);
        	}
	    }
	    
	    zip_close($zip);
	}
}

?>