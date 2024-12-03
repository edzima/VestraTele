<?php

namespace console\controllers;

use backend\modules\issue\models\IssueStage;
use common\components\LeadsFromIssueCreator;
use common\models\issue\Issue;
use common\models\user\query\UserProfileQuery;
use common\models\user\User;
use common\modules\lead\models\Lead;
use Yii;
use yii\console\Controller;
use yii\db\Connection;
use yii\helpers\Console;
use yii\helpers\Json;

class LeadsIssueController extends Controller {

	public function actionUsersIds(string $usersString): array {
		$users = explode(PHP_EOL, $usersString);
		Console::output('Find users in string: ' . PHP_EOL . print_r($users, true));

		$usersIds = [];
		$notFound = [];
		foreach ($users as $userName) {
			$user = User::find()->joinWith([
				'userProfile' => function (UserProfileQuery $query) use ($userName) {

					$query->withFullName(trim($userName));
				},
			])->one();
			if ($user) {
				$usersIds[$userName] = $user->id;
			} else {
				$notFound[] = $userName;
			}
		}

		Console::output(print_r($usersIds, true));

		Console::output('Users in string: ' . count($users));
		Console::output('Find users id DB: ' . count($usersIds));
		Console::output('Not Find:');
		Console::output(print_r($notFound, true));
		return $usersIds;
	}

	public function actionLeadCopy(int $newSourceId, int $oldSourceId): void {
		$count = 0;
		foreach (Lead::find()
			->withoutUsers()
			->andWhere(['source_id' => $oldSourceId])
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
				$row['source_id'] = $newSourceId;
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

	public function actionCreateLeads(int $leadSource, int $issueTypeId = null, array $agents = []): void {
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
			'date_at' => function (): string {
				return date(DATE_ATOM);
			},
			'data' => function (Issue $issue): string {
				$content = [];
				$content[] = $issue->getIssueName();
				$content[] = Yii::t('issue', 'Stage') . ': ' . $issue->getStageName();
				if ($issue->isArchived()) {
					$hasIssueStageWithoutArchive = false;
					foreach ($issue->issueNotes as $note) {
						if ($note->isForStageChange()) {
							$stage = $note->getEntityId();
							if (!in_array($stage, IssueStage::ARCHIVES_IDS)) {
								$content[] = $note->title . ' - ' . Yii::$app->formatter->asDate($note->publish_at);
								$hasIssueStageWithoutArchive = true;
								break;
							}
						}
					}
					if (!$hasIssueStageWithoutArchive) {
						$detailsNote = null;
						foreach ($issue->issueNotes as $note) {
							if (!$note->isSms()) {
								$detailsNote = $note;
								break;
							}
						}
						if ($detailsNote !== null) {
							$content[] = $detailsNote->title . ': ' . $detailsNote->description . ' - ' . Yii::$app->formatter->asDate($detailsNote->publish_at);
						}
					}
				}
				foreach ($issue->payCalculations as $payCalculation) {
					$payCalculationDetails = $payCalculation->getTypeName() . ': ';
					if ($payCalculation->getValueToPay() > 0) {
						$payCalculationDetails .= Yii::t('settlement', 'Value to pay')
							. ' : ' . Yii::$app->formatter->asCurrency($payCalculation->getValueToPay());
					} else {
						if ($payCalculation->hasProblemStatus()) {
							$payCalculationDetails .= $payCalculation->getProblemStatusName();
						} else {
							$payCalculationDetails .= Yii::t('settlement', 'Settled');
						}
					}
					$content[] = $payCalculationDetails;
				}
				if ($issue->agent) {
					$content[] = $issue->getAttributeLabel('agent') . ': ' . $issue->agent->getFullName();
				}
				//	Console::output(implode(PHP_EOL, $content));

				return Json::encode(['details' => implode(PHP_EOL, $content)]);
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
			foreach ($agents as $agent) {
				$agentQuery = clone $issueQuery;
				$agentQuery->agents([$agent]);
				$count = $creator->createLeads($agentQuery);
				Console::output('Agent: ' . $agent . ' Create leads ' . $count . ' from Issues: ' . $agentQuery->count(' * ', $creator->issueDb));
			}
		} else {
			$count = $creator->createLeads($issueQuery);
			Console::output('Create leads: ' . $count . ' from Issues: ' . $issueQuery->count(' * ', $creator->issueDb));
		}
	}

	protected function issuesDb(): Connection {
		return Yii::$app->db;
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
