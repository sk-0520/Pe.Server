<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use Override;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\NodeComponentFactory;

/**
 * 移行期間用: Node コンポーネントが登録されたテンプレートは Node、それ以外は Smarty で描画する。
 */
class EngineMigrationTemplate extends TemplateBase
{
	/**
	 * 生成。
	 */
	public function __construct(
		TemplateOptions $options,
		protected IDiContainer $container
	) {
		parent::__construct($options);
	}

	#region TemplateBase

	#[Override]
	public function build(string $templateName, TemplateParameter $parameter): string
	{
		if (is_subclass_of($templateName, ComponentBase::class)) {
			$template = $this->container->new(NodeTemplate::class, [
				TemplateOptions::class => $this->options,
				NodeComponentFactory::class => new NodeComponentFactory($this->container),
			]);
			return $template->build($templateName, $parameter);
		}

		$template = $this->container->new(SmartyTemplate::class, [TemplateOptions::class => $this->options]);
		return $template->build($templateName . '.tpl', $parameter);
	}

	#endregion
}
