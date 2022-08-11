<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\DiRegisterContainer;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Mvc\ControllerFactory;
use PeServer\Core\Mvc\IControllerFactory;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Throws\NotImplementedException;

/**
 * プログラムスタートアップ処理。
 */
class ProgramStartup
{
	public const MODE_WEB = 'Web';
	public const MODE_CLI = 'Cli';
	public const MODE_TEST = 'Test';

	/**
	 * 共通セットアップ処理。
	 *
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupCommon(array $options, IDiRegisterContainer $container): void
	{
		setlocale(LC_ALL, 'C');
		mb_language('uni');
		Encoding::setDefaultEncoding(Encoding::getUtf8());

		$container->registerMapping(ILogger::class, NullLogger::class);

		/*
		$environment = new Environment2($options['environment'], $options['revision']);
		$container->registerValue($environment);

		if (!$environment->isTest()) {
			$errorHandler = $container->new(ErrorHandler::class);
			$errorHandler->register();
		}
		*/
	}

	/**
	 * Webアプリケーション用セットアップ処理。
	 *
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupWebService(array $options, IDiRegisterContainer $container): void
	{
		$container->registerClass(SpecialStore::class, DiItem::LIFECYCLE_SINGLETON);
		$container->registerMapping(IControllerFactory::class, ControllerFactory::class);
	}

	/**
	 * CLIアプリケーション用セットアップ処理。
	 *
	 * つかわんよ。
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupCliService(array $options, IDiRegisterContainer $container): void
	{
		//NONE
	}

	/**
	 * テスト用セットアップ処理。
	 *
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupTestService(array $options, IDiRegisterContainer $container): void
	{
		//NONE
	}

	/**
	 * 追加セットアップ処理。
	 *
	 * Coreでは何もしないので拡張側で好きにどうぞ。
	 *
	 * @param array{environment:string,revision:string} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupCustom(array $options, IDiRegisterContainer $container): void
	{
		//NONE
	}

	/**
	 * Undocumented function
	 *
	 * @param string $mode
	 * @param array{environment:string,revision:string} $options
	 * @return IDiRegisterContainer
	 */
	public function setup(string $mode, array $options): IDiRegisterContainer
	{
		$container = new DiRegisterContainer();

		$this->setupCommon($options, $container);

		switch ($mode) {
			case self::MODE_WEB:
				$this->setupWebService($options, $container);
				break;

			case self::MODE_CLI:
				$this->setupCliService($options, $container);
				break;

			case self::MODE_TEST:
				$this->setupTestService($options, $container);
				break;

			default:
				throw new NotImplementedException($mode);
		}

		$this->setupCustom($options, $container);

		return $container;
	}
}
