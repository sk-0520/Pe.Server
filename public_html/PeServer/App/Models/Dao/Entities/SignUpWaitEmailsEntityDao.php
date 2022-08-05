<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;

class SignUpWaitEmailsEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	public function selectExistsToken(string $token, int $limitMinutes): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				sign_up_wait_emails
			where
				sign_up_wait_emails.token = :token
				and
				(STRFTIME('%s', CURRENT_TIMESTAMP) - STRFTIME('%s', sign_up_wait_emails.timestamp)) < :limit_minutes * 60

			SQL,
			[
				'token' => $token,
				'limit_minutes' => $limitMinutes,
			]
		);
	}

	public function selectEmail(string $token): string
	{
		return $this->context->querySingle(
			<<<SQL

			select
				sign_up_wait_emails.email
			from
				sign_up_wait_emails
			where
				sign_up_wait_emails.token = :token

			SQL,
			[
				'token' => $token,
			]
		)->fields['email'];
	}

	/**
	 * Undocumented function
	 *
	 * @param integer $markEmail
	 * @-return array<array{mark_email:int,token:string,email:string}>
	 */
	public function selectLikeEmails(int $markEmail): DatabaseTableResult
	{
		/** @-var array<array{mark_email:int,token:string,email:string}> */
		return $this->context->query(
			<<<SQL

			select
				sign_up_wait_emails.mark_email,
				sign_up_wait_emails.token,
				sign_up_wait_emails.email
			from
				sign_up_wait_emails
			where
				sign_up_wait_emails.mark_email = :mark_email

			SQL,
			[
				'mark_email' => $markEmail,
			]
		);
	}

	public function insertEmail(string $token, string $email, int $markEmail, string $ipAddress, string $userAgent): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				sign_up_wait_emails
				(
					token,
					email,
					mark_email,
					timestamp,
					ip_address,
					user_agent
				)
				values
				(
					:token,
					:email,
					:mark_email,
					CURRENT_TIMESTAMP,
					:ip_address,
					:user_agent
				)

			SQL,
			[
				'token' => $token,
				'email' => $email,
				'mark_email' => $markEmail,
				'ip_address' => $ipAddress,
				'user_agent' => $userAgent,
			]
		);
	}

	public function deleteToken(string $token): void
	{
		$this->context->deleteByKey(
			<<<SQL

			delete
			from
				sign_up_wait_emails
			where
				sign_up_wait_emails.token = :token

			SQL,
			[
				'token' => $token,
			]
		);
	}
}
