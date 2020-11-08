<?php

namespace common\widgets\address;

use common\models\Address;
use common\models\address\Address as LegacyAddress;
use Yii;
use yii\widgets\DetailView;

/**
 * Class AddressDetailView
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class AddressDetailView extends DetailView {

	/** @var Address | LegacyAddress */
	public $model;

	public function init(): void {
		if (empty($this->attributes)) {
			if ($this->model instanceof LegacyAddress) {
				$this->attributes = [
					'cityId',
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
			} else {
				$this->attributes = [
					[
						'attribute' => 'city.terc.region',
						'visible' => !empty($this->model->city),
					],
					[
						'attribute' => 'city.terc.district',
						'visible' => !empty($this->model->city),
					],
					[
						'attribute' => 'city.terc.commune',
						'visible' => !empty($this->model->city),
					],
					[
						'attribute' => 'city',
						'label' => 'Miasto',
						'value' => $this->model->city ? Yii::$app->formatter->asCityCode($this->model->city->name, $this->model->postal_code) : $this->model->postal_code,
						'visible' => !empty($this->model->postal_code) || !empty($this->model->city),
					],
					[
						'attribute' => 'info',
						'visible' => !empty($this->model->info),
					],
				];
			}
		}
		parent::init();
	}

}
