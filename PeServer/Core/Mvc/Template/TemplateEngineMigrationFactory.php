<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use Override;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\PageProps;

/**
 * 移行期間用: Smarty と Node を共存させるファクトリ。
 *
 * `registerComponent` したテンプレートのみ Node で描画し、未登録は Smarty のまま。
 * ファクトリは都度生成されるため、登録は継承クラスのコンストラクタ内で行うこと。
 * 移行完了後はこのクラスと `EngineMigrationTemplate` を削除する。
 */
class TemplateEngineMigrationFactory extends TemplateFactory
{
	use DiFactoryTrait;

	#region variable

	/**
	 * @var array<string,class-string>
	 */
	private array $components = [];

	#endregion

	#region function

	/**
	 * テンプレートキーを生成。
	 *
	 * @param TemplateOptions $options
	 * @param string $templateName `ITemplate::build` に渡されるテンプレート名。
	 */
	public static function toKey(TemplateOptions $options, string $templateName): string
	{
		return $options->baseDirectoryName . '/' . $templateName;
	}

	/**
	 * Node で描画するテンプレートを登録。
	 *
	 * @param string $baseDirectoryName `TemplateOptions::$baseDirectoryName`
	 * @param string $templateName 例: `index.tpl`
	 * @param class-string<ComponentBase<PageProps>> $component
	 */
	public function registerComponent(string $baseDirectoryName, string $templateName, string $component): void
	{
		$this->components[$baseDirectoryName . '/' . $templateName] = $component;
	}

	#endregion

	#region TemplateFactory

	#[Override]
	public function createTemplate(TemplateOptions $options): ITemplate
	{
		return new EngineMigrationTemplate($options, $this->container, $this->components);
	}

	#endregion
}
