<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Environment;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Template\Plugin\AssetFunction;
use PeServer\Core\Mvc\Template\Plugin\BotTextImageFunction;
use PeServer\Core\Mvc\Template\Plugin\CodeFunction;
use PeServer\Core\Mvc\Template\Plugin\CsrfFunction;
use PeServer\Core\Mvc\Template\Plugin\DumpModifier;
use PeServer\Core\Mvc\Template\Plugin\InputHelperFunction;
use PeServer\Core\Mvc\Template\Plugin\ITemplateBlockFunction;
use PeServer\Core\Mvc\Template\Plugin\ITemplateFunction;
use PeServer\Core\Mvc\Template\Plugin\ITemplateModifier;
use PeServer\Core\Mvc\Template\Plugin\MarkdownFunction;
use PeServer\Core\Mvc\Template\Plugin\PagerFunction;
use PeServer\Core\Mvc\Template\Plugin\ShowErrorMessagesFunction;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Mvc\Template\Plugin\TimestampFunction;
use PeServer\Core\Mvc\Template\TemplateBase;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Template\TemplateStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Throws\NotImplementedException;
use Smarty\Smarty;

class SmartyTemplate extends TemplateBase
{
	#region variable

	/**
	 * テンプレートエンジン。
	 */
	private Smarty $engine;

	#endregion

	public function __construct(
		TemplateOptions $options,
		protected Stores $stores,
		protected Environment $environment
	) {
		parent::__construct($options);

		$this->engine = new Smarty();

		$this->engine->addTemplateDir(Path::combine($this->options->rootDirectoryPath, $this->options->baseDirectoryName));
		$this->engine->addTemplateDir($this->options->rootDirectoryPath);
		$this->engine->setCompileDir(Path::combine($this->options->temporaryDirectoryPath, 'compile', $this->options->baseDirectoryName));
		$this->engine->setCacheDir(Path::combine($this->options->temporaryDirectoryPath, 'cache', $this->options->baseDirectoryName));

		$this->engine->escape_html = true;

		$this->registerPlugins();
	}

	#region function

	private function applyParameter(TemplateParameter $parameter): void
	{
		$this->engine->assign([
			'status' => $parameter->httpStatus,
			'values' => $parameter->values,
			'errors' => $parameter->errors,
			'stores' => [
				'cookie' => TemplateStore::createCookie($this->stores->cookie),
				'session' => TemplateStore::createSession($this->stores->session),
				'temporary' => TemplateStore::createTemporary($this->stores->temporary),
			],
			'environment' => $this->environment
		]);
	}

	public function build(string $templateName, TemplateParameter $parameter): string
	{
		$this->applyParameter($parameter);
		return $this->engine->fetch($templateName);
	}

	private function registerPlugins(): void
	{
		$argument = new TemplatePluginArgument(
			$this->engine,
			$this->options->rootDirectoryPath,
			Path::combine($this->options->rootDirectoryPath, $this->options->baseDirectoryName),
			$this->options->programContext,
			$this->options->urlHelper,
			$this->options->webSecurity,
			$this->stores,
			$this->environment
		);
		$showErrorMessagesFunction = new ShowErrorMessagesFunction($argument);
		/** @var array<ITemplateFunction|ITemplateModifier> */
		$plugins = [
			new CsrfFunction($argument),
			new AssetFunction($argument),
			$showErrorMessagesFunction,
			new InputHelperFunction($argument, $showErrorMessagesFunction),
			new BotTextImageFunction($argument),
			new MarkdownFunction($argument),
			new CodeFunction($argument),
			new PagerFunction($argument),
			new DumpModifier($argument),
			new TimestampFunction($argument),
		];
		foreach ($plugins as $plugin) {
			// 関数は重複できない
			if ($plugin instanceof ITemplateBlockFunction) {
				$this->engine->registerPlugin(Smarty::PLUGIN_BLOCK, $plugin->getFunctionName(), [$plugin, 'functionBlockBody']);
			} elseif ($plugin instanceof ITemplateFunction) {
				$this->engine->registerPlugin(Smarty::PLUGIN_FUNCTION, $plugin->getFunctionName(), [$plugin, 'functionBody']);
			}

			if ($plugin instanceof ITemplateModifier) {
				$this->engine->registerPlugin(Smarty::PLUGIN_MODIFIER, $plugin->getModifierName(), [$plugin, 'modifierBody']);
			}
		}
	}

	#endregion
}
