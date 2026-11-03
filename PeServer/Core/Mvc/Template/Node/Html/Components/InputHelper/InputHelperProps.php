<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\TemplateParameter;

readonly class InputHelperProps extends Props
{
	public function __construct(
		public TemplateParameter $parameter,
		/** @var non-empty-string */
		public string $key,
		public InputHelperType $type,
		public string $className = '',
		public bool $required = false,
		public DataAttributes|null $data = null,
		public bool $autoError = true,
		public string|null $id = null,
		public bool $readOnly = false,
	) {
		//NOP
	}
}
