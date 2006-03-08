<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

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