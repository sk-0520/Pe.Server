<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\TypeUtility;

class ItBody
{
	public function __construct(
		public Dictionary|Binary $content,
		public string $customContentType
	) {
	}

	public static function form(
		Dictionary|array $body,
		string $enctype = 'application/x-www-form-urlencoded'
	): self {
		if ($body instanceof Dictionary) {
			return new self(
				$body,
				$enctype
			);
		}

		$map = new Dictionary(TypeUtility::TYPE_STRING, []);
		foreach ($body as $key => $value) {
			$map[$key] = (string)$value;
		}

		return new self(
			$map,
			$enctype
		);
	}
}
