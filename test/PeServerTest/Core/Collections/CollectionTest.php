<?php

declare(strict_types=1);

namespace PeServerTest\Core\Collections;

use ArrayIterator;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\Data;
use PeServerTest\TestClass;
use TypeError;

class CollectionTest extends TestClass
{
	public function test_from()
	{
		$this->assertSame([0, 1, 2], Collection::from([0, 1, 2])->toArray());
		$this->assertSame([0, 1, 2], Collection::from(Collection::from([0, 1, 2]))->toArray());
		$this->assertSame([0, 1, 2], Collection::from(new ArrayIterator([0, 1, 2]))->toArray());
	}

	public function test_range()
	{
		$this->assertSame([0, 1, 2], Collection::range(0, 3)->toArray());
		$this->assertSame([10, 11, 12], Collection::range(10, 3)->toArray());
		$this->assertSame([-3, -2, -1], Collection::range(-3, 3)->toArray());
	}

	public function test_repeat()
	{
		$this->assertSame([0, 0, 0], Collection::repeat(0, 3)->toArray());
		$this->assertSame(['A', 'A', 'A'], Collection::repeat('A', 3)->toArray());
	}

	public function test_empty()
	{
		$this->assertSame([], Collection::empty()->toArray());
		foreach (Collection::empty() as $_) {
			$this->fail();
		}
		$this->success();
	}


	public function test_where()
	{
		$expected1 = [2, 4, 6];
		$actual1 = Collection::from([1, 2, 3, 4, 5, 6])
			->where(function ($i) {
				return $i % 2 == 0;
			});
		$this->assertSame($expected1, $actual1->toArray());
		$this->assertSame($expected1, $actual1->toArray());

		$expected2 = [2, 4];
		$actual2 = $actual1
			->where(function ($i) {
				return $i < 5;
			});
		$this->assertSame($expected2, $actual2->toArray());
		$this->assertSame($expected2, $actual2->toArray());
	}

	public function test_select()
	{
		$expected1 = ['[1]', '[2]', '[3]', '[4]', '[5]', '[6]'];
		$actual1 = Collection::from([1, 2, 3, 4, 5, 6])->select(fn ($v) => "[$v]");
		$this->assertSame($expected1, $actual1->toArray());
		$this->assertSame($expected1, $actual1->toArray());
	}

	public function test_selectMany()
	{
		$expected1 = ['[1]', '[2]', '[3]', '[4]', '[5]', '[6]'];
		$actual1 = Collection::from([[1, 2, 3], [4, 5, 6]])->selectMany(fn ($v) => "[$v]");
		$this->assertSame($expected1, $actual1->toArray());
		$this->assertSame($expected1, $actual1->toArray());
	}

	public function test_selectMany_throw()
	{
		$this->expectException(TypeError::class);
		Collection::from([1, 2, 3, 4, 5, 6])->selectMany(fn ($v) => "[$v]")->toArray();
		$this->fail();
	}

	public function test_concat()
	{
		$expected1 = [10, 20, 30, -10, -20, -30];
		$expected2 = [10, 20, 30, -10, -20, -30, 1, 3, 5];
		$expected3 = [10, 20, 30, -10, -20, -30, 1, 3, 5, 2, 4, 6];

		$input1 = [10, 20, 30];
		$input2 = [-10, -20, -30];
		$input3 = Collection::from([1, 3, 5]);
		$input4 = new ArrayIterator([2, 4, 6]);

		$actual1 = Collection::from($input1)
			->concat($input2);
		$actual2 = $actual1
			->concat($input3);
		$actual3 = $actual2
			->concat($input4);
		$actualAll = Collection::from($input1)
			->concat($input2)
			->concat($input3)
			->concat($input4);

		$this->assertSame($expected1, $actual1->toArray());
		$this->assertSame($expected2, $actual2->toArray());
		$this->assertSame($expected3, $actual3->toArray());
		$this->assertSame($expected3, $actualAll->toArray());
	}

	public function test_prepend()
	{
		$this->assertSame([2, 0, 1], Collection::from([0, 1])->prepend(2)->toArray());
		$this->assertSame([3, 2, 0, 1], Collection::from([0, 1])->prepend(2)->prepend(3)->toArray());
	}

	public function test_append()
	{
		$this->assertSame([0, 1, 2], Collection::from([0, 1])->append(2)->toArray());
		$this->assertSame([0, 1, 2, 3], Collection::from([0, 1])->append(2)->append(3)->toArray());
	}

	public function test_any()
	{
		$this->assertTrue(Collection::from([1, 2, 3, 4, 5, 6])->any());
		$this->assertFalse(Collection::from([])->any());

		$this->assertTrue(Collection::from([1, 2, 3, 4, 5, 6])->any(function ($i) {
			return 6 <= $i;
		}));
		$this->assertFalse(Collection::from([1, 2, 3, 4, 5, 6])->any(function ($i) {
			return 6 < $i;
		}));
	}

