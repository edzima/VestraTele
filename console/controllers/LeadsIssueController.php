<?php

namespace console\controllers;

use common\components\LeadsFromIssueCreator;
use common\models\issue\Issue;
use common\modules\lead\models\Lead;
use yii\console\Controller;
use yii\db\Connection;
use yii\helpers\Console;

class LeadsIssueController extends Controller {

	public function actionLeadCopy(int $sourceId): void {
		$count = 0;
		foreach (Lead::find()
			->withoutUsers()
			->andWhere(['source_id' => 128])
			->select([
				'data',
				'name',
				'phone',
				'postal_code',
			])
			->asArray()
			->batch(1000) as $rows) {

			$leadsRows = [];
			foreach ($rows as $row) {
				foreach ($row as $key => $value) {
					if (is_array($value)) {
						unset($row[$key]);
					}
				}
				$row['date_at'] = date(DATE_ATOM);
				$row['source_id'] = $sourceId;
				$row['provider'] = Lead::PROVIDER_COPY;
				$leadsRows[] = $row;
			}
			if (!empty($leadsRows)) {
				$count += Lead::getDb()
					->createCommand()
					->batchInsert(Lead::tableName(), [
						'data',
						'name',
						'phone',
						'postal_code',
						'date_at',
						'source_id',
						'provider',
					], $leadsRows)
					->execute();
			}
		}
		Console::output('Copy Leads: ' . $count);
	}

	public function actionCreateLeads(int $leadSource, int $issueTypeId = null, array $agents = []) {

		$creator = new LeadsFromIssueCreator([
			'issueDb' => $this->issuesDb(),
			'leadDb' => $this->leadsDb(),
		]);
		$creator->leadsColumns = [
			'name' => 'customer.fullName',
			'phone' => 'customer.phone',
			'email' => 'customer.userProfile.email',
			'source_id' => function () use ($leadSource) {
				return $leadSource;
			},
		];

		$issueQuery = Issue::find()
			->joinWith('customer.userProfile')
			->joinWith('agent.userProfile')
			->groupBy(Issue::tableName() . '.id');
		if ($issueTypeId !== null) {
			$issueQuery->type($issueTypeId);
		}
		if (!empty($agents)) {
			$issueQuery->agents($agents);
		}

		$count = $creator->createLeads($issueQuery);
		Console::output('Create leads: ' . $count . ' from Issues: ' . $issueQuery->count('*', $creator->issueDb));
	}

	protected function issuesDb(): Connection {
		return new Connection([
			'dsn' => $_ENV['ISSUE_DB_DSN'],
			'username' => $_ENV['ISSUE_DB_USERNAME'],
			'password' => $_ENV['ISSUE_DB_PASSWORD'],
		]);
	}

	protected function leadsDb(): Connection {
		return new Connection([
			'dsn' => $_ENV['LEADS_DB_DSN'],
			'username' => $_ENV['LEADS_DB_USERNAME'],
			'password' => $_ENV['LEADS_DB_PASSWORD'],
		]);
	}
}
