<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\ShowErrorMessages;

use PeServer\Core\Mvc\Logic\Validator;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\NodeContextBase;
use PeServer\Core\Mvc\Template\Node\Props;

readonly class ShowErrorMessagesProps extends Props
{
	public function __construct(
		public string $key,
		/** @var string[] */
		public array $messages,
	) {}
}
