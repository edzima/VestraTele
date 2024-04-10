<?php

namespace console\controllers;

use common\modules\court\components\CourtApi;
use common\modules\court\components\CourtImporter;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class CourtController extends Controller {

	public function actionImport(string $fileName = null, bool $withAddress = true) {
		$importer = new CourtImporter();
		if ($fileName === null) {
			$api = new CourtApi();
			if ($api->getLastUpdateDate() > $importer->getLastUpdateAt()) {
				$tempFile = Yii::getAlias('@runtime/court.xlsx');
				file_put_contents($tempFile, fopen($api->getFileUrl(), 'r'));
				$importer->fileName = $tempFile;
				Console::output('Insert/Update data for Court: ' . $importer->import());
				FileHelper::unlink($tempFile);
			}
		} else {
			$importer->withAddress = $withAddress;
			$importer->fileName = $fileName;
			Console::output('Insert/Update data for Court: ' . $importer->import());
		}
	}
}
