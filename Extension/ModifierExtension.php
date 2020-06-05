<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;
use Smarty_Internal_Template;

class ModifierExtension {

	public function __construct() {
	}

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('modifier', 'integer', [$this, 'smarty_integer']);
	}

	public function smarty_integer($value): string {
		return number_format((int)$value, 0, ',', '.');
	}
}