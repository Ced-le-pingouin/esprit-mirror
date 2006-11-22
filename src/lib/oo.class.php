<?php

require_once(dirname(__FILE__).'/erreur.class.php');

class OO
{
	function abstraite()
	{
		list( , $asAppelant) = debug_backtrace();
		if ($asAppelant['function'] == $asAppelant['class'] || $asAppelant['function'] == '__construct')
			CErreur::provoquer("Cette classe est abstraite et ne peut donc pas être instanciée");
		else
			CErreur::provoquer("Méthode abstraite non redéfinie");
	}
	
	function implemente($v_sInterface)
	{
		if (!class_exists($v_sInterface))
			CErreur::provoquer("Tentative d'implémenter une interface inexistante: $v_sInterface");
		
		// récupération de la classe qui doit implémenter l'interface
		list( , $asAppelant) = debug_backtrace();
		
		// vérification que la classe qui implémente dispose bien de toutes les méthodes requises de l'interface
		$asMethodesManquantes = array_diff(get_class_methods($v_sInterface),
		                                   get_class_methods($asAppelant['class']));
		if (count($asMethodesManquantes))
		{
			CErreur::provoquer("Implémentation incomplète de l'interface $v_sInterface. "
		                      ."Méthodes manquantes: ".implode(', ', $asMethodesManquantes));
		}
	}
}

?>