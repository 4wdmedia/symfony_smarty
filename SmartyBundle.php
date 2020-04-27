<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SmartyBundle extends Bundle {

	public function build(ContainerBuilder $container) {
		parent::build($container);
	}
}
