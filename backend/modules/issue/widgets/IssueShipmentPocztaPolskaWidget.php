<?php

namespace backend\modules\issue\widgets;

use backend\helpers\Html;
use backend\widgets\GridView;
use common\models\issue\IssueShipmentPocztaPolska;
use Yii;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\widgets\DetailView;

class IssueShipmentPocztaPolskaWidget extends Widget {

	public IssueShipmentPocztaPolska $model;

	public ?string $emptyShipmentText = null;

	public string $headerTag = 'h3';

	public function init() {
		parent::init();
		if ($this->emptyShipmentText === null) {
			$this->emptyShipmentText = Yii::t('backend', 'Have not loaded Data from API. Click refresh.');
		}
	}

	public function run() {
		$shipment = $this->model->getShipment();
		if ($shipment === null) {
			return $this->emptyShipmentText;
		}
		if ($shipment->danePrzesylki === null) {
			return $shipment->getStatusName();
		}
		$content = Html::tag($this->headerTag, Html::encode($shipment->danePrzesylki->rodzPrzes));
		$content .= DetailView::widget([
			'model' => $shipment->danePrzesylki,
			'attributes' => [
				'numer',
				[
					'attribute' => 'masa',
					'visible' => !empty($shipment->danePrzesylki->masa),
				],
				[
					'attribute' => 'format',
					'visible' => !empty($shipment->danePrzesylki->format),
				],

			],
		]);
		$events = $shipment->danePrzesylki->zdarzenia->zdarzenie;
		if (!empty($events)) {
			if (!is_array($events)) {
				$events = [$events];
			}
			$content .= GridView::widget([
				'caption' => 'Zdarzenia',
				'dataProvider' => new ArrayDataProvider(['allModels' => $events]),
				'columns' => [
					'nazwa',
					'czas:datetime',
					[
						'attribute' => 'przyczyna.nazwa',
						'label' => Yii::t('poczta-polska', 'Cause'),
					],
					[
						'attribute' => 'jednostka.nazwa',
						'label' => Yii::t('poczta-polska', 'Entity'),
					],
				],
			]);
		}
		return $content;
	}
}
