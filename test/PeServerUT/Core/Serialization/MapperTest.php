<?php

declare(strict_types=1);

namespace PeServerUT\Core\Serialization;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTime;
use PeServer\Core\Serialization\Converter\DateIntervalConverter;
use PeServer\Core\Serialization\Converter\DateTimeConverter;
use PeServer\Core\Serialization\Mapper;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Throws\MapperKeyNotFoundException;
use PeServer\Core\Throws\MapperTypeException;
use PeServer\Core\Time;
use PeServerTest\TestClass;

class MapperTest extends TestClass
{
	public function test_mapping_Normal()
	{
		$mapper = new Mapper();
		$actual1 = new Normal();
		$mapper->mapping([
			'public' => 10,
			'protected' => 'string',
			'private' => [
				'A' => 'a'
			],
		], $actual1);


		$this->assertSame(10, $actual1->public);

		$actual2 = $actual1->array();
		$this->assertSame('string', $actual2['protected']);
		$this->assertSame('string', $actual2['protected']);
		$this->assertSame(['A' => 'a'], $actual2['private']);
	}

	public function test_mapping_TypeChange_normal()
	{
		$mapper = new Mapper();
		$actual = new TypeChange();
		$mapper->mapping([
			'int' => '10',
		], $actual);
		$this->assertSame(10, $actual->int);
	}

	public function test_mapping_TypeChange_TSURAI()
	{
		$mapper = new Mapper();
		$actual = new TypeChange();
		$mapper->mapping([
			'int' => 'A',
		], $actual);
		$this->assertSame(0, $actual->int);
	}

	public function test_mapping_NestInstance()
	{
		$mapper = new Mapper();
		$actual = new NestInstance();
		$mapper->mapping([
			'value' => 'value',
			'child' => [
				'int' => 123,
			],
		], $actual);
		$this->assertSame('value', $actual->value);
		$this->assertSame(123, $actual->child->int);
	}

	public function test_mapping_NestNull()
	{
		$mapper = new Mapper();
		$actual = new NestNull();
		$mapper->mapping([
			'value' => 'value',
			'child' => [
				'int' => 123,
			],
		], $actual);
		$this->assertSame('value', $actual->value);
		$this->assertSame(123, $actual->child->int);
	}

	public function test_mapping_EnumClass()
	{
		$mapper = new Mapper();
		$actual = new EnumClass();
		$mapper->mapping([
			'int0' => 'int0',
			'int1' => 'Int1',
			'int2' => 2,
			'string0' => '',
			'string1' => 'String1',
			'string2' => 'xyz',
			'AAA' => 1,
			'BBB' => 'Int2',
		], $actual);

		$this->assertSame(EnumInt::Int0, $actual->int0);
		$this->assertSame(EnumInt::Int1, $actual->int1);
		$this->assertSame(EnumInt::Int2, $actual->int2);
		$this->assertSame(EnumString::String0, $actual->string0);
		$this->assertSame(EnumString::String1, $actual->string1);
		$this->assertSame(EnumString::String2, $actual->string2);
		$this->assertSame(EnumInt::Int1, $actual->alias1);
		$this->assertSame(EnumInt::Int2, $actual->alias2);
	}

	public function test_mapping_AttrName()
	{
		$mapper = new Mapper();
		$actual = new AttrName();
		$mapper->mapping([
			'💩' => 'NAME!',
			'value' => 'VALUE!',
		], $actual);
		$this->assertSame('NAME!', $actual->name);
		$this->assertSame('VALUE!', $actual->value);
	}

	public function test_mapping_AttrIgnore()
	{
		$mapper = new Mapper();
		$actual = new AttrIgnore();
		$mapper->mapping([
			'ignore' => 'IGNORE!',
			'value' => 'VALUE!',
		], $actual);
		$this->assertSame('ignore?', $actual->ignore);
		$this->assertSame('VALUE!', $actual->value);
	}

