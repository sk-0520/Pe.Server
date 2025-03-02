<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collections\Access;
use PeServer\Core\Collections\Arr;
use PeServer\Core\IDisposable;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Stream;
use PeServer\Core\IO\StreamReader;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Throws\TypeException;
use ReflectionClass;

/**
 * コーディング上のあれ。
 */
abstract class Code
{
	#region function

	/**
	 * 文字列をリテラル文字列に変換。
	 *
	 * PHPStan用のラッパー(関数にしとけば後で探すの楽でしょ感で作った)。
	 *
	 * @param string $s
	 * @phpstan-return literal-string
	 * @phpstan-pure
	 */
	public static function toLiteralString(string $s): string
	{
		/** @phpstan-var literal-string */
		return $s;
	}

	/**
	 * 疑似コード: C# using
	 *
	 * これつっかえんわぁ。。。
	 * `=>` が複数行使えればなぁ。
	 *
	 * @template TDisposable of IDisposable
	 * @template TResult
	 * @param IDisposable $disposable
	 * @phpstan-param TDisposable $disposable
	 * @param callable $callback
	 * @phpstan-param callable(TDisposable $disposable):TResult $callback
	 * @return mixed
	 * @phpstan-return TResult
	 */
	public static function using(IDisposable $disposable, callable $callback)
	{
		try {
			return $callback($disposable);
		} finally {
			$disposable->dispose();
		}
	}

	/**
	 * 変数名を取得。
	 *
	 * TODO: 一行一項目の制限になるし字句・構文解析してるわけじゃないから改行とかコメントとか💩。
	 *
	 * @param mixed $var
	 * @param int $level
	 * @param Encoding|null $encoding ソースのエンコーディング。
	 * @return non-empty-string
	 */
	public static function nameof(mixed $var, int $level = 1, ?Encoding $encoding = null): string
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level)[$level - 1];
		$file = Access::getString($trace, "file");
		$lineNumber = Access::getInteger($trace, "line");

		$stream = Stream::open($file, Stream::MODE_READ);
		$reader = new StreamReader($stream, $encoding ?? Encoding::getUtf8());
		$sourceLineNumber = 1;
		/** @var string|null */
		$sourceLine = null;
		try {
			while (!$reader->isEnd()) {
				$lineValue = $reader->readLine();
				if ($lineNumber === $sourceLineNumber++) {
					$sourceLine = Text::trim($lineValue);
					break;
				}
			}
		} finally {
			$reader->dispose();
		}
		if ($sourceLine === null) {
			throw new InvalidOperationException();
		}

		// Regex で扱うのはソースではなく内部のエンコーディングなので引数 $encoding は考えなくていい
		$regex = new Regex();
		$symbolMatches = $regex->matches($sourceLine, '/\bCode\s*::\s*nameof\s*\(\s*(?<SYMBOL>.+?)\s*\)/' . 'n');
		if (isset($symbolMatches[2])) {
			// 同じ行に nameof があるとダメなんだわ
			throw new InvalidOperationException($sourceLine);
		}
		$symbolName = $symbolMatches["SYMBOL"];

		$targetMatches = $regex->matches($symbolName, '/((::)|(->))?(?<TARGET>[a-zA-Z_]\w*)$/' . 'n');
		if (!isset($targetMatches["TARGET"])) {
			throw new InvalidOperationException($sourceLine);
		}
		$targetName = $targetMatches["TARGET"];
		Throws::throwIfNullOrWhiteSpace($targetName);

		return $targetName;
	}

	/**
	 *
	 * @param object $obj
	 * @param string[] $propertyNames
	 * @param string $separator
	 * @return string
	 */
	public static function toString(object $obj, array $propertyNames, string $separator = ','): string
	{
		$rc = new ReflectionClass($obj);

		return
			get_class($obj) .
			'(' .
			Text::join($separator, Arr::map($propertyNames, fn($a) => $a . ':' . $rc->getProperty($a)->getValue($obj))) .
			')';
	}

	#endregion
}
