<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use \Throwable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\DownloadFileContent;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;

class ManagementDatabaseDownloadLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//nop
	}


	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode !== LogicCallMode::Submit) {
			throw new HttpStatusException(HttpStatus::internalServerError());
		}

		$connection = $this->config->setting->persistence->default->connection;
		list($db, $target) = Text::split($connection, ':', 2);
		$this->logger->debug("db: {0}, {1}", $db, $target);
		if($db !== 'sqlite') {
			throw new HttpStatusException(HttpStatus::internalServerError());
		}

		$name = Path::getFileName($target);
		$content = File::readContent($target);

		$this->setDownloadContent('application/x-sqlite3', $name, $content);
	}
}
