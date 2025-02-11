<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use Closure;
use Generator;
use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\DataContent;

/**
 * `Stream` 処理。
 *
 * TODO: 用途的に実ファイルのダウンロードなので ChunkedContentBase である意味はない
 */
class StreamContent extends ChunkedContentBase implements IDownloadContent
{
	#region variable

	/** @var positive-int */
	public int $chunkSize = 4 * 1024;

	#endregion

	/**
	 * 生成。
	 *
	 * @param Stream $stream 出力するストリーム。出力後、閉じられる。
	 * @param non-empty-string $fileName
	 * @param non-empty-string|\PeServer\Core\Mime::* $mime
	 */
	public function __construct(private Stream $stream, private readonly string $fileName, string $mime = Mime::STREAM)
	{
		parent::__construct($mime);
	}

	#region ChunkedContentBase

	protected function getIterator(): Iterator
	{
		while (!$this->stream->isEnd()) {
			$chunk = $this->stream->readBinary($this->chunkSize);
			yield $chunk;
		}
		$this->stream->dispose();
	}

	#endregion

	#region IDownloadContent

	public function getFileName(): string
	{
		return $this->fileName;
	}

	#endregion
}
