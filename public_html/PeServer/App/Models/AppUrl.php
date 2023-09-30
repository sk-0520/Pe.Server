<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Code;
use PeServer\Core\Text;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;

/**
 * アプリ設定のURLをなるべく事故らず使う処理。
 */
class AppUrl
{
	#region variable

	private Url|null $publicUrl = null;

	#endregion

	public function __construct(
		private AppConfiguration $appConfiguration
	) {
	}

	#region function

	/**
	 * ドメインを取得。
	 *
	 * @return string
	 */
	public function getDomain(): string
	{
		return $this->appConfiguration->setting->config->address->domain;
	}

	/**
	 * 公開URLを取得。
	 *
	 * @return Url
	 */
	public function getPublicUrl(): Url
	{
		if ($this->publicUrl === null) {
			$url = Text::replaceMap(
				Code::toLiteralString($this->appConfiguration->setting->config->address->publicUrl),
				[
					'DOMAIN' => $this->getDomain()
				]
			);

			$this->publicUrl = Url::parse($url);
		}

		return $this->publicUrl;
	}

	/**
	 * 公開URLに対してパスの追加とクエリを設定する。
	 *
	 * Url はパスを全置換えする挙動のため公開URLがパス付き(リバースプロキシ経由とか)の可能性があるのでそのあたりをいい感じにする感じ
	 *
	 * ※本来は IUrlHelper の役割だと思ってたけどそっちに手を入れる前にこっちが実装されてしまった感。
	 *
	 * @param UrlPath $path
	 * @param UrlQuery|null $query
	 * @return Url
	 */
	public function addPublicUrl(UrlPath $path, ?UrlQuery $query = null): Url
	{
		$url = $this->getPublicUrl();

		if (!$path->isEmpty()) {
			$elements = $path->getElements();
			if (0 < count($elements)) {
				$path = $url->path->add($elements);
				$url = $url->changePath($path);
			}
		}

		if ($query !== null) {
			$url = $url->changeQuery($query);
		}

		return $url;
	}


	#endregion
}
