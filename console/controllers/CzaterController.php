<?php

namespace console\controllers;

use common\modules\czater\entities\Call;
use common\modules\lead\models\forms\CzaterCallLeadForm;
use common\modules\lead\models\Lead;
use common\modules\lead\Module;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class CzaterController extends Controller {

	private array $hosts = [];

	public function actionImportAllCalls() {
		$offset = 0;

		$models = Yii::$app->czater->getCalls($offset);
		$count = 0;
		while (!empty($models)) {
			$offset += 100;
			foreach ($models as $model) {
				$count += $this->parseLead($model);
			}

			$models = Yii::$app->czater->getCalls($offset);
		}
		Console::output('Push New Leads: ' . $count);
		Console::output(print_r($this->hosts));
	}

	public function actionDeleteCalls(): void {
		Lead::deleteAll([
			'provider' => Lead::PROVIDER_CZATER_CALL,
		]);
	}

	public function actionImportCall(int $id): void {
		$model = Yii::$app->czater->getCall($id);
		if (!$model) {
			Console::output('Not Find Call for ID: ' . $id);
		}
		$this->parseLead($model);
	}

	public function actionImportCalls(int $offset): void {
		$models = Yii::$app->czater->getCalls($offset);
		$count = 0;
		foreach ($models as $model) {
			$count += $this->parseLead($model);
		}
		Console::output('Push New Leads: ' . $count);
		Console::output(print_r($this->hosts));
	}

	private function parseLead(Call $model): bool {
		$callLead = new CzaterCallLeadForm();
		$callLead->setCall($model);
		if ($callLead->validate()) {
			$lead = $callLead->findLead();
			if ($lead === null) {
				Console::output('Push new Lead');
				Module::manager()->pushLead($callLead);
				return true;
			}
			Console::output('Lead already Find');
			$callLead->updateLead($lead);
		}
		if ($callLead->hasErrors()) {
			if (!$callLead->hasErrors('data')) {
				Console::output(print_r($callLead->getErrors()));
			}
			if ($callLead->hasErrors('source_id')) {
				Console::output('Referer: ' . $callLead->getReferer());
				$this->addHosts($callLead->getReferer());
			}
			if ($callLead->hasErrors('phone')) {
				Console::output('Phone: ' . $callLead->getPhone());
			}
		}

		return false;
	}

	private function addHosts(string $url): void {
		$parse = parse_url($url);
		if (empty($parse) || !isset($parse['host'])) {
			Yii::warning('Try find by referer for invalid URL: ' . $url, __METHOD__);
			return;
		}
		$host = preg_replace('/^www\./i', '', $parse['host']);
		if (!in_array($host, $this->hosts, true)) {
			$this->hosts[] = $host;
		}
	}

}
