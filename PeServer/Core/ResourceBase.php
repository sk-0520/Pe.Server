<?php

declare(strict_types=1);

namespace PeServer\Core;

use TypeError;
use PeServer\Core\DisposerBase;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\ObjectDisposedException;
use PeServer\Core\Throws\ResourceInvalidException;

/**
 * `resource` 型を持ち運ぶ。
 *
 * 細かい処理は継承側で対応する。
 *
 * @template TResource
 * @property TResource $raw 公開リソース。
 */
abstract class ResourceBase extends DisposerBase
{
	#region variable

	protected string $resourceType;

	#endregion

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

	#region function

	/**
	 * リソース型を解放する。
	 */
	abstract protected function release(): void;

	/**
	 * リソース型は自身の扱えるものか。
	 *
	 * @param string $resourceType
	 * @return bool
	 * @phpstan-pure
	 */
	abstract protected function isValidType(string $resourceType): bool;

	public function __get(string $name): mixed
	{
		switch ($name) {
			case 'raw':
				return $this->resource;

			default:
				throw new NotImplementedException($name);
		}
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		// $resourceType = get_resource_type($this->resource);
		// if ($resourceType === 'Unknown') {
		// 	throw new ObjectDisposedException();
		// }

		$this->release();

		$this->resource = null; //@phpstan-ignore-line はーいーるーのー
	}

	#endregion
}
