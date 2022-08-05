<?php

error_reporting(E_ALL);

require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');

use PeServer\App\Models\Initializer;
use PeServer\Core\ErrorHandler;
use PeServer\Core\IOUtility;
use PeServer\Core\Memory;
use PeServer\Core\PathUtility;
use PeServer\Core\SizeConverter;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Timer;

$autoLoader = new \PeServer\Core\AutoLoader(
	[
		__DIR__,
		__DIR__ . '/../public_html',
	],
	'/^PeServer/'
);
$autoLoader->register();

Initializer::initialize(
	__DIR__ . '/../public_html',
	__DIR__ . '/../public_html/PeServer',
	new SpecialStore(),
	'temp',
	':REVISION:'
);

class Pad
{
	public static function puts($value): void
	{
		if (is_string($value) || is_integer($value) || is_double($value)) {
			echo $value . PHP_EOL;
			return;
		}

		var_dump($value);
	}

	public static function block(string $title, callable $callback): mixed
	{
		$beginMemory = Memory::getUsage();
		$sw = Timer::startNew();

		$result = $callback();

		$endMemory = Memory::getUsage();
		$sw->stop();

		$sizeConverter = new SizeConverter();
		$memorySize = $sizeConverter->convertHumanReadableByte($endMemory - $beginMemory);

		$message = "[{$title}] 所要時間: {$sw->toString()}, 使用メモリ: {$memorySize}";
		self::puts($message);

		return $result;
	}

	public static function benchmark(string $title, int $count, callable $callback): mixed
	{
		self::puts("<{$title}> LOOP: $count");

		$minMemory = PHP_INT_MAX;
		$minTime = PHP_INT_MAX;
		$maxMemory = PHP_INT_MIN;
		$maxTime = PHP_INT_MIN;
		$totalMemory = 0;
		$totalTime = 0;

		$result = null;

		$sw = new Timer();
		for ($i = 0; $i < $count; $i++) {
			$beginMemory = Memory::getUsage();
			$sw->start();

			$result = $callback();

			$endMemory = Memory::getUsage();
			$sw->stop();

			$useMemory = $endMemory - $beginMemory;
			$useTime = $sw->getElapsed();

			if ($useMemory < $minMemory) {
				$minMemory = $useMemory;
			}
			if ($useTime < $minTime) {
				$minTime = $useTime;
			}

			if ($maxMemory < $useMemory) {
				$maxMemory = $useMemory;
			}
			if ($maxTime < $useTime) {
				$maxTime = $useTime;
			}

			$totalMemory += $useMemory;
			$totalTime += $useTime;
		}

		$avgMemory = $totalMemory / $count;
		$avgTime = $totalTime  / $count;

		$sizeConverter = new SizeConverter();

		$minMemorySize = $sizeConverter->convertHumanReadableByte($minMemory);
		$maxMemorySize = $sizeConverter->convertHumanReadableByte($maxMemory);
		$totalMemorySize = $sizeConverter->convertHumanReadableByte($totalMemory);
		$avgMemorySize = $sizeConverter->convertHumanReadableByte($avgMemory);

		$minTimeMs = Timer::nanoToMilliseconds($minTime);
		$maxTimeMs = Timer::nanoToMilliseconds($maxTime);
		$totalTimeMs = Timer::nanoToMilliseconds($totalTime);
		$avgTimeMs = Timer::nanoToMilliseconds($avgTime);

		self::puts(" (合計) 時間: $totalTimeMs msec, メモリ: $totalMemorySize");
		self::puts(" (平均) 時間: $avgTimeMs msec, メモリ: $avgMemorySize");
		self::puts(" (最小) 時間: $minTimeMs msec, メモリ: $minMemorySize");
		self::puts(" (最大) 時間: $maxTimeMs msec, メモリ: $maxMemorySize");

		return $result;
	}
}

$workPath = PathUtility::joinPath(__DIR__, '@phppad.php');
if (IOUtility::existsFile($workPath)) {
	try {
		require $workPath;
		Pad::puts(PHP_EOL . '--EXIT--');
	} catch (Throwable $ex) {
		Pad::puts($ex->__toString());
		Pad::puts(PHP_EOL . '--ERROR--');
	}
} else {
	echo "要作成一時スクリプト: $workPath";
}
