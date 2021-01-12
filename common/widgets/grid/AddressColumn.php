<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\Address;
use Yii;
use yii\base\Model;

class AddressColumn extends DataColumn {

	public $attribute = 'address';
	public $format = 'html';
	public $noWrap = true;

	public $template = '{postal_code} {city}<br>{region}';

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Address');
		}
		if (empty($this->value)) {
			$this->value = function (Model $model): ?string {
				$address = $model->{$this->attribute};
				if ($address instanceof Address) {
					return $this->renderValue($address);
				}
				return null;
			};
		}
		parent::init();
	}

	public function renderValue(Address $address): string {
		if (!$address->city) {
			return Html::encode($address->postal_code);
		}
		$names = [];
		$names['{postal_code}'] = Html::encode($address->postal_code);
		$names['{city}'] = Html::tag('strong', Html::encode($address->city->name));
		$names['{region}'] = Html::encode(ucwords($address->city->region->name));
		return strtr($this->template, $names);
	}
}
