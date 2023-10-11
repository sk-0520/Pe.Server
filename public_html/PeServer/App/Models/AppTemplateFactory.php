<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppTemplateOptions;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\SmartyTemplate;
use PeServer\Core\Mvc\Template\TemplateBase;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Throws\NotImplementedException;

class AppTemplateFactory extends TemplateFactory
{
	use DiFactoryTrait;

	#region TemplateFactory

	/**
	 * @param TemplateOptions $options めっちゃかえるで。
	 */
	public function createTemplate(TemplateOptions $options): TemplateBase
	{
		$customOptions = $options;
		if ($options instanceof AppTemplateOptions) {
			/** @var AppConfiguration */
			$config = $this->container->get(AppConfiguration::class);

			$customOptions = new TemplateOptions(
				Path::combine($config->baseDirectoryPath, 'App', 'Views'),
				$options->controllerName,
				$options->urlHelper,
				$config->setting->cache->template
			);
		}

		return parent::createTemplate($customOptions);
	}

	#endregion
}
