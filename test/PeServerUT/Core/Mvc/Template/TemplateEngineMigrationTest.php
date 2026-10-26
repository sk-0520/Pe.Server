<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template;

use Override;
use PeServer\Core\DI\DiRegisterContainer;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\PageProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\NodeTemplate;
use PeServer\Core\Mvc\Template\TemplateEngineMigrationFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;

/**
 * @extends HtmlComponentBase<PageProps>
 */
class LocalMigrationPage extends HtmlComponentBase
{
	public function __construct(PageProps $props, HtmlBuilder $builder)
	{
		parent::__construct($props, $builder);
	}

	#[Override]
	protected function getNode(): INode
	{
		$value = $this->props->parameter->values['name'];
		return $this->builder->div(new HtmlGlobalAttributes(), new TextNode($value));
	}
}

class TemplateEngineMigrationTest extends TestClass
{
	private function createOptions(): TemplateOptions
	{
		return new TemplateOptions(
			'root',
			'base',
			$this->createStub(ProgramContext::class),
			$this->createStub(IUrlHelper::class),
			$this->createStub(WebSecurity::class),
			'tmp'
		);
	}

	public function test_node_template_build(): void
	{
		$container = new DiRegisterContainer();
		$container->registerValue($this->createStub(Stores::class), Stores::class);
		$container->registerValue($this->createStub(Environment::class), Environment::class);

		$factory = new TemplateEngineMigrationFactory($container);
		$factory->registerComponent('base', 'page.tpl', LocalMigrationPage::class);

		$template = $factory->createTemplate($this->createOptions());
		$actual = $template->build('page.tpl', new TemplateParameter(HttpStatus::OK, ['name' => '<a>'], []));

		$this->assertSame('<!DOCTYPE html>' . PHP_EOL . '<div>&lt;a&gt;</div>', $actual);
	}

	public function test_toKey(): void
	{
		$this->assertSame('base/page.tpl', TemplateEngineMigrationFactory::toKey($this->createOptions(), 'page.tpl'));
	}
}
