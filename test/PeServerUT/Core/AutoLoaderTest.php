<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use Error;
use PeServer\Core\Archive\Archiver;
use PeServer\Core\AutoLoader;
use PeServer\Core\Binary;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ExpectationFailedException;

class AutoLoaderTest extends TestClass
{
	/**
	 * @SuppressWarnings("php:S1848")
	 */
	public function test_constructor_empty()
	{
		new AutoLoader();
		$this->success();
	}

	/**
	 * @SuppressWarnings("php:S1848")
	 */
	public function test_constructor()
	{
		new AutoLoader([
			'Namespace' => [
				'directory' => __DIR__
			]
		]);
		$this->success();
	}

	public function test_set()
	{
		$autoLoader = new AutoLoader();
		$autoLoader->set('Namespace', [
			'directory' => __DIR__
		]);
		$this->success();
	}

	public function test_set_rootNamespace_1()
	{
		$autoLoader = new AutoLoader();
		$autoLoader->set('\\', [
			'directory' => __DIR__
		]);
		$this->success();
	}
	public function test_set_rootNamespace_2()
	{
		$autoLoader = new AutoLoader();
		$autoLoader->set('', [
			'directory' => __DIR__
		]);
		$this->success();
	}

	public function test_findIncludeFile_rootNamespace()
	{
		$autoLoader = new LocalAutoLoader();
		$autoLoader->set('', [
			'directory' => dirname(__DIR__, 2)
		]);

		$this->assertSame(
			__FILE__,
			$autoLoader->findIncludeFile('PeServerUT\\Core\\AutoLoaderTest')
		);
	}

	public function test_findIncludeFile_namespace()
	{
		$autoLoader = new LocalAutoLoader();
		$autoLoader->set('PeServerUT', [
			'directory' => dirname(__DIR__)
		]);

		$this->assertSame(
			__FILE__,
			$autoLoader->findIncludeFile('PeServerUT\\Core\\AutoLoaderTest')
		);
	}

	public function test_findIncludeFile_includes()
	{
		$baseDirectory = dirname(__DIR__, 3);
		$autoLoader = new LocalAutoLoader();
		$autoLoader->add('Alias', [
			'directory' => $baseDirectory,
			'includes' => [
				'Alias\\MappedAutoLoader' => 'PeServer\\Core\\AutoLoader'
			]
		]);
		$autoLoader->add('PeServer\\Core', [
			'directory' => $baseDirectory . DIRECTORY_SEPARATOR . 'PeServer' . DIRECTORY_SEPARATOR . 'Core'
		]);

		$this->assertSame(
			$baseDirectory . DIRECTORY_SEPARATOR . 'PeServer' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'AutoLoader.php',
			$autoLoader->findIncludeFile('Alias\\MappedAutoLoader')
		);
	}

	public function test_set_empty_dir_throw()
	{
		$autoLoader = new AutoLoader();
		$this->expectException(Error::class);
		$autoLoader->set('', [
			'directory' => ''
		]);
	}

	public function test_set_includes_empty_alias_throw()
	{
		$autoLoader = new AutoLoader();
		$this->expectException(Error::class);
		$autoLoader->set('', [
			'directory' => __DIR__,
			'includes' => [
				'' => 'class'
			]
		]);
	}

	public function test_set_includes_dup_alias_throw()
	{
		$autoLoader = new AutoLoader();
		$this->expectException(Error::class);
		$autoLoader->set('', [
			'directory' => __DIR__,
			'includes' => [
				'alias' => 'class',
				' alias ' => 'class',
			]
		]);
	}

	public function test_set_includes_empty_include_throw()
	{
		$autoLoader = new AutoLoader();
		$this->expectException(Error::class);
		$autoLoader->set('', [
			'directory' => __DIR__,
			'includes' => [
				'class' => ''
			]
		]);
	}

	public function test_get_notFound()
	{
		$autoLoader = new AutoLoader();
		$actual = $autoLoader->get('test');
		$this->assertNull($actual);
	}

	public function test_set_get()
	{
		$autoLoader = new AutoLoader();
		$autoLoader->set('test', [
			'directory' => 'dir',
		]);

		$actual = $autoLoader->get('test');

		$this->assertSame('dir' . DIRECTORY_SEPARATOR, $actual['directory']);
		$this->assertEmpty($actual['includes']);
		$this->assertSame(['.php'], $actual['extensions']);
	}

	public function test_set_extensions()
	{
		$autoLoader = new AutoLoader();
		$autoLoader->set('', [
			'directory' => __DIR__,
			'extensions' => [
				'php',
				'.php',
				'inc',
			]
		]);

		$actual = $autoLoader->get('');
		$this->assertSame(__DIR__ . DIRECTORY_SEPARATOR, $actual['directory']);
		$this->assertEmpty($actual['includes']);
		$this->assertSame(['.php', '.inc'], $actual['extensions']);
	}

	public static function provider_add_throw()
	{
		return [
			['Namespace', 'Namespace'],

			['Namespace\\', 'Namespace'],
			['\\Namespace', 'Namespace'],
			['\\Namespace\\', 'Namespace'],

			['Namespace', 'Namespace\\'],
			['Namespace', '\\Namespace'],
			['Namespace', '\\Namespace\\'],

			['Namespace\\', 'Namespace\\'],
			['\\Namespace', '\\Namespace'],
			['\\Namespace\\', '\\Namespace\\'],
		];
	}
	#[DataProvider('provider_add_throw')]
	public function test_add_throw(string $first, string $second)
	{
		$autoLoader = new AutoLoader();
		$autoLoader->add($first, [
			'directory' => __DIR__,
		]);

		$this->expectException(Error::class);
		$autoLoader->add($second, [
			'directory' => __DIR__,
		]);
	}
}

class LocalAutoLoader extends AutoLoader
{
	public function findIncludeFile(string $fullName): ?string
	{
		return parent::findIncludeFile($fullName);
	}
}
