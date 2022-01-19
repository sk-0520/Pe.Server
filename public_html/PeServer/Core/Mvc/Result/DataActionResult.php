<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Bytes;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\DownloadDataContent;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;


class DataActionResult implements IActionResult
{
	private DataContent $content;

	/**
	 * 生成。
	 *
	 * @param DataContent $content
	 */
	public function __construct(DataContent $content)
	{
		$this->content = $content;
	}

	private function convertText(DataContent $content): string
	{
		return strval($content->data);
	}

	/**
	 * Undocumented function
	 *
	 * @param array<mixed> $data
	 * @return string
	 */
	private function convertJsonCore(array $data): string
	{
		$result = json_encode($data);
		if ($result === false) {
			throw new ArgumentException('$data');
		}

		return $result;
	}

	private function convertJson(DataContent $content): string
	{
		return $this->convertJsonCore($content->data); //@phpstan-ignore-line json array
	}

	private function convertRaw(DataContent $content): string
	{
		if ($content->data instanceof Bytes) {
			return $content->data->getRaw();
		}

		if (is_array($content->data)) {
			return $this->convertJsonCore($content->data);
		}

		return strval($content->data);
	}

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();

		$response->status = $this->content->httpStatus;

		$response->header->addValue('Content-Type', $this->content->mime);

		if ($this->content instanceof DownloadDataContent) {
			$fileName = urlencode($this->content->fileName);
			$response->header->addValue('Content-Disposition', "attachment; filename*=UTF-8''$fileName");
			$response->header->addValue('Content-Length', (string)$this->content->data->getLength()); //@phpstan-ignore-line DownloadDataContent
			$response->header->addValue('X-Content-Type-Options', 'nosniff');
			$response->header->addValue('Connection', 'close');
			$response->content = $this->convertRaw($this->content);
		} else {
			$response->content = match ($this->content->mime) {
				Mime::TEXT => $this->convertText($this->content),
				Mime::JSON => $this->convertJson($this->content),
				default => $this->convertRaw($this->content),
			};
		}

		return $response;
	}
}
