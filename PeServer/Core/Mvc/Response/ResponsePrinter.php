<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Response;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mvc\Content\CallbackChunkedContent;
use PeServer\Core\Mvc\Content\EventStreamContentBase;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Text;
use PeServer\Core\Throws\Throws;

/**
 * HTTPレスポンス出力処理。
 *
 * 本クラス処理前後(execute前後)には何も出力しないのがお行儀良い処理。
 */
class ResponsePrinter
{
	#region

	/** @var positive-int */
	public int $chunkSize = 4 * 1024;

	#endregion

	/**
	 * 生成。
	 *
	 * @param HttpRequest $request
	 * @param HttpResponse $response
	 */
	public function __construct(
		protected readonly HttpRequest $request,
		protected readonly HttpResponse $response
	) {
		//NOP
	}

	#region function

	/**
	 * 応答ヘッダ: Content-Length を取得。
	 *
	 * @return int 0以上の場合は決定された出力byte数。負数は不明。
	 * @phpstan-return non-negative-int|ICallbackContent::UNKNOWN
	 */
	private function getContentLength(): int
	{
		if ($this->response->content instanceof ICallbackContent) {
			$length = $this->response->content->getLength();
			if (0 <= $length) {
				return $length;
			}
		} elseif ($this->response->content instanceof Stream) {
			$meta = $this->response->content->getMetaData();
			if ($meta->seekable) {
				$currentOffset = $this->response->content->getOffset();
				$this->response->content->seekTail();
				$lastOffset = $this->response->content->getOffset();
				$length = $lastOffset - $currentOffset;
				assert(0 <= $length);
				$this->response->content->seek($currentOffset, Stream::WHENCE_HEAD);
				return $length;
			}
		} elseif ($this->response->content instanceof Binary) {
			return $this->response->content->count();
		} elseif (is_string($this->response->content)) {
			return Text::getByteCount($this->response->content);
		}

		return ICallbackContent::UNKNOWN;
	}

	/**
	 * 応答本文出力処理。
	 */
	private function output(bool $lengthIsContentLength): void
	{
		if ($this->response->content instanceof ICallbackContent) {
			// 処理は自分で出力を頑張ること
			$this->response->content->output();
		} elseif ($this->response->content instanceof Stream) {
			if ($lengthIsContentLength) {
				while (!$this->response->content->isEnd()) {
					$chunk = $this->response->content->readBinary($this->chunkSize);
					echo $chunk->raw;
				}
			} else {
				// phpstan で検知されるので変数化
				$stream = $this->response->content;
				$content = new CallbackChunkedContent(function () use ($stream) {
					while (!$stream->isEnd()) {
						$chunk = $stream->readBinary($this->chunkSize);
						yield $chunk;
					}
				});
				$content->output();
			}
			$this->response->content->dispose();
		} elseif ($this->response->content instanceof Binary) {
			echo $this->response->content->raw;
		} else {
			echo $this->response->content;
		}
	}

	/**
	 * 応答出力。
	 *
	 * @return void
	 */
	public function execute(): void
	{
		// リダイレクト未設定の場合はステータスコード設定
		if (!$this->response->header->existsRedirect()) {
			http_response_code($this->response->status->value);
		}

		// 設定済みヘッダ出力
		foreach ($this->response->header->getHeaders() as $name => $value) {
			header($name . ': ' . $value);
		}

		if ($this->response->header->existsRedirect()) {
			$redirect = $this->response->header->getRedirect();
			if ($redirect->status === HttpStatus::MovedPermanently) {
				header('Location: ' . $redirect->url->toString());
			} else {
				header('Location: ' . $redirect->url->toString(), true, $redirect->status->value);
			}
			return;
		}

		// ヘッダ
		$contentLength = $this->getContentLength();
		$lengthIsContentLength = 0 <= $contentLength;
		if ($lengthIsContentLength) {
			header('Content-Length: ' . $contentLength);
		} else {
			if ($this->response->content instanceof EventStreamContentBase) {
				header("X-Accel-Buffering: no");
				header("Cache-Control: no-cache");
			} else {
				header('Transfer-Encoding: chunked');
			}
		}

		if ($this->request->httpMethod === HttpMethod::Head) {
			// HEAD 処理は出力を抑制
			return;
		}

		OutputBuffer::httpFlush();

		$this->output($lengthIsContentLength);
	}

	#endregion
}
