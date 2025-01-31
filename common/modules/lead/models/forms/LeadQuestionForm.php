<?php

namespace common\modules\lead\models\forms;

use common\helpers\Html;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use Yii;
use yii\base\Model;
use yii\web\JsExpression;

class LeadQuestionForm extends Model {

	public string $type = LeadQuestion::TYPE_TEXT;
	public string $name = '';
	public ?string $placeholder = null;

	public ?bool $is_active = true;
	public ?bool $show_in_grid = null;
	public ?bool $is_required = null;

	public ?string $order = null;
	public $type_id;
	public $status_id;

	public $values;

	private ?LeadQuestion $model = null;

	public function rules(): array {
		return [
			[['name', 'type'], 'required'],
			[['order'], 'integer'],
			[['show_in_grid', 'is_required', 'is_active'], 'boolean'],
			[['name', 'placeholder'], 'string'],
			[['placeholder', 'order'], 'default', 'value' => null],
			['type_id', 'in', 'range' => array_keys(static::getLeadTypesNames())],
			['status_id', 'in', 'range' => array_keys(static::getLeadStatusNames())],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[
				'values', 'required',
				'when' => function (): bool {
					return $this->type === LeadQuestion::TYPE_RADIO_GROUP;
				},
				'whenClient' => new JsExpression("function (attribute, value) {
       				return $('" . Html::getInputId($this, 'type') . "').val() === '" . LeadQuestion::TYPE_RADIO_GROUP . "';
    			 }"),
			],
		];
	}

	public function attributeLabels(): array {
		return array_merge(
			LeadQuestion::instance()->attributeLabels(), [
			'values' => Yii::t('lead', 'Values'),
		]);
	}

	public function setModel(LeadQuestion $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->placeholder = $model->placeholder;
		$this->show_in_grid = $model->show_in_grid;
		$this->is_required = $model->is_required;
		$this->is_active = $model->is_active;
		$this->type_id = $model->type_id;
		$this->type = $model->type;
		$this->status_id = $model->status_id;
		$this->order = $model->order;
		if ($this->type === LeadQuestion::TYPE_RADIO_GROUP) {
			$this->values = $model->getRadioValues();
		}
	}

	public function getModel(): LeadQuestion {
		if ($this->model === null) {
			$this->model = new LeadQuestion();
		}
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->is_required = $this->is_required;
		$model->is_active = $this->is_active;
		$model->type = $this->type;
		$model->placeholder = $this->placeholder;
		if ($model->isRadioGroup()) {
			$model->setRadioValues((array) $this->values);
		}
		$model->show_in_grid = $this->show_in_grid;
		$model->type_id = $this->type_id;
		$model->status_id = $this->status_id;
		$model->order = $this->order;
		return $model->save();
	}

	public static function getLeadStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getLeadTypesNames(): array {
		return LeadType::getNames();
	}

	public static function getTypesNames(): array {
		return LeadQuestion::getTypesNames();
	}

}
