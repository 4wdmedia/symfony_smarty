<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;
use Smarty_Internal_Template;

class WidgetExtension implements SmartyExtension {

	public function register(Smarty $smarty): void {
		$smarty->registerPlugin('function', 'widget', [$this, 'getWidget']);
	}

	public function getWidget(array $params, Smarty_Internal_Template $smarty): string {
		return '';
	}

}
