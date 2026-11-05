<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\MarkdownEditor;

use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\TemplateParameter;

readonly class MarkdownEditorProps extends Props
{
	public function __construct(
		public TemplateParameter $parameter,
		/** @var non-empty-string */
		public string $key,
	) {
		//NOP
	}
}
