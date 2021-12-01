<?php declare(strict_types=1);
require_once('test.php');
require_once(TestClass::SRC . 'program/core/StringUtility.php');

class StringUtilityTest extends TestClass
{
	public function test_isNullOrEmpty()
	{
		$tests = [
			new Data(true, null),
			new Data(true, ''),
			new Data(false, '0'),
			new Data(false, 'abc'),
		];
		foreach($tests as $test) {
			$actual = StringUtility::isNullOrEmpty(...$test->args);
			if($test->expected) {
				$this->assertTrue($actual);
			} else {
				$this->assertFalse($actual);
			}
		}
	}

}
