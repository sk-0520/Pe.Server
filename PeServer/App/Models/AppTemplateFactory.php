<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Mvc\Template\ITemplate;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;

class AppTemplateFactory extends TemplateFactory
{
	#region TemplateFactory

	/**
	 * @param TemplateOptions $options めっちゃかえるで。
	 */
	public function createTemplate(TemplateOptions $options): ITemplate
	{
		$customOptions = $options;
		if ($options instanceof AppTemplateOptions) {
			/** @var AppConfiguration */
			$config = $this->container->get(AppConfiguration::class);

			$customOptions = new TemplateOptions(
				Path::combine($options->programContext->applicationDirectory, 'App', 'Views'),
				$options->controllerName,
				$options->programContext,
				$options->urlHelper,
				$options->webSecurity,
				$config->setting->cache->template
			);
		}

		return parent::createTemplate($customOptions);
	}

	#endregion
}
