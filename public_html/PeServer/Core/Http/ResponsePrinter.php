<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Bytes;
use PeServer\Core\Http\HttpResponse;


class ResponsePrinter
{
	public function __construct(
		private HttpRequest $request,
		private HttpResponse $response
	) {
	}

	/**
	 * 出力。
	 *
	 * @return void
	 */
	public function print(): void
	{
		// リダイレクト未設定の場合はステータスコード設定
		if (!$this->response->header->existsRedirect()) {
			http_response_code($this->response->status->getCode());
		}

		// ヘッダ出力
		foreach ($this->response->header->getHeaders() as $name => $value) {
			header($name . ': ' . $value);
		}

		if ($this->response->header->existsRedirect()) {
			$redirect = $this->response->header->getRedirect();
			if (isset($redirect['status'])) {
				header('Location: ' . $redirect['url'], true, $redirect['status']->getCode());
			} else {
				header('Location: ' . $redirect['url']);
			}
			return;
		}

		if ($this->request->httpMethod->is(HttpMethod::head())) {
			// HEAD 処理は出力を抑制
			return;
		}

		if ($this->response->content instanceof ICallbackContent) {
			// 処理は自分で出力を頑張ること
			$this->response->content->output();
		} else if ($this->response->content instanceof Bytes) {
			echo $this->response->content->getRaw();
		} else {
			echo $this->response->content;
		}
	}
}
