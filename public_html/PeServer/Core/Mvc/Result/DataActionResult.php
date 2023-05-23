<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Serialization\Json;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Mvc\DownloadDataContent;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Throws\ArgumentException;


/**
 * 結果操作: データ。
 */
class DataActionResult implements IActionResult
{
	#region variable

	protected JsonSerializer $jsonSerializer;

	#endregion

	/**
	 * 生成。
	 *
	 * @param DataContent $content
	 */
	public function __construct(
		/** @readonly */
		private DataContent $content,
		?JsonSerializer $jsonSerializer = null
	) {
		$this->jsonSerializer = $jsonSerializer ?? new JsonSerializer();
	}

	#region function

	private function convertText(DataContent $content): string
	{
		return strval($content->data); //@phpstan-ignore-line
	}

	/**
	 * 配列をJSONに変換。
	 *
	 * @param array<mixed> $data
	 * @return string
	 */
	private function convertJsonCore(array $data): string
	{
		$result = $this->jsonSerializer->save($data);

		return $result->toString();
	}

	private function convertJson(DataContent $content): string
	{
		return $this->convertJsonCore($content->data); //@phpstan-ignore-line json array
	}

	private function convertRaw(DataContent $content): string
	{
		if ($content->data instanceof Binary) {
			return $content->data->getRaw();
		}

		if (is_array($content->data)) {
			return $this->convertJsonCore($content->data);
		}

		return strval($content->data);
	}

	#endregion

	#region IActionResult

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();

		$response->status = $this->content->httpStatus;

		$response->header->addValue('Content-Type', $this->content->mime);

		if ($this->content instanceof DownloadDataContent) {
			$fileName = urlencode($this->content->fileName);
			$response->header->addValue('Content-Disposition', "attachment; filename*=UTF-8''$fileName");
			$response->header->addValue('Content-Length', (string)$this->content->data->count()); //@phpstan-ignore-line DownloadDataContent
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

	#endregion
}
