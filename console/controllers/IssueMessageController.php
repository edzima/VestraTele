<?php

namespace console\controllers;

use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\issue\Summon;
use common\models\KeyStorageItem;
use common\models\message\IssueSmsForm;
use common\models\message\IssueStageChangeMessagesForm;
use common\models\user\User;
use common\models\user\Worker;
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

	public function actionImminentSummonDeadline(string $range = '+2 days'): void {
		$users = User::getAssignmentIds([Worker::PERMISSION_MESSAGE_EMAIL_SUMMON_IMMINENT_DEADLINE], false);
		$count = 0;
		if (!empty($users)) {
			Console::output('Find Users with Permission: ' . count($users));
			$models = Summon::find()
				->active()
				->users($users)
				->orderBy(['deadline_at' => SORT_ASC])
				->imminentDeadline($range)
				->all();
			$usersSummons = [];
			Console::output('Find Summons with imminent deadline: ' . count($models));

			foreach ($users as $userId) {
				$userSummons = array_filter($models, function (Summon $summon) use ($userId): bool {
					return $summon->isForUser($userId);
				});
				if (!empty($userSummons)) {
					$usersSummons[$userId] = $userSummons;
				}
			}
			if (!empty($usersSummons)) {
				$usersEmails = User::find()
					->select('email')
					->andWhere(['id' => array_keys($usersSummons)])
					->indexBy('id')
					->column();
				foreach ($usersSummons as $userId => $summons) {
					$email = $usersEmails[$userId] ?: null;
					if ($email) {
						Yii::$app->mailer->compose([
							'html' => 'summonsImminentDeadline-html',
							'text' => 'summonsImminentDeadline-html',
						], [
							'models' => $summons,
						])
							->setTo($email)
							->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
							->setSubject(Yii::t('issue', 'Summons with immiment deadline.', [
								'count' => count($summons),
							]))
							->send();
						$count++;
					} else {
						Yii::warning('User: ' . $userId . ' without Email.', __METHOD__);
					}
				}
			}
		}
		Console::output('Send Emails to Users: ' . $count);
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
