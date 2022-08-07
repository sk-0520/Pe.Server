<?php

declare(strict_types=1);

namespace PeServer\Core;

use \TypeError;
use PeServer\Core\DisposerBase;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ObjectDisposedException;
use PeServer\Core\Throws\ResourceInvalidException;

/**
 * `resource` 型を持ち運ぶ。
 *
 * 細かい処理は継承側で対応する。
 *
 * @template TResource
 */
abstract class ResourceBase extends DisposerBase
{
	protected string $resourceType;
	/**
	 * 生成。
	 *
	 * @param $resource 持ち運ぶリソース。
	 * @phpstan-param TResource $resource
	 */
	public function __construct(
		protected $resource
	) {
		if (!is_resource($resource)) {
			if (!$resource) {
				throw new TypeError('$resource');
			}
		}

		$this->resourceType = get_resource_type($resource);
		if ($this->resourceType === 'Unknown') {
			throw new ArgumentException('$resource: ' . $this->resourceType);
		}

		if (!$this->isValidType($this->resourceType)) {
			throw new ResourceInvalidException('$resource: ' . $this->resourceType);
		}
	}

	protected function disposeImpl(): void
	{
		// $resourceType = get_resource_type($this->resource);
		// if ($resourceType === 'Unknown') {
		// 	throw new ObjectDisposedException();
		// }

		$this->release();

		$this->resource = null; //@phpstan-ignore-line はーいーるーのー
	}

	/**
	 * リソース型を解放する。
	 */
	protected abstract function release(): void;

	/**
	 * リソース型は自身の扱えるものか。
	 *
	 * @param string $resourceType
	 * @return bool
	 */
	protected abstract function isValidType(string $resourceType): bool;
}
