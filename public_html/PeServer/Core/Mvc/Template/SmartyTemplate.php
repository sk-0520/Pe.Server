<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

require_once(__DIR__ . '/../../../Core/Libs/smarty/libs/Smarty.class.php');

use \Smarty;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Template\Plugin\AssetFunction;
use PeServer\Core\Mvc\Template\Plugin\BotTextImageFunction;
use PeServer\Core\Mvc\Template\Plugin\CsrfFunction;
use PeServer\Core\Mvc\Template\Plugin\InputHelperFunction;
use PeServer\Core\Mvc\Template\Plugin\ITemplateBlockFunction;
use PeServer\Core\Mvc\Template\Plugin\ITemplateFunction;
use PeServer\Core\Mvc\Template\Plugin\MarkdownFunction;
use PeServer\Core\Mvc\Template\Plugin\ShowErrorMessagesFunction;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Template\Plugin\CodeFunction;
use PeServer\Core\Store\Stores;
use PeServer\Core\Throws\NotImplementedException;

class SmartyTemplate extends TemplateBase
{
	/**
	 * テンプレートエンジン。
	 */
	private Smarty $engine;

	public function __construct(TemplateOptions $options, protected Stores $stores)
	{
		parent::__construct($options);

		$this->engine = new Smarty();

		$this->engine->addTemplateDir(Path::combine($this->options->rootDirectoryPath, $this->options->baseDirectoryName));
		$this->engine->addTemplateDir($this->options->rootDirectoryPath);
		$this->engine->setCompileDir(Path::combine($this->options->temporaryDirectoryPath, 'compile', $this->options->baseDirectoryName));
		$this->engine->setCacheDir(Path::combine($this->options->temporaryDirectoryPath, 'cache', $this->options->baseDirectoryName));

		$this->engine->escape_html = true;

		$this->registerPlugins();
	}

	private function applyParameter(TemplateParameter $parameter): void
	{
		// @phpstan-ignore-next-line
		$this->engine->assign([
			'status' => $parameter->httpStatus,
			'values' => $parameter->values,
			'errors' => $parameter->errors,
			'stores' => [
				'cookie' => TemplateStore::createCookie($this->stores->cookie),
				'session' => TemplateStore::createSession($this->stores->session),
				'temporary' => TemplateStore::createTemporary($this->stores->temporary),
			],
		]);
	}

	public function build(string $templateName, TemplateParameter $parameter): string
	{
		$this->applyParameter($parameter);
		// @phpstan-ignore-next-line
		return $this->engine->fetch($templateName);
	}

	private function registerPlugins(): void
	{
		$argument = new TemplatePluginArgument(
			$this->engine,
			$this->options->rootDirectoryPath,
			Path::combine($this->options->rootDirectoryPath, $this->options->baseDirectoryName),
			$this->options->urlHelper,
			$this->stores
		);
		$showErrorMessagesFunction = new ShowErrorMessagesFunction($argument);
		/** @var array<ITemplateFunction> */
		$plugins = [
			new CsrfFunction($argument),
			new AssetFunction($argument),
			$showErrorMessagesFunction,
			new InputHelperFunction($argument, $showErrorMessagesFunction),
			new BotTextImageFunction($argument),
			new MarkdownFunction($argument),
			new CodeFunction($argument),
		];
		foreach ($plugins as $plugin) {
			if ($plugin instanceof ITemplateBlockFunction) {
				// @phpstan-ignore-next-line
				$this->engine->registerPlugin('block', $plugin->getFunctionName(), [$plugin, 'functionBlockBody']);
			} else if ($plugin instanceof ITemplateFunction) { // @phpstan-ignore-line 増えたとき用にelseしたくないのである
				// @phpstan-ignore-next-line
				$this->engine->registerPlugin('function', $plugin->getFunctionName(), [$plugin, 'functionBody']);
			} else { //@phpstan-ignore-line
				throw new NotImplementedException();
			}
		}
	}
}
