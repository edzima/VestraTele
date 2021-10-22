<?php

namespace console\controllers;

use common\models\message\IssueSmsForm;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;

class IssueController extends Controller {

	public function actionAbsenceAgentSms(int $agent_id): void {
		Console::output('File transport: ' . Yii::$app->sms->useFileTransport);
//		Yii::$app->sms->useFileTransport = false;
		$user = User::findOne($agent_id);
		if (!$user) {
			Console::output('Not found User.');
			return;
		}
		$username = $user->getFullName();

		if (Console::confirm($username)) {
			$issueIds = IssueUser::find()
				->select('issue_id')
				->withType(IssueUser::TYPE_AGENT)
				->andWhere(['user_id' => $agent_id])
				->joinWith([
					'issue' => function (IssueQuery $query) {
						$query->withoutArchives();
					},
				])
				->column();

			if (empty($issueIds)) {
				Console::output('Not Found Active Issues.');
				return;
			}
			$message = "Szanowni Panstwo\nInformuje, iz w dniach 03.10-10.10.2021r. przebywam na urlopie i kontakt ze mna bedzie uniemozliwiony."
				. "\nW przypadku koniecznosci skonsultowania sie w tym okresie w sprawie Panstwa postepowania, bardzo prosze o kontakt z infolinia Kancelarii: 59 307 07 01."
				. "\nPozdrawiam, $username.";
			$message = $this->replace($message);

			if (Console::confirm('Find issue: ' . count($issueIds) . "\n" . 'Length message: ' . strlen($message) . "\n" . $message)) {
				foreach ($issueIds as $issueId) {
					try {
						$model = new IssueSmsForm($issueId);
						$model->owner_id = $agent_id;
						$model->message = $message;
						$model->note_title = Yii::t('issue', 'Send SMS for Agent absence.');
						if (empty($model->pushJob())) {
							Console::output(print_r($model->getErrors()));
						}
					} catch (InvalidConfigException $exception) {
						Console::output($exception->getMessage());
					}
				}
			}
		}
	}

}
