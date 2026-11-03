<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages;

use PeServer\Core\Environment;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\Logic\Validator;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;

class ShowErrorMessagesTest extends TestClass
{
	private function render(ShowErrorMessagesProps $props): string
	{
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$this->createStub(Environment::class),
			new WebSecurity(),
			new HtmlBuilder()
		);
		$component = new ShowErrorMessages($props, $context);

		return $component->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
	}

	public function test_empty(): void
	{
		$this->assertSame('', $this->render(new ShowErrorMessagesProps([])));
		$this->assertSame('', $this->render(new ShowErrorMessagesProps([], 'email')));
	}

	public function test_common(): void
	{
		$title = htmlspecialchars(I18n::message(I18n::COMMON_ERROR_TITLE), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
		$actual = $this->render(new ShowErrorMessagesProps(['<message>', 'second'], Validator::COMMON));

		$this->assertSame(
			'<div class="common error"><p>' . $title . '</p><ul class="errors common-error"><li class="error">&lt;message&gt;</li><li class="error">second</li></ul></div>',
			$actual
		);
	}

	public function test_field(): void
	{
		$actual = $this->render(new ShowErrorMessagesProps(['<message>'], 'email'));

		$this->assertSame('<ul class="errors value-error"><li class="error">&lt;message&gt;</li></ul>', $actual);
	}
}
