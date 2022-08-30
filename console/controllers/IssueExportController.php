<?php

namespace console\controllers;

use common\models\issue\Issue;
use common\models\issue\IssueType;
use edzima\teryt\models\District;
use edzima\teryt\models\Region;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

class IssueExportController extends Controller {

	public $dataPath = '@runtime' . DIRECTORY_SEPARATOR . 'issues-export';

	public function actionTypesByDistricts() {


		$districts = [];
		$withoutCities = [];

		foreach (Issue::find()
			->joinWith('customer.addresses.address.city')
			->groupBy(Issue::tableName() . '.id')
			->batch() as $rows) {
			foreach ($rows as $row) {
				/** @var Issue $row */
				$city = $row->customer->homeAddress->city ?? null;
				if ($city) {
					$districts[$city->region_id][$city->district_id][$row->type_id][$row->id] = $row->id;
				} else {
					$withoutCities[$row->id] = $row;
				}
			}
		}

		$data = [];
		$dataPath = Yii::getAlias($this->dataPath);

		$typesNames = IssueType::getTypesNames();
		$header = $typesNames;
		array_unshift($header, Yii::t('address', 'District'));
		$header[] = Yii::t('common', 'Sum');

		foreach ($districts as $regionId => $issues) {
			$districtsNames = District::find()
				->select('name')
				->onlyDistricts($regionId)
				->indexBy('district_id')
				->orderBy('name')
				->column();

			$regionName = Region::getNames()[$regionId];
			$rows = [];
			$rows[] = $header;

			foreach ($districtsNames as $districtId => $name) {
				$row = [];
				$row[] = $name;
				$districtIssues = $issues[$districtId] ?? [];
				$sum = 0;
				foreach ($typesNames as $typeId => $typeName) {
					$typeCount = isset($districtIssues[$typeId]) ? count($districtIssues[$typeId]) : 0;
					$row[] = $typeCount;
					$sum += $typeCount;
				}
				$row[] = $sum;
				$rows[] = $row;
			}

			FileHelper::createDirectory($dataPath);
			$fileName = $dataPath . DIRECTORY_SEPARATOR . $regionName . '.csv';
			$fp = fopen($fileName, 'wb');
			foreach ($rows as $fields) {
				fputcsv($fp, $fields);
			}
			fclose($fp);
		}

		$fileName = $dataPath . DIRECTORY_SEPARATOR . 'without-city' . '.csv';
		$fp = fopen($fileName, 'wb');
		fputcsv($fp, [
			Yii::t('issue', 'Type'),
			Yii::t('address', 'Postal Code'),
			Yii::t('address', 'Info'),
		]);

		foreach ($withoutCities as $issue) {
			/** @var Issue $issue */
			$address = $issue->customer->homeAddress;
			fputcsv($fp, [
					$typesNames[$issue->type_id],
					$address->postal_code,
					$address->info,
				]
			);
		}

		fclose($fp);
	}

}
