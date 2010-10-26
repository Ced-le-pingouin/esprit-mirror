<?php
require_once dirname(__FILE__).'/../../include/template.inc.php';

class TemplateBlocEtendu extends TPL_Block
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