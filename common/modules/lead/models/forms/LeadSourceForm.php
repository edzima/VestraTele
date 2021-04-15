<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadType;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class LeadSourceForm extends Model {

	public string $name = '';
	public string $type_id = '';
	public string $url = '';
	public string $sort_index = '';
	public string $owner_id = '';

	private ?LeadSource $model = null;

	private static function getUsersNames(): array {
		return Module::userNames();
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public function rules(): array {
		return [
			[['name', 'type_id'], 'required'],
			[['type_id', 'owner_id'], 'integer'],
			[['name', 'url'], 'string', 'max' => 255],
			['url', 'url'],
			[
				'name', 'unique', 'targetClass' => LeadSource::class, 'filter' => function (QueryInterface $query) {
				if (!$this->getModel()->isNewRecord) {
					$query->andWhere(['not', ['id' => $this->getModel()->id]]);
				}
			},
			],
			['type_id', 'in', 'range' => array_keys(static::getTypesNames())],
			['owner_id', 'in', 'range' => array_keys(static::getUsersNames())],

		];
	}

	public function attributeLabels(): array {
		return [
			'name' => Yii::t('lead', 'Name'),
			'type_id' => Yii::t('lead', 'Type'),
		];
	}

	public function setModel(LeadSource $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->type_id = $model->type_id;
		$this->sort_index = $model->sort_index;
		$this->url = $model->url;
	}

	public function getModel(): LeadSource {
		if ($this->model === null) {
			$this->model = new LeadSource();
		}
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->type_id = $this->type_id;
		$model->url = $this->url;
		$model->sort_index = $this->sort_index;

		return $model->save(false);
	}
}
