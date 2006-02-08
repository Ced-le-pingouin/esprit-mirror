<?php

if (phpversion () >= "4.0.5")
	return;

function array_search ($searchString,$array)
{
    foreach ($array as $content)
	{ 
        if ($content == $searchString)
            return $pos; 
		
        $pos++; 
    } 
}

?>
