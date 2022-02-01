<?php
declare(strict_types = 1);

namespace Vierwd\Symfony\Smarty\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Vierwd\Symfony\Smarty\Templating\SmartyEngine;

class SmartyInitializeEvent extends Event {

	public const NAME = 'vierwd.smartytemplating.initialize';

	protected SmartyEngine $smartyEngine;

	public function __construct(SmartyEngine $smartyEngine) {
		$this->smartyEngine = $smartyEngine;
	}

	public function getSmartyEngine(): SmartyEngine {
		return $this->smartyEngine;
	}
}
