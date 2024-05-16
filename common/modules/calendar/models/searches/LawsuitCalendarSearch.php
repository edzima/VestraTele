<?php

namespace common\modules\calendar\models\searches;

use common\modules\calendar\models\LawsuitEvent;
use common\modules\court\models\Court;
use common\modules\court\models\Lawsuit;
use Yii;
use yii\base\Model;

class LawsuitCalendarSearch extends Model {

	public $startAt;
	public $endAt;

	public static function getCourtFilters(): array {
		$data = [];
		$data[] = [
			'value' => Court::TYPE_APPEAL,
			'isActive' => true,
			'label' => Yii::t('court', 'Appeal'),
			'color' => '#F69368',
			'badge' => [
				'text' => Court::TYPE_APPEAL,
				'background' => '#F69368',
			],
		];

		$data[] = [
			'value' => Court::TYPE_REGIONAL,
			'isActive' => true,
			'label' => Yii::t('court', 'Regional'),
			'color' => '#E6DC92',
			'badge' => [
				'text' => Court::TYPE_REGIONAL,
				'background' => '#E6DC92',
			],
		];

		$data[] = [
			'value' => Court::TYPE_DISTRICT,
			'isActive' => true,
			'label' => Yii::t('court', 'District'),
			'color' => '#359CD7',
			'badge' => [
				'text' => Court::TYPE_DISTRICT,
				'background' => '#359CD7',
			],
		];

		return $data;
	}

	public function getEventsData(): array {
		$query = Lawsuit::find()
			->andWhere([
				'>=', 'due_at', $this->startAt,
			])
			->andWhere([
				'<=', 'due_at', $this->endAt,
			]);
		$data = [];
		foreach ($query->all() as $model) {
			$event = new LawsuitEvent();
			$event->setModel($model);
			$data[] = $event->toArray();
		}
		return $data;
	}

}
