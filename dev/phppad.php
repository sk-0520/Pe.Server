<?php

error_reporting(E_ALL);

require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');

use PeServer\App\Models\AppStartup;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Memory;
use PeServer\Core\SizeConverter;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Stopwatch;

$autoLoader = new \PeServer\Core\AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . '/../public_html/PeServer',
		]
	]
);
$autoLoader->register();

$startup = new AppStartup(
	new DefinedDirectory(
		__DIR__ . '/../public_html/PeServer',
		__DIR__ . '/../public_html'
	)
);
$container = $startup->setup(
	AppStartup::MODE_CLI,
	[
		'environment' => 'temp',
		'revision' => ':REVISION:',
	]
);
Directory::setTemporaryDirectory(Path::combine(__DIR__, 'temp'));

class Pad
{
	public static function puts(mixed $value): void
	{
		if (is_string($value) || is_int($value) || is_double($value)) {
			echo $value . PHP_EOL;
			return;
		}

		var_dump($value);
	}

	public static function block(string $title, callable $callback): mixed
	{
		gc_collect_cycles();

		$beginMemory = Memory::getUsage();
		$sw = Stopwatch::startNew();

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
		echo ("<{$title}> LOOP: $count => ");

		$minMemory = PHP_INT_MAX;
		$minTime = PHP_INT_MAX;
		$maxMemory = PHP_INT_MIN;
		$maxTime = PHP_INT_MIN;
		$totalMemory = 0;
		$totalTime = 0;

		$result = null;
		$counter = 0;

		gc_collect_cycles();
		$baseMemory = Memory::getUsage();

		$sw = new Stopwatch();
		for ($i = 0; $i < $count; $i++) {
			gc_collect_cycles();

			try {
				$sw->start();

				$result = $callback($i);

				$endMemory = Memory::getUsage();
				$sw->stop();

				$useMemory = $endMemory - $baseMemory;
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

				$counter += 1;
			} catch (Throwable) {
			}
		}

		echo $counter . PHP_EOL;

		$avgMemory = $totalMemory / $counter;
		$avgTime = $totalTime  / $counter;

		$sizeConverter = new SizeConverter();

		$minMemorySize = $sizeConverter->convertHumanReadableByte($minMemory);
		$maxMemorySize = $sizeConverter->convertHumanReadableByte($maxMemory);
		$totalMemorySize = $sizeConverter->convertHumanReadableByte($totalMemory);
		$avgMemorySize = $sizeConverter->convertHumanReadableByte($avgMemory);

		$minTimeMs = Stopwatch::nanoToMilliseconds($minTime);
		$maxTimeMs = Stopwatch::nanoToMilliseconds($maxTime);
		$totalTimeMs = Stopwatch::nanoToMilliseconds($totalTime);
		$avgTimeMs = Stopwatch::nanoToMilliseconds($avgTime);

		self::puts(" (合計) 時間: $totalTimeMs msec, メモリ: $totalMemorySize");
		self::puts(" (平均) 時間: $avgTimeMs msec, メモリ: $avgMemorySize");
		self::puts(" (最小) 時間: $minTimeMs msec, メモリ: $minMemorySize");
		self::puts(" (最大) 時間: $maxTimeMs msec, メモリ: $maxMemorySize");

		return $result;
	}
}

$workPath = Path::combine(__DIR__, '@phppad.php');
if (File::exists($workPath)) {
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
