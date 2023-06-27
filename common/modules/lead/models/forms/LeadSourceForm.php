<?php

namespace common\modules\lead\models\forms;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadSourceInterface;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\LeadTypeInterface;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class LeadSourceForm extends Model implements LeadSourceInterface {

	public const SCENARIO_OWNER = 'owner';

	public string $name = '';
	public string $type_id = '';
	public ?string $url = null;
	public ?string $phone = null;
	public ?string $dialer_phone = null;
	public ?string $sort_index = null;
	public ?string $owner_id = null;
	public ?string $sms_push_template = null;
	public bool $is_active = true;

	public ?string $call_page_widget_id = null;

	private ?LeadSource $model = null;

	public static function getUsersNames(): array {
		return Module::userNames();
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public function rules(): array {
		return [
			[['name', 'type_id', 'is_active'], 'required'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['is_active'], 'boolean'],
			[['type_id', 'owner_id', 'call_page_widget_id'], 'integer'],
			[['name', 'url'], 'string', 'max' => 255],
			[['phone', 'dialer_phone'], 'string', 'max' => 30],
			[['owner_id', 'call_page_widget_id', 'dialer_phone'], 'default', 'value' => null],
			[['sms_push_template'], 'string'],
			['phone', PhoneInputValidator::class],
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
			'dialer_phone' => Yii::t('lead', 'Dialer Phone'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
			'is_active' => Yii::t('lead', 'Is Active'),
			'sms_push_template' => Yii::t('lead', 'SMS Push Template'),
			'call_page_widget_id' => Yii::t('lead', 'CallPage Widget ID'),
		];
	}

	public function setModel(LeadSource $model): void {
		$this->model = $model;
		$this->setSource($model);
	}

	public function setSource(LeadSourceInterface $source): void {
		$this->name = $source->getName();
		$this->type_id = $source->getType()->getID();
		$this->sort_index = $source->sort_index;
		$this->owner_id = $source->getOwnerId();
		$this->phone = $source->getPhone();
		$this->dialer_phone = $source->getDialerPhone();
		$this->url = $source->getURL();
		$this->is_active = $source->getIsActive();
		$this->sms_push_template = $source->getSmsPushTemplate();
		$this->call_page_widget_id = $source->getCallPageWidgetId();
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
		$model->dialer_phone = $this->dialer_phone;
		$model->is_active = $this->is_active;
		$model->sms_push_template = $this->sms_push_template;
		$model->call_page_widget_id = $this->call_page_widget_id;
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

	public function getDialerPhone(): ?string {
		return $this->dialer_phone;
	}

	public function getSmsPushTemplate(): ?string {
		return $this->sms_push_template;
	}

	public function getIsActive(): bool {
		return $this->is_active;
	}

	public function getCallPageWidgetId(): ?int {
		return $this->call_page_widget_id;
	}
}
