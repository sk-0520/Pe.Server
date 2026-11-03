<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\Asset;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\Html\Components\Asset\Asset;
use PeServer\Core\Mvc\Template\Node\Html\Components\Asset\AssetProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class AssetTest extends TestClass
{
	private function render(AssetProps $props, bool $production): string
	{
		$environment = $this->createStub(Environment::class);
		$environment->method('isProduction')->willReturn($production);
		$environment->method('getRevision')->willReturn('revision');
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$environment,
			new WebSecurity(),
			new HtmlBuilder()
		);
		return (new Asset($props, $context))->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
	}

	public static function provider_build(): array
	{
		return [
			[
				new AssetProps('https://example.test/style.css'),
				'<link href="https://example.test/style.css" rel="stylesheet" type="text/css" />',
			],
			[
				new AssetProps('https://example.test/script.js'),
				'<script src="https://example.test/script.js"></script>',
			],
			[
				new AssetProps('https://example.test/favicon.svg', rel: Rel::Icon),
				'<link href="https://example.test/favicon.svg" rel="icon" type="image/svg+xml" />',
			],
			[
				new AssetProps('https://example.test/image.png', alt: 'image', width: 10, height: 20),
				'<img alt="image" height="20" src="https://example.test/image.png" width="10" />',
			],
			[new AssetProps(''), ''],
		];
	}

	#[DataProvider('provider_build')]
	public function test_build(AssetProps $props, string $expected): void
	{
		$this->assertSame($expected, $this->render($props, true));
	}

	public function test_development(): void
	{
		$actual = $this->render(new AssetProps('/styles/style.css'), false);

		$this->assertStringContainsString('<!--', $actual);
		$this->assertStringContainsString(AssetProps::class, $actual);
		$this->assertStringEndsWith('<link href="/styles/style.css?revision" rel="stylesheet" type="text/css" />', $actual);
	}
}
