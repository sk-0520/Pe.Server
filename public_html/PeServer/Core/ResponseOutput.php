<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;

class ResponseOutput
{
	protected function outputPlainText(string $mime, bool $chunked, $data)
	{
		echo strval($data);
	}

	protected function outputJson(string $mime, bool $chunked, $data)
	{
		echo json_encode($data);
	}

	protected function outputStream(string $mime, bool $chunked, $data)
	{
		echo $data;
	}

	protected function outputDefault(string $mime, bool $chunked, $data)
	{
		switch ($mime) {
			case Mime::TEXT_PLAIN:
				$this->outputPlainText($mime, $chunked, $data);
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

	public function output(string $mime, bool $chunked, $data)
	{
		$this->outputDefault($mime, $chunked, $data);
	}
}
