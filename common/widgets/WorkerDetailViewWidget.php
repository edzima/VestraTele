<?php

namespace common\widgets;

use common\models\user\Worker;
use yii\helpers\Html;
use yii\widgets\DetailView;

class WorkerDetailViewWidget extends DetailView {

	public ?string $viewBaseUrl = '/user/worker/view';

	public $attributes = [
		'fullName' => [
			'attribute' => 'fullName',
		],
		'email:email',
		'profile.phone',
	];

	public function init() {
		if (!empty($this->viewBaseUrl)) {
			$this->attributes['fullName'] = [
				'attribute' => 'fullName',
				'format' => 'raw',
				'value' => function (Worker $model): string {
					return Html::a(Html::encode($model->getFullName()), [$this->viewBaseUrl, 'id' => $model->id], [
						'target' => '_blank',
					]);
				},
			];
		}
		parent::init();
	}

}
