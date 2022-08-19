<?php

namespace console\controllers;

use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\KeyStorageItem;
use common\models\message\IssueSmsForm;
use common\models\message\IssueStageChangeMessagesForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class IssueMessageController extends Controller {

	public function actionIssueStageDelayedMessage(): void {
		Console::output('Push Issue Stage Delayed Messages: ');
		Console::output(VarDumper::dumpAsString(IssueStageChangeMessagesForm::pushDelayedMessages($this->getSmsOwnerId())));
	}

	public function actionAgentToCustomer(int $agentId, string $message = null, bool $withArchive = false): void {
		$models = IssueUser::find()
			->andWhere([IssueUser::tableName() . '.user_id' => $agentId])
			->withType(IssueUser::TYPE_AGENT)
			->joinWith([
				'issue' => function (IssueQuery $query) use ($withArchive) {
					if (!$withArchive) {
						$query->withoutArchives();
					}
				},
			])
			->joinWith('issue.customer.userProfile')
			->all();
		$push = 0;
		foreach ($models as $model) {
			$issue = $model->issue;
			$messageForm = new IssueSmsForm($issue, ['userTypes' => [IssueUser::TYPE_CUSTOMER]]);
			$messageForm->owner_id = $this->getSmsOwnerId();
			$messageForm->message = $message;
			$messageForm->setFirstAvailablePhone();
			if ($messageForm->pushJob()) {
				$push++;
			} else {
				Console::output(print_r($messageForm->getErrors()));
			}
		}
		Console::output('All Models: ' . count($models));
		Console::output('Push SMS: ' . $push);
	}

	private function getSmsOwnerId(): int {
		$owner = Yii::$app->keyStorage->get(KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID);
		if ($owner === null) {
			throw new InvalidConfigException('Not Set Robot SMS Owner. Key: (' . KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID . ').');
		}
		return $owner;
	}

}
