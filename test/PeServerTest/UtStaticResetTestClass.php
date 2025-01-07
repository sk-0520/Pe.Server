<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\I18n;
use ReflectionClass;

class UtStaticResetTestClass extends TestClass
{
	#region function

	public function resetInitializeChecker()
	{
		$classNames = [
			I18n::class,
		];

		foreach ($classNames as $className) {
			$dirty = new ReflectionClass($className);
			$dirty->setStaticPropertyValue('initializeChecker', null);
		}
	}

	public function restoreInitializeChecker()
	{
		$this->resetInitializeChecker();
	}

	#endregion
}
