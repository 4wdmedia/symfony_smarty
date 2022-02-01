<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Extension;

use Smarty;

interface SmartyExtension {

	public function register(Smarty $smarty): void;

}
