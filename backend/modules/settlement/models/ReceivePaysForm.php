<?php

namespace backend\modules\settlement\models;

use common\models\settlement\PayReceived;
use common\models\user\User;
use Yii;
use yii\base\Model;

class ReceivePaysForm extends Model {

	public $user_id;
	public array $pays_ids = [];
	public $date;

	public function rules(): array {
		return [
			[['user_id', 'pays_ids', 'date'], 'required'],
			['date', 'date', 'format' => 'Y-m-d'],
			['user_id', 'in', 'range' => array_keys(static::getUsersNames())],
			[
				'pays_ids', 'in', 'range' => function () {
				return array_keys($this->getNotTransferPays());
			}, 'allowArray' => true, 'enableClientValidation' => false,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('settlement', 'Receiver'),
			'pays_ids' => Yii::t('settlement', 'Pays'),
		];
	}

	/**
	 * @return PayReceived[]
	 */
	public function getNotTransferPays(): array {
		if ($this->hasErrors('user_id')) {
			return [];
		}
		return PayReceived::find()
			->andWhere(['transfer_at' => null])
			->andWhere(['user_id' => $this->user_id])
			->indexBy('pay_id')
			->all();
	}

	public function getPaysData(): array {
		$data = [];
		$pays = $this->getNotTransferPays();
		foreach ($this->pays_ids as $id) {
			if (isset($pays[$id])) {
				$pay = $pays[$id];
				$data[$pay->pay_id] = static::getName($pay);
			}
		}

		return $data;
	}

	public static function getName(PayReceived $payReceived): string {
		return Yii::$app->formatter->asCurrency($payReceived->pay->value)
			. ' - ' . $payReceived->pay->calculation->issue->customer->getFullName();
	}

	public static function getUsersNames(): array {
		return User::getSelectList(PayReceived::find()
			->select('user_id')
			->distinct()
			->andWhere(['transfer_at' => null])
			->column()
		);
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		PayReceived::updateAll(['transfer_at' => $this->date], ['pay_id' => $this->pays_ids, 'user_id' => $this->user_id]);

		return true;
	}
}
