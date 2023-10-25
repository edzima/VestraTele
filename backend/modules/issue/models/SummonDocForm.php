<?php

namespace backend\modules\issue\models;

use common\models\issue\SummonDoc;
use common\models\issue\SummonType;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class SummonDocForm extends Model {

	public string $name = '';
	public $priority;

	public $summonTypesIds = [];

	private ?SummonDoc $model = null;

	public static function getPriorityNames(): array {
		return SummonDoc::getPriorityNames();
	}

	public static function getSummonTypesNames(): array {
		return SummonType::getNames();
	}

	public function rules(): array {
		return [
			['name', 'required'],
			['priority', 'integer'],
			[
				'name', 'unique', 'targetClass' => SummonDoc::class, 'filter' => function (QueryInterface $query) {
				if (!$this->getModel()->isNewRecord) {
					$query->andWhere(['not', ['id' => $this->getModel()->id]]);
				}
			},
			],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
			['summonTypesIds', 'in', 'range' => array_keys(static::getSummonTypesNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels() {
		return array_merge(
			SummonDoc::instance()->attributeLabels(), [
				'summonTypesIds' => Yii::t('issue', 'Summon Types'),
			]
		);
	}

	public function getModel(): SummonDoc {
		if ($this->model === null) {
			$this->model = new SummonDoc();
		}
		return $this->model;
	}

	public function setModel(SummonDoc $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->priority = $model->priority;
		$this->summonTypesIds = $model->getSummonTypesIds();
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->priority = $this->priority;
		$model->setSummonTypesIds((array) $this->summonTypesIds);
		return $model->save();
	}
}