	public function test_mapping_NotAttrNameNotFound()
	{
		$mapper = new Mapper();
		$actual = new NotAttrNameNotFound();
		$mapper->mapping([
			'abc' => 'VALUE!',
		], $actual);
		$this->assertSame('value?', $actual->value);
	}

	public function test_mapping_AttrNameNotFound_throw()
	{
		$mapper = new Mapper();
		$actual = new AttrNameNotFound();
		$this->expectException(MapperKeyNotFoundException::class);
		$mapper->mapping([
			'abc' => 'VALUE!',
		], $actual);
		$this->fail();
	}

	public function test_mapping_AttrObjectInstanceOnly()
	{
		$mapper = new Mapper();
		$actual = new AttrObjectInstanceOnly();

		$this->assertNull($actual->child);
		$mapper->mapping([
			'child' => [
				'int' => 123,
			],
		], $actual);
		$this->assertNull($actual->child);

		$actual->child = new NestChild();
		$actual->child->int = 456;
		$mapper->mapping([
			'child' => [
				'int' => 123,
			],
		], $actual);
		$this->assertNotNull($actual->child);
		$this->assertSame(123, $actual->child->int);
	}

	public function test_mapping_AttrTypeMismatch_throw()
	{
		$mapper = new Mapper();
		$actual = new AttrTypeMismatch();
		$this->expectException(MapperTypeException::class);
		$mapper->mapping([
			'int' => 'string',
		], $actual);
		$this->fail();
	}

	public function test_mapping_AttrTypeArrayValue()
	{
		$mapper = new Mapper();
		$actual = new AttrTypeArrayValue();
		$mapper->mapping([
			'array' => [
				'key1' => [
					'int' => 10,
					'string' => 'STRING',
				],
				'key2' => [
					'int' => 20,
					'string' => 'STRSTR',
				],
			],
		], $actual);

		$this->assertSame(ArrayValue::class, $actual->array['key1']::class);
		$this->assertSame(10, $actual->array['key1']->int);
		$this->assertSame('STRING', $actual->array['key1']->string);

		$this->assertSame(ArrayValue::class, $actual->array['key2']::class);
		$this->assertSame(20, $actual->array['key2']->int);
		$this->assertSame('STRSTR', $actual->array['key2']->string);
	}

	public function test_mapping_AttrTypeListArrayValue()
	{
		$mapper = new Mapper();
		$actual = new AttrTypeListArrayValue();
		$mapper->mapping([
			'array' => [
				'key1' => [
					'int' => 10,
					'string' => 'STRING',
				],
				'key2' => [
					'int' => 20,
					'string' => 'STRSTR',
				],
			],
		], $actual);

		$this->assertSame(ArrayValue::class, $actual->array[0]::class);
		$this->assertSame(10, $actual->array[0]->int);
		$this->assertSame('STRING', $actual->array[0]->string);

		$this->assertSame(ArrayValue::class, $actual->array[1]::class);
		$this->assertSame(20, $actual->array[1]->int);
		$this->assertSame('STRSTR', $actual->array[1]->string);
	}

	public function test_AttrTypeArrayNest()
	{
		$mapper = new Mapper();
		$actual = new AttrTypeArrayNest();
		$mapper->mapping([
			'array' => [
				'k1' => [
					'value' => [
						'int' => 999,
						'string' => 'K1',
					],
				],
				'k2' => [
					'value' => [
						'int' => -999,
						'string' => 'K2',
					],
				],
			]
		], $actual);

		$this->assertSame(NestArrayValue::class, $actual->array['k1']::class);
		$this->assertSame(999, $actual->array['k1']->value->int);
		$this->assertSame('K1', $actual->array['k1']->value->string);

		$this->assertSame(NestArrayValue::class, $actual->array['k2']::class);
		$this->assertSame(-999, $actual->array['k2']->value->int);
		$this->assertSame('K2', $actual->array['k2']->value->string);
	}

