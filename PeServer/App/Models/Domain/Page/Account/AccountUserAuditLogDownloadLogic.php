<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppTemporary;
use PeServer\App\Models\Configuration\AppSetting;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Archive\ArchiveEntry;
use PeServer\Core\Archive\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\FileCleanupStream;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeUtility;
use PeServer\Core\Utc;
use ZipArchive;

class AccountUserAuditLogDownloadLogic extends PageLogicBase
{
	#region define

	//public const RAW_LOG_COUNT = 5;
	public const RAW_LOG_COUNT = 50;

	#endregion

	public function __construct(LogicParameter $parameter, private AppTemporary $appTemporary)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->getAuditUserInfo();
		if ($userInfo === null) {
			throw new InvalidOperationException();
		}
		$userId = $userInfo->getUserId();

		// $pageNumber = Pagination::FIRST_PAGE_NUMBER;
		// if ($callMode === LogicCallMode::Submit) {
		// 	$requestPageNumber = $this->getRequest('page_number');
		// 	if (TypeUtility::tryParseInteger($requestPageNumber, $temp)) {
		// 		$pageNumber = $temp;
		// 	}
		// }

		$database = $this->openDatabase();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($database);

		$result = $userAuditLogsEntityDao->selectAuditLogsFromUserId($userId);

		if (count($result->rows) < self::RAW_LOG_COUNT) {
			$jsonSerializer = new JsonSerializer();
			$items = $jsonSerializer->save($result->rows);
			$this->setDownloadContent(Mime::JSON, "audit-log.json", $items);
		} else {
			$workDirPath = $this->appTemporary->getDatabaseDownloadDirectory($userId);
			$this->logger->info("audit temp dir: {0}", $workDirPath);
			$auditFilePath = Path::combine($workDirPath, $this->appTemporary->createFileName($this->beginTimestamp, "json"));
			$zipFilePath = Path::combine($workDirPath, $this->appTemporary->createFileName($this->beginTimestamp, "zip"));

			// JSONを一旦ファイルに出力する
			// なんでこんな面倒な処理してるかというと、データが多くなると PHP 側がメモリ不足で落ちる
			// とりあえずロジックで何とかする方針で組んだが、どうにもならんくなったら PHP 設定を変更する
			// 2025-05-11 追記: DB 周りのログがえっぐいことがわかったので 2KB 制限追加, どうしても確認したいのであれば DB 自体を DL すること
			$textLength = 2 * 1024;
			$jsonSerializer = new JsonSerializer();
			File::writeContent($auditFilePath, new Binary("[" . PHP_EOL));
			foreach ($result->rows as $i => $row) {
				if (0 < $i) {
					File::appendContent($auditFilePath, new Binary("," . PHP_EOL));
				}
				$row["info"] = $textLength < Text::getLength($row["info"])
					? Text::substring($row["info"], 0, $textLength) . "...<省略>"
					: $row["info"]
				;
				$json = $jsonSerializer->save($row);
				$nestObject = Text::join(
					PHP_EOL,
					Arr::map(Text::splitLines($json->raw), fn($a) => "    " . $a)
				);
				File::appendContent($auditFilePath, new Binary($nestObject));
			}
			File::appendContent($auditFilePath, new Binary(PHP_EOL . "]" . PHP_EOL));

			Archiver::compressZip(
				$zipFilePath,
				[
					// @phpstan-ignore argument.type
					new ArchiveEntry($auditFilePath, "audit-log.json")
				]
			);
			File::removeFile($auditFilePath);

			$data = FileCleanupStream::read($zipFilePath);
			$this->setDownloadContent(Mime::ZIP, "audit-log.zip", $data);
		}
	}

	#endregion
}
