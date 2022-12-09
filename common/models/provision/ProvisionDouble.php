<?php

namespace common\models\provision;

use yii\db\ActiveQuery;

class ProvisionDouble extends Provision {

	public static function find(): ProvisionQuery {
		return parent::find()
			->groupBy('pay_id, to_user_id, from_user_id')
			->having('COUNT(*) > 1');
	}

	public function getDoubles(): ActiveQuery {
		return $this->hasMany(Provision::class, [
			'to_user_id' => 'to_user_id',
			'pay_id' => 'pay_id',
			'from_user_id' => 'from_user_id',
		]);
	}
}
