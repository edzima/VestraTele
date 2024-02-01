<?php

namespace console\controllers;

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class LeadMessageController extends Controller {

	public function actionTemplate(string $key, string $queryString) {
		$template = Yii::$app->messageTemplate->getTemplate($key);
		if ($template === null) {
			Console::output('Not find template for key: ' . $key);
			return;
		}
		Console::output('Find template: ' . $template->getSubject());
		$model = new LeadSearch();
		parse_str($queryString, $params);
		$dataProvider = $model->search($params);
		$dataProvider->pagination = false;
		$ids = $dataProvider->getKeys();
		if (empty($ids)) {
			Console::output('Not find Models');
			return;
		}
		Console::output('Find Leads: ' . count($ids));

		$users = LeadUser::find()
			->andWhere([LeadUser::tableName() . '.type' => LeadUser::TYPE_OWNER])
			->joinWith('user.userProfile')
			->groupBy('user_id')
			->andWhere(['lead_id' => $ids])
			->indexBy('lead_id')
			->all();

		$usersPhones = ArrayHelper::map($users, 'user_id', 'user.phone');
		$usersNames = ArrayHelper::map($users, 'user_id', 'user.userProfile.firstname');
		$usersEmails = ArrayHelper::map($users, 'user_id', 'user.email');
		$count = 0;
		foreach ($dataProvider->getModels() as $lead) {
			$ownerId = $users[$lead->getId()]['user_id'] ?? null;
			if ($ownerId) {
				$phone = $usersPhones[$ownerId] ?? null;
				$userName = $usersNames[$ownerId] ?? null;
				$userEmail = $usersEmails[$ownerId] ?? null;
				if ($phone && $userName && $userEmail) {
					$model = new LeadSmsForm($lead);
					$model->owner_id = $ownerId;
					$userTemplate = clone $template;
					$userTemplate->parseBody([
						'userName' => $userName,
						'userEmail' => $userEmail,
						'userPhone' => Yii::$app->formatter->asTel($phone, [
							'asLink' => false,
						]),
					]);
					$model->message = $userTemplate->getSmsMessage();
					if ($model->pushJob()) {
						$count++;
					}
				} else {
					Yii::warning('In Lead: ' . $lead->getId() . ' Owner without all required data.');
				}
			} else {
				Yii::warning('In Lead: ' . $lead->getId() . ' not found Owner.');
			}
		}
		Console::output('Push jobs: ' . $count);
	}
}
