<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;

/**
 * 応答データ出力。
 */
class ResponseOutput
{
	/**
	 * テキスト出力 標準実装。
	 *
	 * @param string $mime
	 * @param boolean $chunked
	 * @param mixed $data
	 * @return void
	 */
	protected function outputText(string $mime, bool $chunked, $data): void
	{
		echo strval($data);
	}

	/**
	 * JSON出力 標準実装。
	 *
	 * @param string $mime
	 * @param boolean $chunked
	 * @param mixed $data
	 * @return void
	 */
	protected function outputJson(string $mime, bool $chunked, $data): void
	{
		echo json_encode($data);
	}

	/**
	 * ストリーム出力 標準実装。
	 *
	 * @param string $mime
	 * @param boolean $chunked
	 * @param mixed $data
	 * @return void
	 */
	protected function outputStream(string $mime, bool $chunked, $data): void
	{
		echo $data;
	}

	/**
	 * HTTP応答 標準実装。
	 *
	 * @param string $mime
	 * @param boolean $chunked
	 * @param mixed $data
	 * @return void
	 */
	protected function outputDefault(string $mime, bool $chunked, $data): void
	{
		switch ($mime) {
			case Mime::TEXT:
				$this->outputText($mime, $chunked, $data);
				break;

			case Mime::JSON:
				$this->outputJson($mime, $chunked, $data);
				break;

			case Mime::STREAM:
				$this->outputStream($mime, $chunked, $data);
				break;

			default:
				throw new LogicException('unknown mime: ' . $mime);
		}
	}

	/**
	 * HTTP応答。
	 *
	 * @param string $mime
	 * @param boolean $chunked
	 * @param mixed $data
	 * @return void
	 */
	public function output(string $mime, bool $chunked, $data): void
	{
		$this->outputDefault($mime, $chunked, $data);
	}
}
