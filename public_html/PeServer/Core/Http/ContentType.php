<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Encoding;
use PeServer\Core\Text;

class ContentType
{
	#region define

	public const NAME = "Content-Type";

	#endregion

	public function __construct(
		public string $mime,
		public ?Encoding $encoding,
		public ?string $boundary = null
	) {
	}

	public static function create(string $mime, ?Encoding $encoding = null): self
	{
		return new self($mime, $encoding ?? Encoding::getDefaultEncoding(), null);
	}

	/**
	 * Undocumented function
	 *
	 * @param string[] $params
	 * @return self
	 */
	public static function from(array $params): self
	{
		$mime = Text::EMPTY;

		$rawParamValues = Text::split($params[0], ';');

		$mime = Text::trim($rawParamValues[0]);
		/** @var Encoding|null */
		$encoding = null;
		/** @var string|null */
		$boundary = null;

		$values = array_slice($rawParamValues, 1);
		foreach ($values as $value) {
			$kv = Text::split($value, '=', 2);
			if (Arr::getCount($kv) === 2) {
				$k = Text::toLower(Text::trim($kv[0]));

				if (Text::isNullOrWhiteSpace($k)) {
					continue;
				}

				$v = Text::trim($kv[1]);
				if (Text::isNullOrWhiteSpace($v)) {
					continue;
				}

				switch ($k) {
					case "charset":
						$encoding = new Encoding($v);
						break;

					case "boundary":
						$boundary = $v;
						break;

					default:
						break;
				}
			}
		}

		return new self($mime, $encoding, $boundary);
	}

	/**
	 * Undocumented function
	 *
	 * @return string[]
	 */
	public function toValues(): array
	{
		$result = [$this->mime];

		if ($this->encoding) {
			$result[] = 'charset=' . $this->encoding->name;
		}
		if ($this->boundary) {
			$result[] = 'boundary=' . $this->boundary;
		}

		return $result;
	}
}
