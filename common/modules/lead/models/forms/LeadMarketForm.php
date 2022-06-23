<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\Module;
use yii\base\Model;
use yii\db\QueryInterface;

class LeadMarketForm extends Model {

	public $lead_id;
	public $creator_id;
	public $status;
	public string $details = '';

	private ?LeadMarket $model = null;
	private ?LeadMarketOptions $options = null;

	public static function getStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	public function rules(): array {
		return [
			[['!lead_id', 'status', '!creator_id'], 'required'],
			[['lead_id', 'status', 'creator_id'], 'integer'],
			['details', 'string'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['creator_id' => 'id']],
			[
				'lead_id', 'unique',
				'targetClass' => LeadMarket::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],

		];
	}

	public function load($data, $formName = null): bool {
		return parent::load($data, $formName)
			&& $this->getOptions()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors);
	}

	public function afterValidate() {
		parent::afterValidate();
	}

	public function getModel(): LeadMarket {
		if ($this->model === null) {
			$this->model = new LeadMarket();
		}
		return $this->model;
	}

	public function setModel(LeadMarket $model): void {
		$this->model = $model;
		$this->lead_id = $model->lead_id;
		$this->status = $model->status;
		$this->details = $model->details;
		$this->creator_id = $model->creator_id;
		$this->setOptions($model->getMarketOptions());
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->creator_id = $this->creator_id;
		$model->lead_id = $this->lead_id;
		$model->status = $this->status;
		$model->options = $this->getOptions()->toJson();
		$model->details = $this->details;
		return $model->save();
	}

	public function getOptions(): LeadMarketOptions {
		if ($this->options === null) {
			$this->options = $this->getModel()->getMarketOptions();
		}
		return $this->options;
	}

	public function setOptions(LeadMarketOptions $options): void {
		$this->options = $options;
	}

}
