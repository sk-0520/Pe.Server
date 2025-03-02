<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Web\UrlEncodeKind;
use PeServer\Core\Web\UrlEncoding;

class FormUrlEncodedContent extends StaticContentBase
{
	/**
	 * 生成。
	 *
	 * @param Dictionary<string|null> $map
	 * @param UrlEncoding|null $urlEncoding
	 */
	public function __construct(Dictionary $map, ?UrlEncoding $urlEncoding = null)
	{
		$urlEncoding ??= new UrlEncoding(UrlEncodeKind::Rfc1738, Encoding::getDefaultEncoding());

		$kvItems = [];
		foreach ($map as $key => $value) {
			$encKey = $urlEncoding->encode($key);
			if ($value === null) {
				$kvItems[] = $encKey;
			} else {
				$encValue = $urlEncoding->encode($value);
				$kvItems[] = "$encKey=$encValue";
			}
		}

		$rawBody = Text::join('&', $kvItems);
		$body = new Binary($rawBody);

		parent::__construct($body);
	}

	#region StaticContentBase

	public function toHeader(): HttpHeader
	{
		return $this->createContentTypeHeader(" application/x-www-form-urlencoded");
	}

	#endregion
}
