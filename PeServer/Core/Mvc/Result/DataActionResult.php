<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Binary;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\DataContent;
use PeServer\Core\Mvc\Content\DataContentBase;
use PeServer\Core\Mvc\Content\DownloadDataContent;
use PeServer\Core\Mvc\Content\IDownloadContent;
use PeServer\Core\Mvc\Content\StaticDataContent;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Serialization\Json;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * 結果操作: データ。
 */
readonly class DataActionResult implements IActionResult
{
	#region variable

	protected JsonSerializer $jsonSerializer;

	#endregion

	/**
	 * 生成。
	 *
	 * @param DataContentBase $content
	 */
	public function __construct(
		private DataContentBase $content,
		?JsonSerializer $jsonSerializer = null
	) {
		$this->jsonSerializer = $jsonSerializer ?? new JsonSerializer();
	}

	#region function

	private function convertText(StaticDataContent $content): string
	{
		return Text::toString($content->data);
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

	private function convertJson(StaticDataContent $content): string
	{
		assert(is_array($content->data));
		return $this->convertJsonCore($content->data);
	}

	private function convertRaw(StaticDataContent $content): string
	{
		if ($content->data instanceof Binary) {
			return $content->data->raw;
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

		$response->header->setContentType(ContentType::create($this->content->mime));

		if ($this->content instanceof IDownloadContent) {
			$fileName = urlencode($this->content->getFileName());
			$response->header->addValue('Content-Disposition', "attachment; filename*=UTF-8''$fileName");
			$response->header->addValue('X-Content-Type-Options', 'nosniff');
			if ($this->content instanceof StaticDataContent) {
				$response->header->addValue('Connection', 'close');
				if ($this->content->data instanceof Binary) {
					$response->header->addValue('Content-Length', (string)$this->content->data->count());
				} elseif (is_string($this->content->data)) {
					$response->header->addValue('Content-Length', (string)strlen($this->content->data));
				}
				$response->content = $this->convertRaw($this->content);
			}
		} else {
			if ($this->content instanceof StaticDataContent) {
				$response->content = match ($this->content->mime) {
					Mime::TEXT => $this->convertText($this->content),
					Mime::JSON => $this->convertJson($this->content),
					default => $this->convertRaw($this->content),
				};
			}
		}

		if ($this->content instanceof ICallbackContent) {
			$response->header->addValue('Transfer-Encoding', "chunked");
			$response->content = $this->content;
		}

		return $response;
	}

	#endregion
}
