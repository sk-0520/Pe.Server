<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\PluginState;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServerTest\ItControllerClass;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class PluginControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	private const ID = '12345678-1234-4234-8234-123456789abc';
	private const OTHER_ID = '12345678-1234-4234-8234-123456789def';

	private function plugin(string $id, string $name, string $displayName, string $state = PluginState::ENABLED): PluginCacheItem
	{
		return new PluginCacheItem($id, 'owner', $name, $displayName, $state, '**description** & text', [], ['a', 'b']);
	}

	private function setupCache(?PluginCache $data): callable
	{
		return function (ItSetup $setup) use ($data): void {
			$cache = $data === null
				? $this->createMock(AppDatabaseCache::class)
				: $this->createStub(AppDatabaseCache::class);
			$cache->method('existsPluginInformation')->willReturn($data !== null);
			if ($data !== null) {
				$cache->method('readPluginInformation')->willReturn($data);
			} else {
				$cache->expects($this->never())->method('readPluginInformation');
			}
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($cache, AppDatabaseCache::class);
		};
	}

	public static function provider_index(): array
	{
		return [['/plugin']];
	}

	#[DataProvider('provider_index')]
	public function test_index_sorted_and_escaped(string $path): void
	{
		$actual = $this->call(HttpMethod::Get, $path, new ItOptions(), $this->setupCache(new PluginCache([], [
			$this->plugin(self::ID, 'z-plugin', 'First <plugin>'),
			$this->plugin(self::OTHER_ID, 'a-plugin', 'Second & plugin'),
		])));
		$this->assertStatusOK($actual);
		$this->assertTitle('プラグイン', $actual);
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='node-template-indicator']"));
		$this->assertTextNode('Second & plugin', $actual->html->path()->collections("//*[@id='content']/ul/li[1]/a[@href='/plugin/" . self::OTHER_ID . "']")->single());
		$this->assertTextNode('First <plugin>', $actual->html->path()->collections("//*[@id='content']/ul/li[2]/a")->single());
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']//plugin"));
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']//a[@href='/management/default-plugin']"));
	}

	public function test_index_missing_cache(): void
	{
		$actual = $this->call(HttpMethod::Get, '/plugin', new ItOptions(), $this->setupCache(null));
		$this->assertStatusOK($actual);
		$this->assertVisibleCommonError(['プラグインなし', 'この状態は原則ありえない(標準プラグインすら未登録状態)'], $actual);
		$this->assertTextNode('標準プラグイン', $actual->html->path()->collections("//*[@id='content']/p/a[@href='/management/default-plugin']")->single());
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']/ul/li"));
	}

	public function test_index_empty_cache(): void
	{
		$actual = $this->call(HttpMethod::Get, '/plugin', new ItOptions(), $this->setupCache(new PluginCache([], [])));
		$this->assertStatusOK($actual);
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']/ul/li"));
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']//a[@href='/management/default-plugin']"));
	}

	public static function provider_detail(): array
	{
		return [
			['/plugin/', PluginState::ENABLED, true],
			['/plugin/', PluginState::DISABLED, false],
			['/plugin/', PluginState::RESERVED, false],
			['/plugin/', PluginState::CHECK_FAILED, false],
		];
	}

	#[DataProvider('provider_detail')]
	public function test_detail(string $base, string $state, bool $hasCategories): void
	{
		$categories = $hasCategories ? [
			new PluginCacheCategory('a', 'Z category', ''),
			new PluginCacheCategory('other', 'Not mapped', ''),
			new PluginCacheCategory('b', 'A <category>', ''),
		] : [];
		$actual = $this->call(HttpMethod::Get, $base . strtoupper(self::ID), new ItOptions(), $this->setupCache(new PluginCache(
			$categories, [$this->plugin(self::ID, 'internal<&"', 'Display <plugin>', $state)]
		)));
		$this->assertStatusOK($actual);
		$this->assertTitle('プラグイン: Display <plugin>', $actual);
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='node-template-indicator']"));
		$this->assertTextNode(self::ID, $actual->html->path()->collections("//dt[text()='プラグインID']/following-sibling::dd[1]/code[@data-clipboard='inline']")->single());
		$this->assertTextNode('internal<&"', $actual->html->path()->collections("//dt[text()='プラグイン内部名']/following-sibling::dd[1]/code[@data-clipboard='inline']")->single());
		$this->assertTextNode('Display <plugin>', $actual->html->path()->collections("//dt[text()='プラグイン表示名']/following-sibling::dd[1]")->single());
		$this->assertTextNode(PluginState::toString($state), $actual->html->path()->collections("//dt[text()='状態']/following-sibling::dd[1]")->single());
		$this->assertTextNode('description', $actual->html->path()->collections("//dt[text()='説明']/following-sibling::dd[1]/section[@class='markdown']//strong")->single());
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']//plugin"));
		if ($hasCategories) {
			$this->assertTextNode('A <category>', $actual->html->path()->collections("//ul[@class='inline']/li[1]")->single());
			$this->assertTextNode('Z category', $actual->html->path()->collections("//ul[@class='inline']/li[2]")->single());
			$this->assertCount(2, $actual->html->path()->collections("//ul[@class='inline']/li"));
		} else {
			$this->assertTextNode('プラグインカテゴリ未登録', $actual->html->path()->collections("//dt[text()='カテゴリ']/following-sibling::dd[1]/span[@class='mute']")->single());
		}

	}

	public function test_canonical_routes_use_node(): void
	{
		$data = new PluginCache([], [$this->plugin(self::ID, 'internal', 'Display name')]);
		$index = $this->call(HttpMethod::Get, '/plugin', new ItOptions(), $this->setupCache($data));
		$this->assertStatusOK($index);
		$this->assertTitle('プラグイン', $index);
		$this->assertCount(0, $index->html->path()->collections("//*[@id='node-template-indicator']"));
		$this->assertTextNode('Display name', $index->html->path()->collections("//*[@id='content']/ul/li/a[@href='/plugin/" . self::ID . "']")->single());

		$detail = $this->call(HttpMethod::Get, '/plugin/' . self::ID, new ItOptions(), $this->setupCache($data));
		$this->assertStatusOK($detail);
		$this->assertTitle('プラグイン: Display name', $detail);
		$this->assertCount(0, $detail->html->path()->collections("//*[@id='node-template-indicator']"));
		$this->assertTextNode('internal', $detail->html->path()->collections("//code[@data-clipboard='inline'][text()='internal']")->single());
	}
}
