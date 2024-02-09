<?php

namespace console\controllers;

use common\components\message\MessageTemplate;
use common\helpers\ArrayHelper;
use common\models\KeyStorageItem;
use common\modules\calendar\models\searches\LeadStatusDeadlineSearch;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;

class LeadMessageController extends Controller {

	public function actionTodayDeadline() {
		$model = new LeadStatusDeadlineSearch();
		$model->startAt = date('Y-m-d 00:00:00');
		$model->endAt = date('Y-m-d 23:59:59');
		$models = $model->getQuery()
			->joinWith('owner')
			->all();
		if (empty($models)) {
			Console::output('Not find models.');
			return;
		}
		$owners = [];
		foreach ($models as $model) {
			$ownerId = $model->owner->getID();
			if (!isset($owners[$ownerId])) {
				$owners[$ownerId] = [
					'email' => $model->owner->getEmail(),
					'models' => [],
				];
			}
			$owners[$ownerId]['models'][] = $model;
		}
	}

	public function actionTemplate(string $key, string $queryString, int $limit = 3, ?int $smsTagId = null) {
		$smsOwnerId = $this->getSmsOwnerId();
		$template = Yii::$app->messageTemplate->getTemplate($key);
		if ($template === null) {
			Console::output('Not find template for key: ' . $key);
			return;
		}
		Console::output('Find template: ' . $template->getSubject());
		$model = new LeadSearch();
		if ($smsTagId) {
			$model->excludedClosedQuestions = [$smsTagId];
		}
		parse_str($queryString, $params);
		$dataProvider = $model->search($params);
		$dataProvider->pagination = false;
		$ids = $dataProvider->getKeys();
		if (empty($ids)) {
			Console::output('Not find Models');
			return;
		}
		Console::output('Find Models: ' . count($ids));

		$users = LeadUser::find()
			->andWhere([LeadUser::tableName() . '.type' => LeadUser::TYPE_OWNER])
			->joinWith('user.userProfile')
			->andWhere(['lead_id' => $ids])
			->indexBy('lead_id')
			->all();

		$usersPhones = ArrayHelper::map($users, 'user_id', 'user.phone');
		$usersNames = ArrayHelper::map($users, 'user_id', 'user.userProfile.firstname');
		$usersEmails = ArrayHelper::map($users, 'user_id', 'user.email');
		$count = 0;
		$notQualify = [];
		foreach ($dataProvider->getModels() as $lead) {
			/** @var Lead $lead */
			$ownerId = $users[$lead->getId()]['user_id'] ?? null;
			$smsCount = 0;
			if ($ownerId) {
				$phone = $usersPhones[$ownerId] ?? null;
				$userName = $usersNames[$ownerId] ?? null;
				$userEmail = $usersEmails[$ownerId] ?? null;

				if ($phone && $userName && $userEmail) {
					$model = new LeadSmsForm($lead);
					$model->owner_id = $smsOwnerId;
					/** @var MessageTemplate $userTemplate */
					$userTemplate = clone $template;
					$userTemplate->parseBody([
						'userName' => $userName,
						'userEmail' => $userEmail,
						'userPhone' => Yii::$app->formatter->asTel($phone, [
							'asLink' => false,
						]),
					]);
					$model->message = $userTemplate->getSmsMessage();
					$lastSmsReportId = null;
					if ($model->validate()) {
						foreach ($lead->reports as $report) {
							if ($report->owner_id === $smsOwnerId && strpos($report->details, $model->message)) {
								$smsCount++;
								if ($lastSmsReportId === null) {
									$lastSmsReportId = $report->id;
								}
							}
						}
					}
					if ($smsCount < $limit) {
						if ($model->pushJob()) {
							$count++;
						}
					} else {
						Console::output('Not qualify to send');
						if ($smsTagId && $lastSmsReportId) {
							$notQualify[] = $lastSmsReportId;
						}
					}
				} else {
					Yii::warning('In Lead: ' . $lead->getId() . ' Owner without all required data.');
				}
			} else {
				Yii::warning('In Lead: ' . $lead->getId() . ' not found Owner.');
			}
		}
		Console::output('Push jobs: ' . $count);

		if (!empty($notQualify)) {
			$rows = [];
			foreach ($notQualify as $reportId) {
				$rows[] = [
					'report_id' => $reportId,
					'question_id' => $smsTagId,
				];
			}
			$notQualifyCount = LeadAnswer::getDb()->createCommand()
				->batchInsert(LeadAnswer::tableName(), ['report_id', 'question_id'], $rows)
				->execute();
			if ($notQualifyCount) {
				Console::output('Mark as not qualify reports: ' . $notQualifyCount);
			}
		}
	}

	private function getSmsOwnerId(): int {
		$owner = Yii::$app->keyStorage->get(KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID);
		if ($owner === null) {
			throw new InvalidConfigException('Not Set Robot SMS Owner. Key: (' . KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID . ').');
		}
		return $owner;
	}

}
