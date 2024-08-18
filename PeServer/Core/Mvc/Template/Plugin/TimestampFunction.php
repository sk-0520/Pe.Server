<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Throws\ArgumentException;

class TimestampFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	//@phpstan-ignore method.unused
	private function createHtml(DateTimeInterface $utc, DateTimeInterface $timestamp, string $format): string
	{
		$datetime = $utc->format(DateTimeInterface::ISO8601_EXPANDED);
		$display = $timestamp->format($format);
		return "<time datetime='$datetime'>$display</time>";
	}

	#endregion

	#region ITemplateModifier

	public function getFunctionName(): string
	{
		return 'timestamp';
	}

	#endregion

	#region TemplateFunctionBase

	protected function functionBodyImpl(): string
	{
		//@phpstan-ignore booleanOr.alwaysTrue, instanceof.alwaysFalse
		if (!isset($this->params['value']) || !($this->params['value'] instanceof DateTimeInterface)) {
			if (isset($this->params['fallback'])) {
				/** @var string */
				return $this->params['fallback'];
			}
			throw new ArgumentException('value');
		}

		/** @var DateTimeInterface */
		$value = $this->params['value']; //@phpstan-ignore deadCode.unreachable

		/** @var string */
		$timezoneText = $this->params['timezone'] ?? 'Asia/Tokyo';
		$timezone = new DateTimeZone($timezoneText);

		$work = DateTime::createFromInterface($value);
		$work->setTimezone($timezone);

		if (isset($this->params['format'])) {
			/** @var string */
			$format = $this->params['format'];
			return $this->createHtml($value, $work, $format);
		}

		return $this->createHtml($value, $work, 'Y-m-d\\TH:i:sP');
	}

	#endregion
}
