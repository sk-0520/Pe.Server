<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\InputHelper;

use DOMDocument;
use DOMXPath;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class InputHelperTest extends TestClass
{
	private function render(InputHelperProps $props): DOMXPath
	{
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$this->createStub(Environment::class),
			new WebSecurity(),
			new HtmlBuilder()
		);
		$html = (new InputHelper($props, $context))->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
		$dom = new DOMDocument();
		$this->assertTrue($dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>'));
		return new DOMXPath($dom);
	}

	public static function provider_input(): array
	{
		return [
			[InputHelperType::Text, false, true],
			[InputHelperType::Email, false, true],
			[InputHelperType::Password, false, true],
			[InputHelperType::Url, false, true],
			[InputHelperType::Textarea, false, true],
			[InputHelperType::Text, true, true],
			[InputHelperType::Email, true, true],
			[InputHelperType::Password, true, true],
			[InputHelperType::Url, true, true],
			[InputHelperType::Textarea, true, true],
			[InputHelperType::Textarea, true, false],
		];
	}

	#[DataProvider('provider_input')]
	public function test_input(InputHelperType $type, bool $hasError, bool $autoError): void
	{
		$value = "first <&\"</textarea>\nsecond";
		$parameter = new TemplateParameter(HttpStatus::OK, ['field' => $value], [
			'field' => $hasError ? ['invalid <field>'] : [],
			'other' => ['other error'],
		]);
		$xpath = $this->render(new InputHelperProps(
			parameter: $parameter,
			key: 'field',
			type: $type,
			className: 'edit markdown-editor',
			required: true,
			data: new DataAttributes(['markdown-result' => '.markdown-browser']),
			autoError: $autoError,
		));
		$target = $type === InputHelperType::Textarea ? '//textarea' : '//input[@type="' . $type->value . '"]';
		$this->assertSame(1, $xpath->query($target . '[@id="field"][@name="field"][@required]')->length);
		$this->assertSame($value, $xpath->evaluate('string(' . $target . ($type === InputHelperType::Textarea ? ')' : '/@value)')));
		$this->assertSame($hasError ? 'edit markdown-editor error' : 'edit markdown-editor', $xpath->evaluate('string(' . $target . '/@class)'));
		$this->assertSame('.markdown-browser', $xpath->evaluate('string(' . $target . '/@data-markdown-result)'));
		$this->assertSame($hasError && $autoError ? 1 : 0, $xpath->query('//ul[@class="errors value-error"]/li')->length);
		if ($hasError && $autoError) {
			$this->assertSame('invalid <field>', $xpath->evaluate('string(//ul/li)'));
		}
	}

	public function test_missing_value_and_empty_class(): void
	{
		$xpath = $this->render(new InputHelperProps(
			new TemplateParameter(HttpStatus::OK, [], ['field' => ['error']]),
			'field',
			InputHelperType::Text,
		));
		$this->assertSame('', $xpath->evaluate('string(//input/@value)'));
		$this->assertSame('error', $xpath->evaluate('string(//input/@class)'));
		$this->assertSame(0, $xpath->query('//input[@required]')->length);
	}
}
