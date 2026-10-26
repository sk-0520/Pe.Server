<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use Override;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Mvc\Template\Node\NodeComponentFactory;

/**
 * 移行期間用: Node コンポーネントが登録されたテンプレートは Node、それ以外は Smarty で描画する。
 */
class EngineMigrationTemplate extends TemplateBase
{
	/**
	 * 生成。
	 *
	 * @param array<string,class-string> $components テンプレートキー(`baseDirectoryName/templateName`) => コンポーネントクラス名
	 */
	public function __construct(
		TemplateOptions $options,
		protected IDiContainer $container,
		protected array $components
	) {
		parent::__construct($options);
	}

	#region TemplateBase

	#[Override]
	public function build(string $templateName, TemplateParameter $parameter): string
	{
		$component = $this->components[TemplateEngineMigrationFactory::toKey($this->options, $templateName)] ?? null;
		if ($component !== null) {
			$template = $this->container->new(NodeTemplate::class, [
				TemplateOptions::class => $this->options,
				NodeComponentFactory::class => new NodeComponentFactory($this->container),
			]);
			return $template->build($component, $parameter);
		}

		$template = $this->container->new(SmartyTemplate::class, [TemplateOptions::class => $this->options]);
		return $template->build($templateName, $parameter);
	}

	#endregion
}
