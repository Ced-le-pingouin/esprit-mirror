<?php
require_once dirname(__FILE__).'/../../include/template.inc.php';

class TemplateEtendu extends Template
{
	const EXTENSION_PAR_DEFAUT = 'tpl';
	
	/**
	 * @param string $fichierTemplate
	 * @param boolean $inclureSousTemplatesAuto
	 */
	public function __construct($fichierTemplate, $inclureSousTemplatesAuto = true)
	{
		if (!is_readable($fichierTemplate)) {
			throw new Exception("Fichier template '{$fichierTemplate}' inaccessible!");
		}
		
		parent::Template($fichierTemplate);
		
		if ($inclureSousTemplatesAuto) {
		  $this->inclureSousTemplates();
		}
	}
	
	public function inclureSousTemplates()
	{
		$this->remplacerParMotifEtCallback(
		    '/\\[sub:([\\w.]+)\\]/',
		    array($this, 'remplacerCommandeSousTemplateParContenuFichier')
		);
	}
	
	/**
	 * @param array[]string $correspondances
	 * @throws Exception
	 */
	protected function remplacerCommandeSousTemplateParContenuFichier($correspondances)
	{
		list($tout, $nomSousTemplate) = $correspondances;
		
		$fichierSousTemplate = $nomSousTemplate.'.'.self::EXTENSION_PAR_DEFAUT;
		
		if (!is_readable($fichierSousTemplate)) {
			throw new Exception("Le sous-template '{$fichierSousTemplate}' n'a pu être inclus car il est inaccessible");
		}
		
		return file_get_contents($fichierSousTemplate);
	}
	
	/**
	 * @param string $motif
	 * @param callback $callback
	 */
	public function remplacerParMotifEtCallback($motif, $callback)
    {
        $this->data = preg_replace_callback($motif, $callback, $this->data);
    }
}