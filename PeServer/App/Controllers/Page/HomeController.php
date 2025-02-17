<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Page;

use Exception;
use PeServer\App\Controllers\Page\PageControllerBase;
use PeServer\App\Models\Domain\Page\Home\HomeAboutLogic;
use PeServer\App\Models\Domain\Page\Home\HomeApiDocumentLogic;
use PeServer\App\Models\Domain\Page\Home\HomeContactLogic;
use PeServer\App\Models\Domain\Page\Home\HomeIndexLogic;
use PeServer\App\Models\Domain\Page\Home\HomePrivacyLogic;
use PeServer\App\Models\Domain\Page\Home\HomeWildcardLogic;
use PeServer\Core\Binary;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\StreamingContent;
use PeServer\Core\Mvc\Controller\ControllerArgument;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateParameter;

/**
 * [PAGE] ホームコントローラ。
 */
final class HomeController extends PageControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	public function index(): IActionResult
	{
		$logic = $this->createLogic(HomeIndexLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('index', $logic->getViewData());
	}

	public function privacy(): IActionResult
	{
		$logic = $this->createLogic(HomePrivacyLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('privacy', $logic->getViewData());
	}

	public function contact_get(): IActionResult
	{
		$logic = $this->createLogic(HomeContactLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('contact', $logic->getViewData());
	}

	public function about(): IActionResult
	{
		$logic = $this->createLogic(HomeAboutLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('about', $logic->getViewData());
	}

	public function api(): IActionResult
	{
		$logic = $this->createLogic(HomeApiDocumentLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->view('api', $logic->getViewData());
	}

	public function wildcard(): IActionResult
	{
		$logic = $this->createLogic(HomeWildcardLogic::class);
		$logic->run(LogicCallMode::Initialize);

		return $this->data($logic->getContent());
	}

	public function exception(): IActionResult
	{
		throw new Exception();
	}

	public function streaming_html(): IActionResult
	{
		return $this->view(
			'dev_streaming',
			new TemplateParameter(
				HttpStatus::OK,
				[],
				[]
			)
		);
	}

	private function wait(): void
	{
		//usleep(500);
		sleep(1);
	}

	public function streaming_ajax(): IActionResult
	{
		return $this->data(new StreamingContent(function () {
			$this->wait();
			yield new Binary("abc");
			$this->wait();
			yield new Binary("defghi");
			$this->wait();
			yield new Binary("jklmnoopq");
			$this->wait();
			yield new Binary("rstuvwxyz012");
		}, Mime::TEXT));
	}
}
