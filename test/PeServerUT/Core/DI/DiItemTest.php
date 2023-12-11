<?php

declare(strict_types=1);

namespace PeServerUT\Core\DI;

use stdClass;
use Throwable;
use TypeError;
use PeServer\Core\DI\DiItem;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class DiItemTest extends TestClass
{
	public function test_constructor()
	{
		new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, self::class);
		new DiItem(DiItem::LIFECYCLE_SINGLETON, DiItem::TYPE_VALUE, ['k' => 'v']);
		new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_FACTORY, fn () => new self(__FILE__));
		$this->success();
	}

	public static function provider_constructor_throw()
	{
		return [
			[ArgumentException::class, -1, DiItem::TYPE_TYPE, self::class],
			[ArgumentException::class, 2, DiItem::TYPE_TYPE, self::class],
			[NotSupportedException::class, DiItem::LIFECYCLE_TRANSIENT, -1, self::class],
			[NotSupportedException::class, DiItem::LIFECYCLE_TRANSIENT, 3, self::class],
			[TypeError::class, DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, 123],
			[ArgumentException::class, DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, '  '],
			[ArgumentException::class, DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_VALUE, '  '],
			[TypeError::class, DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_FACTORY, 'func!'],
		];
	}

	#[DataProvider('provider_constructor_throw')]
	public function test_constructor_throw($exception, $lifecycle, $type, $data)
	{
		$this->expectException($exception);
		new DiItem($lifecycle, $type, $data);
		$this->fail();
	}

	public function test_get_set_SingletonValue_transient()
	{
		$item = new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, self::class);
		$this->assertFalse($item->hasSingletonValue());

		try {
			$item->getSingletonValue();
		} catch (NotSupportedException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$item->setSingletonValue('AAA');
		} catch (NotSupportedException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		$this->assertFalse($item->hasSingletonValue());
	}

	public function test_get_set_SingletonValue_singleton()
	{
		$item = new DiItem(DiItem::LIFECYCLE_SINGLETON, DiItem::TYPE_TYPE, self::class);
		$this->assertFalse($item->hasSingletonValue());

		try {
			$item->getSingletonValue();
		} catch (InvalidOperationException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$item->setSingletonValue(new stdClass());
		} catch (TypeError $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}
		$this->assertFalse($item->hasSingletonValue());

		try {
			$item->setSingletonValue($this);
		} catch (InvalidOperationException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}
		$this->assertTrue($item->hasSingletonValue());

		try {
			$item->setSingletonValue(new self(__FILE__));
		} catch (InvalidOperationException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}
		$this->assertTrue($item->hasSingletonValue());

		$obj = $item->getSingletonValue();
		$this->assertSame($this, $obj);
	}
}
