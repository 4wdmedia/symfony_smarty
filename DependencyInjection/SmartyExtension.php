<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SmartyExtension extends Extension {

	public function load(array $configs, ContainerBuilder $container) {

		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('smarty.yaml');

		$container->setParameter('smarty.default_path', '%kernel.project_dir%/templates/%%name%%');
		$container->setParameter('smarty.plugin_path', '%kernel.project_dir%/smarty');
	}
}
