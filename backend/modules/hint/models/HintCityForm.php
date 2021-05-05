<?php

namespace backend\modules\hint\models;

use common\models\hint\HintCity;
use common\models\user\User;
use edzima\teryt\models\Simc;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class HintCityForm extends Model {

	public $city_id;
	public $details;
	public string $status = HintCity::STATUS_NEW;
	public string $type = HintCity::TYPE_CARE_BENEFITS;
	public $user_id;

	private ?HintCity $model = null;

	public function rules(): array {
		return [
			[['status', 'type', 'city_id', 'user_id'], 'required'],
			[['city_id', 'user_id'], 'integer'],
			[['details', 'status', 'type'], 'string'],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['user_id', 'in', 'range' => array_keys(static::getUsersNames())],
			[
				['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Simc::class, 'targetAttribute' => ['city_id' => 'id'],
			],
			[
				['user_id', 'type', 'city_id'],
				'unique',
				'targetClass' => HintCity::class,
				'targetAttribute' => ['user_id' => 'user_id', 'type' => 'type', 'city_id' => 'city_id'],
				'filter' => function (QueryInterface $query): void {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
			[
				'details', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				return $this->status === HintCity::STATUS_ABANDONED;
			}, 'message' => Yii::t('hint', 'Details cannot be blank when status is abandoned.'),
			],
		];
	}

	public function attributeLabels(): array {
		return HintCity::instance()->attributeLabels();
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->status = $this->status;
		$model->type = $this->type;
		$model->city_id = $this->city_id;
		$model->user_id = $this->user_id;
		$model->details = $this->details;
		return $model->save();
	}

	public function getModel(): HintCity {
		if ($this->model === null) {
			$this->model = new HintCity();
		}
		return $this->model;
	}

	public function getCity(): ?Simc {
		return Simc::findOne($this->city_id);
	}

	public function setModel(HintCity $model): void {
		$this->model = $model;
		$this->status = $model->status;
		$this->type = $model->type;
		$this->details = $model->details;
		$this->city_id = $model->city_id;
		$this->user_id = $model->user_id;
	}

	public static function getStatusNames(): array {
		return HintCity::getStatusesNames();
	}

	public static function getTypesNames(): array {
		return HintCity::getTypesNames();
	}

	public static function getUsersNames(): array {
		return User::getSelectList(User::getAssignmentIds([User::PERMISSION_HINT]));
	}

}
