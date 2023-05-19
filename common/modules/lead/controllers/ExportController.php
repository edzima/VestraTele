<?php

namespace common\modules\lead\controllers;

use common\helpers\ArrayHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\helpers\FileHelper;
use yii2tech\csvgrid\CsvGrid;
use ZipArchive;

class ExportController extends BaseController {

	public function actionQueryGroupedByTypes(string $query) {
		$model = new LeadSearch();
		$model->joinAddress = false;
		parse_str($query, $dataQuery);
		$dataProvider = $model->search($dataQuery);

		/** @var LeadQuery $query */
		$query = $dataProvider->query;
		$ids = Yii::$app->request->post('selection');
		if (!empty($ids)) {
			$query->andWhere([Lead::tableName() . '.id' => $ids]);
		}
		$query->orderBy(['date_at' => SORT_DESC]);
		$columns = [
			'name',
			'phone',
			'email',
		];
		$typesQuery = clone $query;
		$typesNames = ArrayHelper::map(
			LeadType::find()->andWhere([
				'id' => $typesQuery
					->select('type_id')
					->distinct()
					->column(),
			])
				->asArray()->all(),
			'id',
			'name'
		);

		if ($model->addressSearch->isNotEmpty()) {
			$query->joinWith('addresses.address.city.terc');
			$columns = array_merge($columns, [
				[
					'attribute' => 'customerAddress.city.region.name',
					'label' => Yii::t('address', 'Region'),
				],
				[
					'attribute' => 'customerAddress.city.terc.district.name',
					'label' => Yii::t('address', 'District'),
				],
				[
					'attribute' => 'customerAddress.city.terc.commune.name',
					'label' => Yii::t('address', 'Commune'),
				],
				[
					'attribute' => 'customerAddress.postal_code',
					'label' => Yii::t('address', 'Code'),
				],
				[
					'attribute' => 'customerAddress.city.name',
					'label' => Yii::t('address', 'City'),
				],
				[
					'attribute' => 'customerAddress.info',
					'label' => Yii::t('address', 'Info'),
				],
			]);
		}

		if (!empty($typesNames)) {
			$zipFilename = 'leads-' . date(DATE_RFC3339) . '.zip';
			$zipPath = Yii::getAlias('@runtime/lead-csv') . DIRECTORY_SEPARATOR . $zipFilename;
			FileHelper::createDirectory(Yii::getAlias('@runtime/lead-csv'));
			$zip = new ZipArchive();
			$zip->open($zipPath, ZipArchive::CREATE);

			foreach ($typesNames as $id => $name) {
				$typeQuery = clone $query;
				$typeQuery->andWhere(['type_id' => $id]);
				$exporter = new CsvGrid([
					'query' => $typeQuery,
					'columns' => $columns,
				]);
				$result = $exporter->export();
				$csvFile = Yii::getAlias('@runtime/lead-csv') . DIRECTORY_SEPARATOR . $name . '.csv';
				$result->saveAs($csvFile);
				$zip->addFile($csvFile, $name);
			}
			$zip->close();
			Yii::$app->response->sendFile($zipPath);
		}
	}
}
