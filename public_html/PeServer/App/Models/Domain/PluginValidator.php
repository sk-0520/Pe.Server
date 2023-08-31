<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\I18n;
use PeServer\Core\Uuid;
use PeServer\Core\TrueKeeper;
use PeServer\Core\Web\UrlUtility;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Text;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Mvc\IValidationReceiver;
use PeServer\App\Models\Domain\ValidatorBase;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;


class PluginValidator extends ValidatorBase
{
	public const PLUGIN_NAME_RANGE_MIN = 4;
	public const PLUGIN_NAME_RANGE_MAX = 64;
	public const PLUGIN_DISPLAY_NAME_LENGTH = 500;
	public const PLUGIN_DESCRIPTION_LENGTH = 1000;


	public function __construct(IValidationReceiver $receiver, Validator $validator)
	{
		parent::__construct($receiver, $validator);
	}

	public function isPluginId(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			/** @var string $value isNotWhiteSpace */
			$trueKeeper = new TrueKeeper();

			if (!Uuid::isGuid($value)) {
				$trueKeeper->state = false;
				$this->receiver->receiveErrorMessage($key, I18n::message('error/illegal_plugin_id'));
			}

			return $trueKeeper->state;
		}

		return false;
	}

	public function isPluginName(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			/** @var string $value isNotWhiteSpace */
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inRange($key, self::PLUGIN_NAME_RANGE_MIN, self::PLUGIN_NAME_RANGE_MAX, $value);
			$trueKeeper->state = $this->validator->isMatch($key, '/^[a-zA-Z0-9\-_!=\(\)\[\]\.]+$/', $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isDisplayName(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			/** @var string $value isNotWhiteSpace */
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::PLUGIN_DISPLAY_NAME_LENGTH, $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isCheckUrl(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			/** @var string $value isNotWhiteSpace */

			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->isWebsite($key, $value);

			if (Environment::isProduction()) {
				// チェック用URLなのでワッケ分からんURLの登録は禁止する(検証環境はいい)
				$trueKeeper->state = $this->validator->isNotMatch($key, UrlUtility::LOCALHOST_PATTERN, $value);
			}

			return $trueKeeper->state;
		}

		return false;
	}

	public function isDescription(string $key, ?string $value): bool
	{
		if (!Text::isNullOrWhiteSpace($value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::PLUGIN_DESCRIPTION_LENGTH, $value);

			return $trueKeeper->state;
		}

		return true;
	}


	public function isFreePluginId(DatabaseContext $database, string $key, string $pluginId): bool
	{
		$pluginsEntityDao = new PluginsEntityDao($database);

		if ($pluginsEntityDao->selectExistsPluginId($pluginId)) {
			$this->receiver->receiveErrorMessage($key, I18n::message('error/unusable_plugin_id'));
			return false;
		}

		return true;
	}

	public function isFreePluginName(DatabaseContext $database, string $key, string $pluginName): bool
	{
		$pluginsEntityDao = new PluginsEntityDao($database);

		if ($pluginsEntityDao->selectExistsPluginName($pluginName)) {
			$this->receiver->receiveErrorMessage($key, I18n::message('error/unusable_plugin_name'));
			return false;
		}

		return true;
	}
}
