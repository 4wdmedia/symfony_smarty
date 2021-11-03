<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;

class ModifierExtension implements SmartyExtension {

	public function __construct() {
	}

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('modifier', 'integer', [$this, 'smarty_integer']);
	}

	/**
	 * @param mixed $value
	 */
	public function smarty_integer($value): string {
		return number_format((int)$value, 0, ',', '.');
	}
}