	public function test_all()
	{
		$this->assertTrue(Collection::from([1, 2, 3, 4, 5, 6])->all(function ($i) {
			return $i <= 6;
		}));
		$this->assertFalse(Collection::from([1, 2, 3, 4, 5, 6])->all(function ($i) {
			return $i < 6;
		}));
	}

	public function test_count()
	{
		$this->assertSame(6, Collection::from([1, 2, 3, 4, 5, 6])->count());
		$this->assertSame(6, Collection::from(function () {
			foreach ([1, 2, 3, 4, 5, 6] as $v) yield $v;
		})->count());

		$this->assertSame(3, Collection::from([1, 2, 3, 4, 5, 6])->count(fn ($v) => ($v % 2) == 0));
		$this->assertSame(3, Collection::from(function () {
			foreach ([1, 2, 3, 4, 5, 6] as $v) yield $v;
		})->count(fn ($v) => ($v % 2) == 0));
	}

	public function test_first()
	{
		$this->assertSame(1, Collection::from([1, 2, 3, 4, 5, 6])->first());
		$this->assertSame(5, Collection::from([1, 2, 3, 4, 5, 6])->first(fn ($v) => 4 < $v));
	}

	public function test_first_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([])->first();
		$this->fail();
	}

	public function test_first_or()
	{
		$this->assertSame(1, Collection::from([1, 2, 3, 4, 5, 6])->firstOr(-1));
		$this->assertSame(5, Collection::from([1, 2, 3, 4, 5, 6])->firstOr(-1, fn ($v) => 4 < $v));
		$this->assertSame(-1, Collection::from([1, 2, 3, 4, 5, 6])->firstOr(-1, fn ($v) => 6 < $v));
	}

	public function test_last()
	{
		$this->assertSame(6, Collection::from([1, 2, 3, 4, 5, 6])->last());
		$this->assertSame(3, Collection::from([1, 2, 3, 4, 5, 6])->last(fn ($v) => $v < 4));
	}

	public function test_last_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([])->first();
		$this->fail();
	}

	public function test_last_or()
	{
		$this->assertSame(6, Collection::from([1, 2, 3, 4, 5, 6])->lastOr(-1));
		$this->assertSame(3, Collection::from([1, 2, 3, 4, 5, 6])->lastOr(-1, fn ($v) => $v < 4));
		$this->assertSame(-1, Collection::from([1, 2, 3, 4, 5, 6])->lastOr(-1, fn ($v) => $v < 0));
	}

	public function test_single_empty_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([])->single();
		$this->fail();
	}
	public function test_single_1_1()
	{
		$this->assertSame(1, Collection::from([1])->single());
	}
	public function test_single_3_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([1, 2, 3])->single();
		$this->fail();
	}

	public function test_single_callback_empty_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([])->single(fn ($v) => $v == 2);
		$this->fail();
	}
	public function test_single_3_1()
	{
		$this->assertSame(1, Collection::from([0, 1, 2, 2])->single(fn ($v) => $v === 1));
	}
	public function test_single_4_2_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([0, 1, 2, 2])->single(fn ($v) => $v === 2);
		$this->fail();
	}

	public function test_singleOr_empty()
	{
		$this->assertSame(-1, Collection::from([])->singleOr(-1));
	}
	public function test_singleOr_1_1()
	{
		$this->assertSame(1, Collection::from([1])->singleOr(-1));
	}
	public function test_singleOr_3_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([1, 2, 3])->singleOr(-1);
		$this->fail();
	}
	public function test_singleOr_callback_empty()
	{
		$this->assertSame(-1, Collection::from([])->singleOr(-1, fn ($v) => $v == 2));
	}
	public function test_singleOr_3_1()
	{
		$this->assertSame(1, Collection::from([0, 1, 2, 2])->singleOr(-1, fn ($v) => $v === 1));
	}
	public function test_singleOr_4_2_throw()
	{
		$this->expectException(InvalidOperationException::class);
		Collection::from([0, 1, 2, 2])->singleOr(-1, fn ($v) => $v === 2);
		$this->fail();
	}

	public function test_skip()
	{
		$range = Collection::range(0, 3);
		$this->assertSame([0, 1, 2], $range->skip(0)->toArray());
		$this->assertSame([1, 2], $range->skip(1)->toArray());
		$this->assertSame([2], $range->skip(2)->toArray());
		$this->assertSame([], $range->skip(3)->toArray());
		$this->assertSame([], $range->skip(4)->toArray());
	}

	public function test_skipWhile()
	{
		$range = Collection::from([0, 1, 2, 2, 3, 3, 4, 5, 5, 6]);
		$this->assertSame([0, 1, 2, 2, 3, 3, 4, 5, 5, 6], $range->skipWhile(fn ($v) => false)->toArray());
		$this->assertSame([1, 2, 2, 3, 3, 4, 5, 5, 6], $range->skipWhile(fn ($v) => $v === 0)->toArray());
		$this->assertSame([2, 2, 3, 3, 4, 5, 5, 6], $range->skipWhile(fn ($v) => $v <= 1)->toArray());
		$this->assertSame([3, 3, 4, 5, 5, 6], $range->skipWhile(fn ($v) => $v <= 2)->toArray());
		$this->assertSame([4, 5, 5, 6], $range->skipWhile(fn ($v) => $v <= 3)->toArray());
		$this->assertSame([5, 5, 6], $range->skipWhile(fn ($v) => $v <= 4)->toArray());
		$this->assertSame([6], $range->skipWhile(fn ($v) => $v <= 5)->toArray());
		$this->assertSame([], $range->skipWhile(fn ($v) => $v <= 6)->toArray());
		$this->assertSame([], $range->skipWhile(fn ($v) => $v <= 7)->toArray());
		$this->assertSame([], $range->skipWhile(fn ($v) => true)->toArray());
	}

	public function test_take()
	{
		$range = Collection::range(0, 3);
		$this->assertSame([0, 1, 2], $range->take(4)->toArray());
		$this->assertSame([0, 1, 2], $range->take(3)->toArray());
		$this->assertSame([0, 1], $range->take(2)->toArray());
		$this->assertSame([0], $range->take(1)->toArray());
		$this->assertSame([], $range->take(0)->toArray());
	}

	public function test_takeWhile()
	{
		$range = Collection::from([0, 1, 2, 2, 3, 3, 4, 5, 5, 6]);
		$this->assertSame([], $range->takeWhile(fn ($v) => false)->toArray());
		$this->assertSame([0], $range->takeWhile(fn ($v) => $v === 0)->toArray());
		$this->assertSame([0, 1], $range->takeWhile(fn ($v) => $v <= 1)->toArray());
		$this->assertSame([0, 1, 2, 2], $range->takeWhile(fn ($v) => $v <= 2)->toArray());
		$this->assertSame([0, 1, 2, 2, 3, 3], $range->takeWhile(fn ($v) => $v <= 3)->toArray());
		$this->assertSame([0, 1, 2, 2, 3, 3, 4], $range->takeWhile(fn ($v) => $v <= 4)->toArray());
		$this->assertSame([0, 1, 2, 2, 3, 3, 4, 5, 5], $range->takeWhile(fn ($v) => $v <= 5)->toArray());
		$this->assertSame([0, 1, 2, 2, 3, 3, 4, 5, 5, 6], $range->takeWhile(fn ($v) => $v <= 6)->toArray());
		$this->assertSame([0, 1, 2, 2, 3, 3, 4, 5, 5, 6], $range->takeWhile(fn ($v) => true)->toArray());
	}

	public function test_reverse()
	{
		$tests = [
			new Data([3, 2, 1], [1, 2, 3]),
			new Data([3, 2, 1], Collection::from([1, 2, 3])),
			new Data([3, 2, 1], Collection::from(new ArrayIterator([1, 2, 3]))),
		];
		foreach ($tests as $test) {
			$items = Collection::from(...$test->args);
			$this->assertSame($test->expected, $items->reverse()->toArray());
			$this->assertSame($test->expected, $items->reverse()->toArray());

			$a = $items->reverse()->reverse();
			$b = $items->reverse()->reverse()->reverse()->reverse();

			$this->assertSame($a->toArray(), $b->toArray());
		}
	}

	function test_aggregate()
	{
		$tests = [
			new Data(1, Collection::range(1, 1), fn($result, $value, $key) => $result + $value),
			new Data(3, Collection::range(1, 2), fn($result, $value, $key) => $result + $value),
			new Data(6, Collection::range(1, 3), fn($result, $value, $key) => $result + $value),

			new Data(11, Collection::range(1, 1), fn($result, $value, $key) => $result + $value, 10),
			new Data(13, Collection::range(1, 2), fn($result, $value, $key) => $result + $value, 10),
			new Data(16, Collection::range(1, 3), fn($result, $value, $key) => $result + $value, 10),

			new Data('[[[A]A]A]', Collection::repeat('A', 3), fn($result, $value, $key) => '[' . $result . $value . ']', ''),
			new Data('[[[xA]A]A]', Collection::repeat('A', 3), fn($result, $value, $key) => '[' . $result . $value . ']', 'x'),
		];
		foreach ($tests as $test) {
			$items = Collection::from($test->args[0]);
			$actual = $items->aggregate($test->args[1], isset($test->args[2]) ? $test->args[2]: 0);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
