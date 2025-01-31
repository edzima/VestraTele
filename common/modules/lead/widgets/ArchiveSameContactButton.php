<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use Yii;
use yii\base\Widget;

class ArchiveSameContactButton extends Widget {

	public ActiveLead $model;

	public function run() {
		if (!$this->shouldRender()) {
			return '';
		}
		return Html::a(
			Yii::t('lead', 'Move {type} to Archive', [
				'type' => $this->model->getTypeName(),
			]),
			[
				'/lead/archive/same-contact',
				'id' => $this->model->getId(),
				'onlySameType' => true,
			],
			[
				'class' => 'btn btn-danger',
				'data' => [
					'method' => 'POST',
					'confirm' => Yii::t('lead',
						'Move {type} Same Contact to Archive?', [
							'type' => $this->model->getTypeName(),
						]),
				],
			]
		);
	}

	public function shouldRender(): bool {
		return !empty($this->model->getSameContacts(true));
	}
}
