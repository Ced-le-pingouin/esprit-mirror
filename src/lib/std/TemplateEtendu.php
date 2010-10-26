<?php
require_once dirname(__FILE__).'/../../include/template.inc.php';

class TemplateEtendu extends Template
{
	/**
	 * @param string $motif
	 * @param callback $callback
	 */
	public function remplacerParMotifEtCallback($motif, $callback)
    {
        $this->data = preg_replace_callback($motif, $callback, $this->data);
    }
}