	public function test_TypeConverter()
	{
		$actual = new DateTimeClass();
		$mapper = new Mapper();
		$mapper->mapping([
			'datetime' => '2022-10-04T19:46:12+09:00',
			'immutable' => '2023-10-04T19:46:12+09:00',
			'interface' => '2023-10-04T19:46:12+00:00',
			'interval' => '1.23:45:34',
		], $actual);

		$this->assertSame('2022-10-04T19:46:12+09:00', $actual->datetime->format(DateTime::ATOM));
		$this->assertSame('2023-10-04T19:46:12+09:00', $actual->immutable->format(DateTime::ATOM));
		$this->assertSame('2023-10-04T19:46:12+00:00', $actual->interface->format(DateTime::ATOM));
		$this->assertSame('1.23:45:34', Time::toString($actual->interval, Time::FORMAT_READABLE));
	}
}

class Normal
{
	public int $public = 1;
	protected string $protected = 's';
	private array $private = [];

	public function array()
	{
		return [
			'public' => $this->public,
			'protected' => $this->protected,
			'private' => $this->private,
		];
	}
}

class TypeChange
{
	public int $int = -1;
}

class NestChild
{
	public int $int;
}

class NestInstance
{
	public string $value;
	public NestChild $child;

	public function __construct()
	{
		$this->child = new NestChild();
	}
}

class NestNull
{
	public string $value;
	public ?NestChild $child;
}

enum EnumInt: int
{
	case Int0 = 0;
	case Int1 = 1;
	case Int2 = 2;
}

enum EnumString: string
{
	case String0 = "";
	case String1 = "abc";
	case String2 = "xyz";
}

class EnumClass
{
	public EnumInt $int0;
	public EnumInt $int1;
	public EnumInt $int2;

	public EnumString $string0;
	public EnumString $string1;
	public EnumString $string2;

	#[Mapping('AAA')]
	public EnumInt $alias1;

	#[Mapping('BBB')]
	public EnumInt $alias2;
}

class AttrName
{
	#[Mapping('💩')]
	public string $name = 'name?';
	public string $value = 'value?';
}

class AttrIgnore
{
	#[Mapping(flags: Mapping::FLAG_IGNORE)]
	public string $ignore = 'ignore?';
	public string $value = 'value?';
}

class NotAttrNameNotFound
{
	public string $value = 'value?';
}

class AttrNameNotFound
{
	#[Mapping(flags: Mapping::FLAG_EXCEPTION_NOT_FOUND_KEY)]
	public string $value = 'value?';
}

class AttrObjectInstanceOnly
{
	#[Mapping(flags: Mapping::FLAG_OBJECT_INSTANCE_ONLY)]
	public ?NestChild $child = null;
}

class AttrTypeMismatch
{
	#[Mapping(flags: Mapping::FLAG_EXCEPTION_TYPE_MISMATCH)]
	public int $int;
}

class ArrayValue
{
	public int $int = 0;
	public string $string = 'string';
}

class AttrTypeArrayValue
{
	#[Mapping(arrayValueClassName: ArrayValue::class)]
	public array $array;
}

class AttrTypeListArrayValue
{
	#[Mapping(flags: Mapping::FLAG_LIST_ARRAY_VALUES, arrayValueClassName: ArrayValue::class)]
	public array $array;
}

class NestArrayValue
{
	public ArrayValue $value;
}

class AttrTypeArrayNest
{
	#[Mapping(arrayValueClassName: NestArrayValue::class)]
	public array $array = [];
}

class DateTimeClass
{
	#[Mapping(converter: DateTimeConverter::class)]
	public DateTime $datetime;
	#[Mapping(converter: DateTimeConverter::class)]
	public DateTimeImmutable $immutable;
	#[Mapping(converter: DateTimeConverter::class)]
	public DateTimeInterface $interface;
	#[Mapping(converter: DateIntervalConverter::class)]
	public DateInterval $interval;
}
