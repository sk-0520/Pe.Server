<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\StringUtility;

/**
 * HTTPレスポンス出力処理。
 *
 * 本クラス処理前後(execute前後)には何も出力しないのがお行儀良い処理。
 */
class ResponsePrinter
{
	public function __construct(
		/** @readonly */
		private HttpRequest $request,
		/** @readonly */
		private HttpResponse $response
	) {
	}

	/**
	 * 出力。
	 *
	 * @return void
	 */
	public function execute(): void
	{
		// リダイレクト未設定の場合はステータスコード設定
		if (!$this->response->header->existsRedirect()) {
			http_response_code($this->response->status->getCode());
		}

		// 設定済みヘッダ出力
		foreach ($this->response->header->getHeaders() as $name => $value) {
			header($name . ': ' . $value);
		}

		if ($this->response->header->existsRedirect()) {
			$redirect = $this->response->header->getRedirect();
			if ($redirect->status->is(HttpStatus::moved())) {
				header('Location: ' . $redirect->url);
			} else {
				header('Location: ' . $redirect->url, true, $redirect->status->getCode());
			}
			return;
		}

		// ヘッダ: Content-Length
		if ($this->response->content instanceof Binary) {
			header('Content-Length: ' . $this->response->content->getLength());
		} else if (is_string($this->response->content)) {
			header('Content-Length: ' . StringUtility::getByteCount($this->response->content));
		}

		if ($this->request->httpMethod->is(HttpMethod::head())) {
			// HEAD 処理は出力を抑制
			return;
		}

		if ($this->response->content instanceof ICallbackContent) {
			// 処理は自分で出力を頑張ること
			$this->response->content->output();
		} else if ($this->response->content instanceof Binary) {
			echo $this->response->content->getRaw();
		} else {
			echo $this->response->content;
		}
	}
}
