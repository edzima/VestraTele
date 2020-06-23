<?php

namespace common\widgets\address;

use common\models\address\Address;
use Yii;
use yii\widgets\DetailView;

/**
 * Class AddressDetailView
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class AddressDetailView extends DetailView {

	/** @var Address */
	public $model;

	public function init() {
		if (empty($this->attributes)) {
			$this->attributes = [
				'state',
				'province',
				'subProvince',
				[
					'attribute' => 'city',
					'label' => 'Miasto',
					'value' => Yii::$app->formatter->asCityCode($this->model->getCity(), $this->model->cityCode),
				],
				'street',
			];
		}
		parent::init();
	}

}
