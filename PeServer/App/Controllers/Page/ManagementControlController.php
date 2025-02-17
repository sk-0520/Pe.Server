<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\ManagementControl\ManagementControlBackupDownloadLogic;
use PeServer\App\Models\Domain\Page\ManagementControl\ManagementControlBackupListLogic;
use PeServer\App\Models\Domain\Page\ManagementControl\ManagementControlUserListLogic;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * [PAGE] 管理者用操作コントローラ。
 */
final class ManagementControlController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function user_list_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementControlUserListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('user_list', $logic->getViewData());
	}

	public function backup_list_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementControlBackupListLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('backup_list', $logic->getViewData());
	}

	public function backup_detail_get(): IActionResult
	{
		$logic = $this->createLogic(ManagementControlBackupDownloadLogic::class);
		$logic->run(LogicCallMode::Submit);

		return $this->data($logic->getContent());
	}
}
