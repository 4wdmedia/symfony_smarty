<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\DataCollector;

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SmartyCollector extends AbstractDataCollector {

	public function getName(): string {
		return 'vierwd.smarty';
	}

	public function collect(Request $request, Response $response, \Throwable $exception = null): void {
		// todo
	}

}
