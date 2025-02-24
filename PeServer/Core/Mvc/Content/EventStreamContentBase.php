<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Time;
use PeServer\Core\TypeUtility;

abstract class EventStreamContentBase extends DataContentBase implements ICallbackContent
{
	#region variable

	protected JsonSerializer $jsonSerializer;
	private Encoding $outputEncoding;
	private Binary $newLine;

	#endregion

	/**
	 * 生成。
	 */
	public function __construct(JsonSerializer $jsonSerializer = null)
	{
		if ($this instanceof IDownloadContent) {
			throw new NotSupportedException("IDownloadContent");
		}

		$this->jsonSerializer = $jsonSerializer ?? new JsonSerializer(JsonSerializer::SAVE_UNESCAPED_SLASHES | JsonSerializer::SAVE_UNESCAPED_UNICODE);
		$this->outputEncoding = Encoding::getUtf8();
		$this->newLine = $this->outputEncoding->getBinary("\r\n");
		parent::__construct(HttpStatus::OK, Mime::EVENT_STREAM);
	}

	#region function

	/** @return Iterator<EventStreamMessage> */
	abstract protected function getIterator(): Iterator;

	protected function outputContent(string $field, Binary $value): void
	{
		// ｇｄｇｄここに極まる
		echo $this->outputEncoding->getBinary($field)->raw, ": ", $value->raw, $this->newLine;
	}

	protected function outputClose(): void
	{
		$this->outputContent("data", new Binary("<DONE>"));
	}

	#endregion

	#region ICallbackContent

	final public function getLength(): int
	{
		return ICallbackContent::UNKNOWN;
	}

	final public function output(): void
	{
		$iterator = $this->getIterator();
		foreach ($iterator as $message) {
			if ($message->event !== null) {
				$this->outputContent("event", new Binary($message->event));
			}
			if ($message->id !== null) {
				$this->outputContent("id", new Binary($message->id));
			}
			if ($message->retry !== null) {
				$ms = (string)round(Time::getTotalMilliseconds($message->retry));
				$this->outputContent("retry", new Binary($ms));
			}

			if (is_array($message->data) || is_object($message->data)) {
				$dataBinary = $this->jsonSerializer->save($message->data);
				$this->outputContent("data", $dataBinary);
			} else {
				$lines = Text::splitLines($message->data);
				foreach ($lines as $line) {
					$dataBinary = new Binary($line);
					$this->outputContent("data", $dataBinary);
				}
			}

			echo $this->newLine->raw;

			OutputBuffer::httpFlush();
		}

		$this->outputClose();
		echo $this->newLine->raw;
		OutputBuffer::httpFlush();
	}

	#endregion
}
