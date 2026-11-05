<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template;

use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\NodeTemplate;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;

class TemplateFactoryTest extends TestClass
{
	private function options(): TemplateOptions
	{
		return new TemplateOptions(
			'',
			'',
			new ProgramContext('', '', ''),
			new UrlHelper(''),
			new WebSecurity(),
			''
		);
	}

	private function factory(TemplateOptions $options): TemplateFactory
	{
		$container = $this->container();
		$container->registerValue($this->createStub(Stores::class), Stores::class);
		$container->registerValue($this->createStub(Environment::class), Environment::class);
		$container->registerValue($options->programContext, ProgramContext::class);
		$container->registerValue($options->urlHelper, IUrlHelper::class);
		$container->registerValue($options->webSecurity, WebSecurity::class);

		return new TemplateFactory($container);
	}

	public function test_factory_builds_node_template(): void
	{
		$options = $this->options();
		$template = $this->factory($options)->createTemplate($options);

		$this->assertInstanceOf(NodeTemplate::class, $template);
		$this->assertSame(
			'<!DOCTYPE html>' . PHP_EOL . '<p>&lt;value&gt;</p>',
			$template->build(LocalTemplatePage::class, new TemplateParameter(HttpStatus::OK, ['text' => '<value>'], []))
		);
	}

	public function test_core_view_passes_component_class_without_extension(): void
	{
		$options = $this->options();
		$result = new ViewActionResult(
			'page',
			LocalTemplatePage::class,
			new TemplateParameter(HttpStatus::Created, ['text' => 'created'], []),
			['X-Test' => ['node']],
			$options->programContext,
			$this->factory($options),
			$options->urlHelper,
			$options->webSecurity
		);

		$response = $result->createResponse();

		$this->assertSame(HttpStatus::Created, $response->status);
		$this->assertSame('<!DOCTYPE html>' . PHP_EOL . '<p>created</p>', $response->content);
		$this->assertSame(['node'], $response->header->getValues('X-Test'));
	}
}

/**
 * @extends HtmlComponentBase<RootProps<HtmlNodeContext>>
 */
class LocalTemplatePage extends HtmlComponentBase
{
	public function __construct(RootProps $props, HtmlNodeContext $context)
	{
		parent::__construct($props, $context);
	}

	protected function getNode(): INode
	{
		return $this->context->builder->p(
			new HtmlGlobalAttributes(),
			new TextNode($this->props->parameter->values['text'])
		);
	}
}
