<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\BotTextImage;

use DOMDocument;
use DOMXPath;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Components\BotTextImage\BotTextImage;
use PeServer\Core\Mvc\Template\Node\Html\Components\BotTextImage\BotTextImageProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class BotTextImageTest extends TestClass
{
	public static function provider_image(): array
	{
		return [
			[new BotTextImageProps('0123'), 100, 100],
			[new BotTextImageProps('0123', 'token<&"', 56, 28, 12, '#eeeeeeff', '#0c0c0c', 'token'), 56, 28],
		];
	}

	#[DataProvider('provider_image')]
	public function test_image(BotTextImageProps $props, int $width, int $height): void
	{
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$this->createStub(Environment::class),
			new WebSecurity(),
			new HtmlBuilder()
		);
		$node = new BotTextImage($props, $context);
		$html = $node->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
		$this->assertSame($html, $node->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, '')));
		$dom = new DOMDocument();
		$this->assertTrue($dom->loadHTML($html));
		$xpath = new DOMXPath($dom);
		$this->assertSame((string)$width, $xpath->evaluate('string(//img/@width)'));
		$this->assertSame((string)$height, $xpath->evaluate('string(//img/@height)'));
		$this->assertSame(hash('sha256', $props->text), $xpath->evaluate('string(//img/@data-hash)'));
		$this->assertSame($props->alt, $xpath->evaluate('string(//img/@alt)'));
		$this->assertSame($props->className, $xpath->evaluate('string(//img/@class)'));
		$source = $xpath->evaluate('string(//img/@src)');
		$this->assertStringStartsWith('data:image/png;base64,', $source);
		$binary = base64_decode(substr($source, strlen('data:image/png;base64,')), true);
		$this->assertNotFalse($binary);
		$info = getimagesizefromstring($binary);
		$this->assertNotFalse($info);
		$this->assertSame($width, $info[0]);
		$this->assertSame($height, $info[1]);
		$this->assertSame(IMAGETYPE_PNG, $info[2]);
	}
}
