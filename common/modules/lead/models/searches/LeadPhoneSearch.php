<?php

namespace common\modules\lead\models\searches;

use common\models\SearchModel;
use common\modules\lead\models\Lead;
use common\validators\PhoneValidator;
use yii\data\ActiveDataProvider;

class LeadPhoneSearch extends Lead implements SearchModel {

	public function rules(): array {
		return [
			['phone', 'required'],
			['phone', PhoneValidator::class],
			['phone', 'string', 'min' => 9],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$query = Lead::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
			return $dataProvider;
		}

		$query->withPhoneNumber($this->phone);
		return $dataProvider;
	}
}
