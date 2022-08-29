<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAddress;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadReport;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class LeadMarketForm extends Model {

	public $lead_id;
	public $creator_id;
	public $status;
	public string $details = '';
	public bool $withoutAddressFilter = true;

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
			['withoutAddressFilter', 'boolean'],
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
			['lead_id', 'uniqueWithSameContactValidator'],
			[
				['lead_id'], 'exist',
				'skipOnError' => true,
				'targetClass' => LeadAddress::class,
				'targetAttribute' => ['lead_id' => 'lead_id'],
				'when' => function (): bool {
					return $this->withoutAddressFilter && $this->getModel()->isNewRecord;
				},
				'message' => Yii::t('lead', 'Lead must have Address.'),
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'details' => Yii::t('lead', 'Details'),
			'withoutAddressFilter' => Yii::t('lead', 'Without Address Filter'),
		];
	}

	public function uniqueWithSameContactValidator(): void {
		$lead = Lead::findById($this->lead_id);
		if ($lead) {
			$sameContacts = $lead->getSameContacts(true);
			if ($this->leadsHasMarket($sameContacts)) {
				$this->addError('lead_id', 'Same Lead has already in Market.');
			}
		}
	}

	/**
	 * @param Lead[] $leads
	 * @return bool
	 */
	private function leadsHasMarket(array $leads): bool {
		foreach ($leads as $lead) {
			if ($lead->getMarket()->exists()) {
				return true;
			}
		}
		return false;
	}

	public function load($data, $formName = null): bool {
		return parent::load($data, $formName)
			&& $this->getOptions()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors);
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

	public function saveReport(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		if ($model->isNewRecord) {
			return false;
		}
		$report = new LeadReport();
		$report->owner_id = $this->creator_id;
		$report->lead_id = $this->lead_id;
		$report->old_status_id = $model->lead->getStatusId();
		$report->status_id = $model->lead->getStatusId();
		$report->details = Yii::t('lead', 'Move Lead to Market');
		return $report->save();
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
