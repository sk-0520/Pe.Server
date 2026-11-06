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
		$expected = new LocalA();
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

		$dc->add(LocalI::class, DiItem::class(LocalA::class));

		$actual1 = $dc->get(LocalI::class);
		$actual2 = $dc->get(LocalI::class);

		$this->assertSame(LocalA::class, $actual1::class);
		$this->assertSame(LocalA::class, $actual2::class);
		$this->assertNotSame($actual1, $actual2);
	}

	public function test_get_type_I_life_singleton()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class, DiItem::LIFECYCLE_SINGLETON));

		$actual1 = $dc->get(LocalI::class);
		$actual2 = $dc->get(LocalI::class);

		$this->assertSame(LocalA::class, $actual1::class);
		$this->assertSame(LocalA::class, $actual2::class);
		$this->assertSame($actual1, $actual2);
	}

	public function test_get_type_A()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA0::class));

		$actual = $dc->get(LocalI::class);

		$this->assertSame(LocalA0::class, $actual::class);
	}

	public function test_get_type_B()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalB::class, DiItem::class(LocalB::class));

		$actual = $dc->get(LocalB::class);

		$this->assertSame(LocalB::class, $actual::class);
		$this->assertSame(LocalA::class, $actual->i::class);
	}


	public function test_get_type_C()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalB::class, DiItem::class(LocalB::class));
		$dc->add(LocalC::class, DiItem::class(LocalC::class));

		$actual = $dc->get(LocalC::class);

		$this->assertSame(LocalC::class, $actual::class);
		$this->assertSame(LocalB::class, $actual->b::class);
		$this->assertSame(LocalA::class, $actual->a::class);
		$this->assertSame(LocalA::class, $actual->i::class);
	}

	public function test_get_type_D_D1()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalD1::class, DiItem::class(LocalD1::class));
		$dc->add(LocalD::class, DiItem::class(LocalD::class));

		$actual = $dc->get(LocalD::class);
		$this->assertSame(LocalD1::class, $actual->union::class);
	}
	public function test_get_type_D_D2()
	{
		$dc = new DiRegisterContainer();
		$dc->add(LocalD2::class, DiItem::class(LocalD2::class));
		$dc->add(LocalD::class, DiItem::class(LocalD::class));

		$actual = $dc->get(LocalD::class);
		$this->assertSame(LocalD2::class, $actual->union::class);
	}
	public function test_get_type_D_2to1()
	{
		$dc = new DiRegisterContainer();
		$dc->add(LocalD2::class, DiItem::class(LocalD2::class));
		$dc->add(LocalD::class, DiItem::class(LocalD::class));

		$actualD2 = $dc->get(LocalD::class);
		$this->assertSame(LocalD2::class, $actualD2->union::class);

		$dc->add(LocalD1::class, DiItem::class(LocalD1::class));
		$actualD1 = $dc->get(LocalD::class);
		$this->assertSame(LocalD1::class, $actualD1->union::class);
	}

	public function test_get_type_E_I()
	{
		$dc = new DiRegisterContainer();
		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalE::class, DiItem::class(LocalE::class));

		$actual = $dc->get(LocalE::class);
		$this->assertSame(LocalA::class, $actual->i::class);
	}
	public function test_get_type_E_EI()
	{
		$dc = new DiRegisterContainer();
		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalEI::class, DiItem::class(LocalEI::class));
		$dc->add(LocalE::class, DiItem::class(LocalE::class));

		$actual = $dc->get(LocalE::class);
		$this->assertSame(LocalEI::class, $actual->i::class);
	}

	public function test_get_value_F()
	{
		$dc = new DiRegisterContainer();
		$dc->add(LocalI::class, DiItem::value(new LocalA()));
		$dc->add(LocalF::class, DiItem::class(LocalF::class));

		$actual1 = $dc->get(LocalF::class);
		$this->assertSame(LocalA::class, $actual1->i::class);

		$actual2 = $dc->get(LocalF::class);
		$this->assertSame(LocalA::class, $actual2->i::class);

		$this->assertNotSame($actual1, $actual2);
		$this->assertSame($actual1->i, $actual2->i);
	}

	public function test_get_factory_G()
	{
		$dc = new DiRegisterContainer();
		$dc->add(LocalG::class, DiItem::factory(fn() => new LocalG(10)));
		$actual = $dc->get(LocalG::class);

		$this->assertSame(10, $actual->num);
	}

	public function test_get_factory_G_1to3()
	{
		$counter = 1;

		$dc = new DiRegisterContainer();
		$dc->add(LocalG::class, DiItem::factory(function (IDiContainer $c, array $stack) use (&$counter) {
			return new LocalG($counter++);
		}));

		$actual1 = $dc->get(LocalG::class);
		$this->assertSame(1, $actual1->num);

		$actual2 = $dc->get(LocalG::class);
		$this->assertSame(2, $actual2->num);

		$actual3 = $dc->get(LocalG::class);
		$this->assertSame(3, $actual3->num);
	}

	public function test_get_factory_G_1to3_singleton()
	{
		$counter = 10;

		$dc = new DiRegisterContainer();
		$dc->add(LocalG::class, DiItem::factory(function (IDiContainer $c, array $stack) use (&$counter) {
			return new LocalG($counter++);
		}, DiItem::LIFECYCLE_SINGLETON));

		$actual1 = $dc->get(LocalG::class);
		$this->assertSame(10, $actual1->num);

		$actual2 = $dc->get(LocalG::class);
		$this->assertSame(10, $actual2->num);

		$actual3 = $dc->get(LocalG::class);
		$this->assertSame(10, $actual3->num);
	}

	public function test_get_type_H()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalH::class, DiItem::class(LocalH::class));

		try {
			$dc->get(LocalH::class);
			$this->fail();
		} catch (DiContainerUndefinedTypeException $ex) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		$dc->add(LocalB::class, DiItem::class(LocalB::class));

		$actual = $dc->get(LocalH::class);


		$this->assertSame(LocalH::class, $actual::class);
		$this->assertSame(LocalA::class, $actual->i::class);
		$this->assertSame(LocalB::class, $actual->b::class);
	}

	public function test_get_type_J()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalJ::class, DiItem::class(LocalJ::class));

		$actual1 = $dc->get(LocalJ::class);
		$this->assertSame(LocalJ::class, $actual1::class);
		$this->assertSame(LocalA::class, $actual1->i::class);
		$this->assertNull($actual1->b);

		$dc->add(LocalB::class, DiItem::class(LocalB::class));

		$actual2 = $dc->get(LocalJ::class);

		$this->assertSame(LocalJ::class, $actual2::class);
		$this->assertSame(LocalA::class, $actual2->i::class);
		$this->assertSame(LocalB::class, $actual2->b::class);
	}

	public function test_get_type_K()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalK::class, DiItem::class(LocalK::class));

		$actual1 = $dc->get(LocalK::class);
		$this->assertSame(LocalK::class, $actual1::class);
		$this->assertSame(LocalA::class, $actual1->i::class);
		$this->assertSame(123, $actual1->b);

		$dc->add(LocalB::class, DiItem::class(LocalB::class));

		$actual2 = $dc->get(LocalK::class);

		$this->assertSame(LocalK::class, $actual2::class);
		$this->assertSame(LocalA::class, $actual2->i::class);
		$this->assertSame(LocalB::class, $actual2->b::class);
	}

	public function test_get_type_L()
	{
		$dc = new DiRegisterContainer();

		$dc->add(LocalI::class, DiItem::class(LocalA::class));
		$dc->add(LocalL::class, DiItem::class(LocalL::class));

		$actual1 = $dc->get(LocalL::class);
		$this->assertSame(LocalL::class, $actual1::class);
		$this->assertSame(LocalA::class, $actual1->i::class);
		$this->assertSame(123, $actual1->b);

		$dc->add(LocalB::class, DiItem::class(LocalB::class));

		$actual2 = $dc->get(LocalL::class);

		$this->assertSame(LocalL::class, $actual2::class);
		$this->assertSame(LocalA::class, $actual2->i::class);
		$this->assertSame(LocalB::class, $actual2->b::class);

		$dc->add(LocalF::class, DiItem::class(LocalF::class));

		$actual3 = $dc->get(LocalL::class);

		$this->assertSame(LocalL::class, $actual3::class);
		$this->assertSame(LocalA::class, $actual3->i::class);
		$this->assertSame(LocalF::class, $actual3->b::class);
	}

	public function test_get_type_M()
	{
		$dc = new DiRegisterContainer();

		//$dc->add(I::class, DiItem::class(A::class));
		$dc->add(LocalM::class, DiItem::class(LocalM::class));

		$actual1 = $dc->get(LocalM::class);
		$this->assertSame(LocalM::class, $actual1::class);
		try {
			$actual1->i; // 未初期化アクセスは死ぬ
			$this->fail();
		} catch (Error) {
			$this->success();
		}

		$dc->add(LocalI::class, DiItem::class(LocalA::class));

		$actual2 = $dc->get(LocalM::class);
		$this->assertSame(LocalM::class, $actual2::class);
		$this->assertSame(LocalA::class, $actual2->i::class);
	}

	public function test_new()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);
		$dc->registerClass(LocalB::class);
		$dc->registerClass(LocalEI::class);
		$dc->registerClass(LocalE::class);
		$dc->registerClass(LocalH::class);

		/** @var N */
		$actual = $dc->new(LocalN::class);
		$this->assertSame(LocalN::class, $actual::class);
		$this->assertSame(LocalH::class, $actual->h::class);
		$this->assertSame(LocalB::class, $actual->h->b::class);
		$this->assertSame(LocalA::class, $actual->h->i::class);
		$this->assertSame(LocalE::class, $actual->e::class);
		$this->assertSame(LocalEI::class, $actual->e->i::class);
	}

	public function test_new_arguments()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);
		$dc->registerClass(LocalB::class);

		$actual1 = $dc->get(LocalB::class);
		$this->assertSame(LocalB::class, $actual1::class);
		$this->assertSame(LocalA::class, $actual1->i::class);

		$actual2 = $dc->new(LocalB::class);
		$this->assertSame(LocalB::class, $actual2::class);
		$this->assertSame(LocalA::class, $actual2->i::class);

		$actual3 = $dc->new(LocalB::class, [0 => new LocalA0()]);
		$this->assertSame(LocalB::class, $actual3::class);
		$this->assertSame(LocalA0::class, $actual3->i::class);

		$actual4 = $dc->new(LocalB::class, [1 => new LocalA0()]);
		$this->assertSame(LocalB::class, $actual4::class);
		$this->assertSame(LocalA::class, $actual4->i::class);

		$actual5 = $dc->new(LocalB::class, ['$i' => new LocalA0()]);
		$this->assertSame(LocalB::class, $actual5::class);
		$this->assertSame(LocalA0::class, $actual5->i::class);

		$actual6 = $dc->new(LocalB::class, ['$I' => new LocalA0()]);
		$this->assertSame(LocalB::class, $actual6::class);
		$this->assertSame(LocalA::class, $actual6->i::class);
	}

	public function test_new_arguments_value_throw()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);
		$dc->registerValue($dc->new(LocalB::class));

		$actual = $dc->new(LocalB::class);
		$this->assertSame(LocalB::class, $actual::class);
		$this->assertSame(LocalA::class, $actual->i::class);

		$this->expectException(DiContainerArgumentException::class);
		$this->expectExceptionMessage(LocalB::class . ': DiItem::TYPE_VALUE');
		$dc->new(LocalB::class, [0 => new LocalA0()]);
		$this->fail();
	}

	public function test_new_arguments_value_singleton()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);
		$dc->registerClass(LocalB::class, DiItem::LIFECYCLE_SINGLETON);

		$actual = $dc->new(LocalB::class);
		$this->assertSame(LocalB::class, $actual::class);
		$this->assertSame(LocalA::class, $actual->i::class);

		$this->expectException(DiContainerArgumentException::class);
		$this->expectExceptionMessage(LocalB::class . ': DiItem::LIFECYCLE_SINGLETON');
		$dc->new(LocalB::class, [0 => new LocalA0()]);
		$this->fail();
	}

	public function test_new_arguments_type()
	{
		$dc = new DiRegisterContainer();

		$actual1 = $dc->new(LocalB::class, [LocalI::class => new LocalA()]);
		$this->assertSame(LocalA::class, $actual1->i::class);

		$actual2 = $dc->new(LocalB::class, [LocalI::class => new LocalA0()]);
		$this->assertSame(LocalA0::class, $actual2->i::class);
	}

	public function test_new_arguments_type_2()
	{
		$dc = new DiRegisterContainer();
		$dc->registerMapping(LocalI::class, LocalA::class);

		$actual = $dc->new(LocalP::class, [LocalI::class => new LocalA0()]);
		$this->assertSame(LocalA0::class, $actual->i1::class);
		$this->assertSame(LocalA::class, $actual->i2::class);
	}

	public function test_new_arguments_minus()
	{
		$dc = new DiRegisterContainer();

		$actual1 = $dc->new(LocalP::class, [-1 => new LocalA(), -2 => new LocalA0()]);
		$this->assertSame(LocalA::class, $actual1->i1::class);
		$this->assertSame(LocalA0::class, $actual1->i2::class);

		$actual2 = $dc->new(LocalP::class, [-2 => new LocalA(), -1 => new LocalA0()]);
		$this->assertSame(LocalA0::class, $actual2->i1::class);
		$this->assertSame(LocalA::class, $actual2->i2::class);
	}

	public function test_new_arguments_union()
	{
		$dc = new DiRegisterContainer();
		$dc->registerMapping(LocalI::class, LocalEI::class);

		$actual = $dc->new(LocalQ::class, [-1 => new LocalA(), LocalI::class => new LocalA0(), LocalB::class => new LocalB(new LocalA0())]);
		$this->assertSame(LocalA0::class, $actual->i1::class);
		$this->assertSame(LocalB::class, $actual->i2::class);
		$this->assertSame(LocalA0::class, $actual->i2->i::class);
		$this->assertSame(LocalA::class, $actual->i3::class);
		$this->assertSame(LocalEI::class, $actual->i4::class);
	}

	public function test_call_instance()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);
		$instance = $dc->new(LocalOInstance::class);

		$actual = $dc->call([$instance, 'method']);
		$this->assertSame(LocalA::class, $actual['i']::class);
	}

	public function test_call_static()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);

		$actual = $dc->call(LocalOStatic::class . '::method');
		$this->assertSame(LocalA::class, $actual['i']::class);
	}

	public function test_call_function()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);

		/** @disregard P1006 */
		$actual = $dc->call(O_function::class);
		$this->assertSame(LocalA::class, $actual['i']::class);
	}

	public function test_call_callable()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalI::class, LocalA::class);

		$actual1 = $dc->call(fn(LocalI $i) => ['i' => $i]);
		$this->assertSame(LocalA::class, $actual1['i']::class);

		$actual2 = $dc->call(fn(#[Inject(LocalEI::class)] LocalI $i) => ['i' => $i]);
		$this->assertSame(LocalA::class, $actual2['i']::class);

		$dc->registerMapping(LocalEI::class, LocalEI::class);
		$actual3 = $dc->call(fn(#[Inject(LocalEI::class)] LocalI $i) => ['i' => $i]);
		$this->assertSame(LocalEI::class, $actual3['i']::class);
	}
}

interface LocalI
{
	//NOP
}

class LocalA implements LocalI
{
	//NOP
}

class LocalA0 extends LocalA
{
	public function __construct()
	{
		//NOP
	}
}

class LocalB
{
	public function __construct(public LocalI $i)
	{
		//NOP
	}
}

class LocalC
{
	public function __construct(public LocalB $b, public LocalA $a, public LocalI $i)
	{
		//NOP
	}
}

class LocalD1
{
	//NOP
}
class LocalD2
{
	//NOP
}
class LocalD
{
	public function __construct(public LocalD1|LocalD2 $union)
	{
		//NOP
	}
}

class LocalEI implements LocalI
{
	//NOP
}

class LocalE
{
	public function __construct(#[Inject(LocalEI::class)] public LocalI $i)
	{
		//NOP
	}
}

class LocalF
{
	public function __construct(public LocalI $i)
	{
		//NOP
	}
}

class LocalG
{
	public function __construct(public int $num)
	{
		//NOP
	}
}

class LocalH
{
	public function __construct(
		public LocalI $i,
		public LocalB $b
	) {
		//NOP
	}
}

class LocalJ
{
	public function __construct(
		public LocalI $i,
		public ?LocalB $b
	) {
		//NOP
	}
}

class LocalK
{
	public function __construct(
		public LocalI $i,
		public int|LocalB $b = 123
	) {
		//NOP
	}
}

class LocalL
{
	public function __construct(
		public LocalI $i,
		#[Inject(LocalF::class)]
		public int|LocalB|LocalF $b = 123
	) {
		//NOP
	}
}

class LocalM
{
	#[Inject]
	public LocalI $i;
}

class LocalN
{
	public function __construct(
		public LocalH $h,
		public LocalE $e
	) {
		//NOP
	}
}

class LocalOInstance
{
	public function method(LocalI $i)
	{
		return ['i' => $i];
	}
}

class LocalOStatic
{
	public static function method(LocalI $i)
	{
		return ['i' => $i];
	}
}

function O_function(LocalI $i)
{
	return ['i' => $i];
}

class LocalP
{
	public function __construct(
		public LocalI $i1,
		public LocalI $i2
	) {
		//NOP
	}
}

class LocalQ
{
	public function __construct(
		public int|LocalI|LocalB $i1,
		public int|LocalI|LocalB $i2,
		public int|LocalI|LocalB $i3,
		public int|LocalI|LocalB $i4
	) {
		//NOP
	}
}
