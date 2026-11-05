<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages;

use PeServer\Core\Mvc\Logic\Validator;
use PeServer\Core\Mvc\Template\Node\Props;

readonly class ShowErrorMessagesProps extends Props
{
	public function __construct(
		/** @var array<string,string[]> */
		public array $errors,
		public string $key = Validator::COMMON,
	) {
		//NOP
	}
}
