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

	#region TemplateFactory

	#[Override]
	public function createTemplate(TemplateOptions $options): ITemplate
	{
		return new EngineMigrationTemplate($options, $this->container);
	}

	#endregion
}
