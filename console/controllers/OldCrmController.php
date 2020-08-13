<?php

namespace console\controllers;

use common\models\issue\Issue;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\IssueNote;
use common\models\User;
use console\components\oldCrmData\IssueNoteDataTransfer;
use yii\console\Controller;
use console\components\oldCrmData\FixtureMigration;
use console\components\oldCrmData\IssueDataTransfer;
use yii\db\Query;
use yii\helpers\Console;

/**
 * Class OldCrmController
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class OldCrmController extends Controller {

	public function actionUser(): void {
		if ($this->confirm('Remove users?')) {
			$id = Console::input('> id for remove?');
			User::deleteAll('id > :id', [':id' => $id]);
		}

		$user = new UserDataTransfer();
		$user->transfer();
	}

	public function actionIssue(): void {
		$issue = new IssueDataTransfer();
		if (!$issue->isAddedOldIdColumn()) {
			$issue->addOldIdColumn();
		}
		$issue->transfer();
		Console::output('Not transfered: ' . count($issue->getNotTransferedIds()));
	}

	public function actionNote(int $id): void {
		IssueNote::deleteAll(['issue_id' => $id]);
		$notes = new IssueNoteDataTransfer(['issueId' => $id]);
		$notes->transfer();
	}

	public function actionFixEntity(): void {
		EntityResponsible::deleteAll();
		$migration = new FixtureMigration();
		$migration->fixEntity();
		$issue = new IssueDataTransfer();
		if (!$issue->isAddedOldIdColumn()) {
			$issue->addOldIdColumn();
		}
		$issue->queryCondition = function (Query $query) {
			$query->groupBy(FixtureMigration::COLUMN_ENTITY);
		};
		$issue->transfer();
	}

	public function actionFixType(): void {
		(new FixtureMigration())->fixType();
	}

	public function actionFixRegion(): void {
		(new FixtureMigration())->fixRegion();
	}

	public function actionMergeUser(int $oldUser, int $newUser): void {
		Issue::updateAll(['tele_id' => $newUser], ['tele_id' => $oldUser]);
		Issue::updateAll(['agent_id' => $newUser], ['agent_id' => $oldUser]);
		IssueNote::updateAll(['user_id' => $newUser], ['user_id' => $oldUser]);
		User::deleteAll(['id' => $oldUser]);
	}

}
