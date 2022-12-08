<?php

namespace common\widgets\address;

use common\models\Address;
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

	public function init(): void {
		if (empty($this->attributes)) {
			$this->attributes = $this->defaultAttributes();
		}
		parent::init();
	}

	public function defaultAttributes(): array {
		return [
			[
				'attribute' => 'city.terc.region',
				'label' => Yii::t('address', 'Region'),
				'visible' => !empty($this->model->city),
			],
			[
				'attribute' => 'city.terc.district',
				'label' => Yii::t('address', 'District'),
				'visible' => !empty($this->model->city),
			],
			[
				'attribute' => 'city.terc.commune',
				'label' => Yii::t('address', 'Commune'),
				'visible' => !empty($this->model->city),
			],
			[
				'attribute' => 'city',
				'label' => Yii::t('address', 'City'),
				'value' => Yii::$app->formatter->asCityCode(
					$this->model->city
						? $this->model->city->name
						: null,
					$this->model->postal_code),
				'visible' => !empty($this->model->postal_code) || !empty($this->model->city),
				'format' => 'html',
			],
			[
				'attribute' => 'info',
				'label' => Yii::t('address', 'Info'),
				'visible' => !empty($this->model->info),
			],
		];
	}

}
