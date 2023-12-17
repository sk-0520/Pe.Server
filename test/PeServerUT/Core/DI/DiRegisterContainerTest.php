<?php

declare(strict_types=1);

namespace PeServerUT\Core\DI;

use Error;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\DiRegisterContainer;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\DI\Inject;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\DiContainerArgumentException;
use PeServer\Core\Throws\DiContainerNotFoundException;
use PeServer\Core\Throws\DiContainerRegisteredException;
use PeServer\Core\Throws\DiContainerUndefinedTypeException;
use PeServerTest\TestClass;
use Throwable;

class DiRegisterContainerTest extends TestClass
{
	public function test_add_empty_throw()
	{
		$dc = new DiRegisterContainer();
		$this->expectException(ArgumentException::class);
		$dc->add('  ', new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, self::class));
		$this->fail();
	}

	public function test_add_remove()
	{
		$dc = new DiRegisterContainer();
		$dc->add('ID', new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, self::class));
		try {
			$dc->add('ID', new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, self::class));
			$this->fail();
		} catch (DiContainerRegisteredException) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		$item = $dc->remove('ID');
		$this->assertNotNull($item);

		$item2 = $dc->remove('ID');
		$this->assertNull($item2);

		$dc->add('ID', new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, self::class));
		$this->success();
	}

	public function test_get_value()
	{
		$expected = self::class;
		$dc = new DiRegisterContainer();
		$dc->add('ID', new DiItem(DiItem::LIFECYCLE_SINGLETON, DiItem::TYPE_VALUE, $expected));
		$actual = $dc->get('ID');
		$this->assertSame($expected, $actual);
	}

	public function test_get_throw()
	{
		$dc = new DiRegisterContainer();

		$dc->add('ID', DiItem::value('A'));

		$this->expectException(DiContainerNotFoundException::class);
		$this->expectExceptionMessage('id');
		$dc->get('id');
		$this->fail();
	}

	public function test_get_type_I_life_transient()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));

		$actual1 = $dc->get(I::class);
		$actual2 = $dc->get(I::class);

		$this->assertSame(A::class, $actual1::class);
		$this->assertSame(A::class, $actual2::class);
		$this->assertNotSame($actual1, $actual2);
	}

	public function test_get_type_I_life_singleton()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class, DiItem::LIFECYCLE_SINGLETON));

		$actual1 = $dc->get(I::class);
		$actual2 = $dc->get(I::class);

		$this->assertSame(A::class, $actual1::class);
		$this->assertSame(A::class, $actual2::class);
		$this->assertSame($actual1, $actual2);
	}

	public function test_get_type_A()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A0::class));

		$actual = $dc->get(I::class);

		$this->assertSame(A0::class, $actual::class);
	}

	public function test_get_type_B()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(B::class, DiItem::class(B::class));

		$actual = $dc->get(B::class);

		$this->assertSame(B::class, $actual::class);
		$this->assertSame(A::class, $actual->i::class);
	}


	public function test_get_type_C()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(B::class, DiItem::class(B::class));
		$dc->add(C::class, DiItem::class(C::class));

		$actual = $dc->get(C::class);

		$this->assertSame(C::class, $actual::class);
		$this->assertSame(B::class, $actual->b::class);
		$this->assertSame(A::class, $actual->a::class);
		$this->assertSame(A::class, $actual->i::class);
	}

	public function test_get_type_D_D1()
	{
		$dc = new DiRegisterContainer();

		$dc->add(D1::class, DiItem::class(D1::class));
		$dc->add(D::class, DiItem::class(D::class));

		$actual = $dc->get(D::class);
		$this->assertSame(D1::class, $actual->union::class);
	}
	public function test_get_type_D_D2()
	{
		$dc = new DiRegisterContainer();
		$dc->add(D2::class, DiItem::class(D2::class));
		$dc->add(D::class, DiItem::class(D::class));

		$actual = $dc->get(D::class);
		$this->assertSame(D2::class, $actual->union::class);
	}
	public function test_get_type_D_2to1()
	{
		$dc = new DiRegisterContainer();
		$dc->add(D2::class, DiItem::class(D2::class));
		$dc->add(D::class, DiItem::class(D::class));

		$actualD2 = $dc->get(D::class);
		$this->assertSame(D2::class, $actualD2->union::class);

		$dc->add(D1::class, DiItem::class(D1::class));
		$actualD1 = $dc->get(D::class);
		$this->assertSame(D1::class, $actualD1->union::class);
	}

	public function test_get_type_E_I()
	{
		$dc = new DiRegisterContainer();
		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(E::class, DiItem::class(E::class));

		$actual = $dc->get(E::class);
		$this->assertSame(A::class, $actual->i::class);
	}
	public function test_get_type_E_EI()
	{
		$dc = new DiRegisterContainer();
		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(EI::class, DiItem::class(EI::class));
		$dc->add(E::class, DiItem::class(E::class));

		$actual = $dc->get(E::class);
		$this->assertSame(EI::class, $actual->i::class);
	}

	public function test_get_value_F()
	{
		$dc = new DiRegisterContainer();
		$dc->add(I::class, DiItem::value(new A()));
		$dc->add(F::class, DiItem::class(F::class));

		$actual1 = $dc->get(F::class);
		$this->assertSame(A::class, $actual1->i::class);

		$actual2 = $dc->get(F::class);
		$this->assertSame(A::class, $actual2->i::class);

		$this->assertNotSame($actual1, $actual2);
		$this->assertSame($actual1->i, $actual2->i);
	}

	public function test_get_factory_G()
	{
		$dc = new DiRegisterContainer();
		$dc->add(G::class, DiItem::factory(fn () => new G(10)));
		$actual = $dc->get(G::class);

		$this->assertSame(10, $actual->num);
	}

	public function test_get_factory_G_1to3()
	{
		$counter = 1;

		$dc = new DiRegisterContainer();
		$dc->add(G::class, DiItem::factory(function (IDiContainer $c, array $stack) use (&$counter) {
			return new G($counter++);
		}));

		$actual1 = $dc->get(G::class);
		$this->assertSame(1, $actual1->num);

		$actual2 = $dc->get(G::class);
		$this->assertSame(2, $actual2->num);

		$actual3 = $dc->get(G::class);
		$this->assertSame(3, $actual3->num);
	}

	public function test_get_factory_G_1to3_singleton()
	{
		$counter = 10;

		$dc = new DiRegisterContainer();
		$dc->add(G::class, DiItem::factory(function (IDiContainer $c, array $stack) use (&$counter) {
			return new G($counter++);
		}, DiItem::LIFECYCLE_SINGLETON));

		$actual1 = $dc->get(G::class);
		$this->assertSame(10, $actual1->num);

		$actual2 = $dc->get(G::class);
		$this->assertSame(10, $actual2->num);

		$actual3 = $dc->get(G::class);
		$this->assertSame(10, $actual3->num);
	}

	public function test_get_type_H()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(H::class, DiItem::class(H::class));

		try {
			$dc->get(H::class);
			$this->fail();
		} catch (DiContainerUndefinedTypeException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		$dc->add(B::class, DiItem::class(B::class));

		$actual = $dc->get(H::class);


		$this->assertSame(H::class, $actual::class);
		$this->assertSame(A::class, $actual->i::class);
		$this->assertSame(B::class, $actual->b::class);
	}

	public function test_get_type_J()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(J::class, DiItem::class(J::class));

		$actual1 = $dc->get(J::class);
		$this->assertSame(J::class, $actual1::class);
		$this->assertSame(A::class, $actual1->i::class);
		$this->assertNull($actual1->b);

		$dc->add(B::class, DiItem::class(B::class));

		$actual2 = $dc->get(J::class);

		$this->assertSame(J::class, $actual2::class);
		$this->assertSame(A::class, $actual2->i::class);
		$this->assertSame(B::class, $actual2->b::class);
	}

	public function test_get_type_K()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(K::class, DiItem::class(K::class));

		$actual1 = $dc->get(K::class);
		$this->assertSame(K::class, $actual1::class);
		$this->assertSame(A::class, $actual1->i::class);
		$this->assertSame(123, $actual1->b);

		$dc->add(B::class, DiItem::class(B::class));

		$actual2 = $dc->get(K::class);

		$this->assertSame(K::class, $actual2::class);
		$this->assertSame(A::class, $actual2->i::class);
		$this->assertSame(B::class, $actual2->b::class);
	}

	public function test_get_type_L()
	{
		$dc = new DiRegisterContainer();

		$dc->add(I::class, DiItem::class(A::class));
		$dc->add(L::class, DiItem::class(L::class));

		$actual1 = $dc->get(L::class);
		$this->assertSame(L::class, $actual1::class);
		$this->assertSame(A::class, $actual1->i::class);
		$this->assertSame(123, $actual1->b);

		$dc->add(B::class, DiItem::class(B::class));

		$actual2 = $dc->get(L::class);

		$this->assertSame(L::class, $actual2::class);
		$this->assertSame(A::class, $actual2->i::class);
		$this->assertSame(B::class, $actual2->b::class);

		$dc->add(F::class, DiItem::class(F::class));

		$actual3 = $dc->get(L::class);

		$this->assertSame(L::class, $actual3::class);
		$this->assertSame(A::class, $actual3->i::class);
		$this->assertSame(F::class, $actual3->b::class);
	}

	public function test_get_type_M()
	{
		$dc = new DiRegisterContainer();

		//$dc->add(I::class, DiItem::class(A::class));
		$dc->add(M::class, DiItem::class(M::class));

		$actual1 = $dc->get(M::class);
		$this->assertSame(M::class, $actual1::class);
		try {
			$actual1->i; // 未初期化アクセスは死ぬ
			$this->fail();
		} catch (Error) {
			$this->success();
		}

		$dc->add(I::class, DiItem::class(A::class));

		$actual2 = $dc->get(M::class);
		$this->assertSame(M::class, $actual2::class);
		$this->assertSame(A::class, $actual2->i::class);
	}

	public function test_new()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);
		$dc->registerClass(B::class);
		$dc->registerClass(EI::class);
		$dc->registerClass(E::class);
		$dc->registerClass(H::class);

		/** @var N */
		$actual = $dc->new(N::class);
		$this->assertSame(N::class, $actual::class);
		$this->assertSame(H::class, $actual->h::class);
		$this->assertSame(B::class, $actual->h->b::class);
		$this->assertSame(A::class, $actual->h->i::class);
		$this->assertSame(E::class, $actual->e::class);
		$this->assertSame(EI::class, $actual->e->i::class);
	}

	public function test_new_arguments()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);
		$dc->registerClass(B::class);

		$actual1 = $dc->get(B::class);
		$this->assertSame(B::class, $actual1::class);
		$this->assertSame(A::class, $actual1->i::class);

		$actual2 = $dc->new(B::class);
		$this->assertSame(B::class, $actual2::class);
		$this->assertSame(A::class, $actual2->i::class);

		$actual3 = $dc->new(B::class, [0 => new A0()]);
		$this->assertSame(B::class, $actual3::class);
		$this->assertSame(A0::class, $actual3->i::class);

		$actual4 = $dc->new(B::class, [1 => new A0()]);
		$this->assertSame(B::class, $actual4::class);
		$this->assertSame(A::class, $actual4->i::class);

		$actual5 = $dc->new(B::class, ['$i' => new A0()]);
		$this->assertSame(B::class, $actual5::class);
		$this->assertSame(A0::class, $actual5->i::class);

		$actual6 = $dc->new(B::class, ['$I' => new A0()]);
		$this->assertSame(B::class, $actual6::class);
		$this->assertSame(A::class, $actual6->i::class);
	}

	public function test_new_arguments_value_throw()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);
		$dc->registerValue($dc->new(B::class));

		$actual = $dc->new(B::class);
		$this->assertSame(B::class, $actual::class);
		$this->assertSame(A::class, $actual->i::class);

		$this->expectException(DiContainerArgumentException::class);
		$this->expectExceptionMessage(B::class . ': DiItem::TYPE_VALUE');
		$dc->new(B::class, [0 => new A0()]);
		$this->fail();
	}

	public function test_new_arguments_value_singleton()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);
		$dc->registerClass(B::class, DiItem::LIFECYCLE_SINGLETON);

		$actual = $dc->new(B::class);
		$this->assertSame(B::class, $actual::class);
		$this->assertSame(A::class, $actual->i::class);

		$this->expectException(DiContainerArgumentException::class);
		$this->expectExceptionMessage(B::class . ': DiItem::LIFECYCLE_SINGLETON');
		$dc->new(B::class, [0 => new A0()]);
		$this->fail();
	}

	public function test_new_arguments_type()
	{
		$dc = new DiRegisterContainer();

		$actual1 = $dc->new(B::class, [I::class => new A()]);
		$this->assertSame(A::class, $actual1->i::class);

		$actual2 = $dc->new(B::class, [I::class => new A0()]);
		$this->assertSame(A0::class, $actual2->i::class);
	}

	public function test_new_arguments_type_2()
	{
		$dc = new DiRegisterContainer();
		$dc->registerMapping(I::class, A::class);

		$actual = $dc->new(P::class, [I::class => new A0()]);
		$this->assertSame(A0::class, $actual->i1::class);
		$this->assertSame(A::class, $actual->i2::class);
	}

	public function test_new_arguments_minus()
	{
		$dc = new DiRegisterContainer();

		$actual1 = $dc->new(P::class, [-1 => new A(), -2 => new A0()]);
		$this->assertSame(A::class, $actual1->i1::class);
		$this->assertSame(A0::class, $actual1->i2::class);

		$actual2 = $dc->new(P::class, [-2 => new A(), -1 => new A0()]);
		$this->assertSame(A0::class, $actual2->i1::class);
		$this->assertSame(A::class, $actual2->i2::class);
	}

	public function test_new_arguments_union()
	{
		$dc = new DiRegisterContainer();
		$dc->registerMapping(I::class, EI::class);

		$actual = $dc->new(Q::class, [-1 => new A(), I::class => new A0(), B::class => new B(new A0())]);
		$this->assertSame(A0::class, $actual->i1::class);
		$this->assertSame(B::class, $actual->i2::class);
		$this->assertSame(A0::class, $actual->i2->i::class);
		$this->assertSame(A::class, $actual->i3::class);
		$this->assertSame(EI::class, $actual->i4::class);
	}

	public function test_call_instance()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);
		$instance = $dc->new(O_instance::class);

		$actual = $dc->call([$instance, 'method']);
		$this->assertSame(A::class, $actual['i']::class);
	}

	public function test_call_static()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);

		$actual = $dc->call(O_static::class . '::method');
		$this->assertSame(A::class, $actual['i']::class);
	}

	public function test_call_function()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);

		/** @disregard P1006 */
		$actual = $dc->call(O_function::class);
		$this->assertSame(A::class, $actual['i']::class);
	}

	public function test_call_callable()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(I::class, A::class);

		$actual1 = $dc->call(fn (I $i) => ['i' => $i]);
		$this->assertSame(A::class, $actual1['i']::class);

		$actual2 = $dc->call(fn (#[Inject(EI::class)] I $i) => ['i' => $i]);
		$this->assertSame(A::class, $actual2['i']::class);

		$dc->registerMapping(EI::class, EI::class);
		$actual3 = $dc->call(fn (#[Inject(EI::class)] I $i) => ['i' => $i]);
		$this->assertSame(EI::class, $actual3['i']::class);
	}
}

interface I
{
}

class A implements I
{
}

class A0 extends A
{
	public function __construct()
	{
	}
}

class B
{
	public function __construct(public I $i)
	{
	}
}

class C
{
	public function __construct(public B $b, public A $a, public I $i)
	{
	}
}

class D1
{
}
class D2
{
}
class D
{
	public function __construct(public D1|D2 $union)
	{
	}
}

class EI implements I
{
}

class E
{
	public function __construct(#[Inject(EI::class)] public I $i)
	{
	}
}

class F
{
	public function __construct(public I $i)
	{
	}
}

class G
{
	public function __construct(public int $num)
	{
	}
}

class H
{
	public function __construct(
		public I $i,
		public B $b
	) {
	}
}

class J
{
	public function __construct(
		public I $i,
		public ?B $b
	) {
	}
}

class K
{
	public function __construct(
		public I $i,
		public int|B $b = 123
	) {
	}
}

class L
{
	public function __construct(
		public I $i,
		#[Inject(F::class)]
		public int|B|F $b = 123
	) {
	}
}

class M
{
	#[Inject]
	public I $i;
}

class N
{
	public function __construct(
		public H $h,
		public E $e
	) {
	}
}

class O_instance
{
	public function method(I $i)
	{
		return ['i' => $i];
	}
}

class O_static
{
	public static function method(I $i)
	{
		return ['i' => $i];
	}
}

function O_function(I $i)
{
	return ['i' => $i];
}

class P
{
	public function __construct(
		public I $i1,
		public I $i2
	) {
	}
}

class Q
{
	public function __construct(
		public int|I|B $i1,
		public int|I|B $i2,
		public int|I|B $i3,
		public int|I|B $i4
	) {
	}
}
