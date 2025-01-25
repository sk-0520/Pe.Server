<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Environment;
use PeServer\Core\Store\Stores;

class PeServerTemplate extends TemplateBase
{
	public function __construct(
		TemplateOptions $options,
		protected Stores $stores,
		protected Environment $environment
	) {
		parent::__construct($options);

		// $this->engine = new Smarty();

		// $this->engine->addTemplateDir(Path::combine($this->options->rootDirectoryPath, $this->options->baseDirectoryName));
		// $this->engine->addTemplateDir($this->options->rootDirectoryPath);
		// $this->engine->setCompileDir(Path::combine($this->options->temporaryDirectoryPath, 'compile', $this->options->baseDirectoryName));
		// $this->engine->setCacheDir(Path::combine($this->options->temporaryDirectoryPath, 'cache', $this->options->baseDirectoryName));

		// $this->engine->escape_html = true;

		// $this->registerPlugins();
	}

	#region TemplateBase

	public function build(string $templateName, TemplateParameter $parameter): string
	{
		return "";
	}

	#endregion
}
