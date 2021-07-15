<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadSourceInterface;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\LeadTypeInterface;
use common\modules\lead\Module;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class LeadSourceForm extends Model implements LeadSourceInterface {

	public const SCENARIO_OWNER = 'owner';

	public string $name = '';
	public string $type_id = '';
	public ?string $url = null;
	public ?string $phone = null;
	public ?string $sort_index = null;
	public ?string $owner_id = null;

	private ?LeadSource $model = null;

	public static function getUsersNames(): array {
		return Module::userNames();
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public function rules(): array {
		return [
			[['name', 'type_id'], 'required'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['type_id', 'owner_id'], 'integer'],
			[['name', 'url'], 'string', 'max' => 255],
			[['phone'], 'string', 'max' => 30],
			//@todo attribute for country from Module or model?
			['phone', PhoneValidator::class, 'country' => 'PL'],
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
			'phone' => Yii::t('lead', 'Phone'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
		];
	}

	public function setModel(LeadSource $model): void {
		$this->model = $model;
		$this->setSource($model);
	}

	public function setSource(LeadSourceInterface $source): void {
		$this->name = $source->name;
		$this->type_id = $source->type_id;
		$this->sort_index = $source->sort_index;
		$this->phone = $source->phone;
		$this->url = $source->url;
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
		$model->owner_id = $this->owner_id;
		$model->sort_index = $this->sort_index;
		$model->phone = $this->phone;
		return $model->save(false);
	}

	public function getID(): string {
		return $this->getModel()->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getType(): LeadTypeInterface {
		return LeadType::getModels()[$this->type_id];
	}

	public function getURL(): ?string {
		return $this->url;
	}

	public function getOwnerId(): ?int {
		return $this->owner_id;
	}

	public function getPhone(): ?string {
		return $this->phone;
	}
}
