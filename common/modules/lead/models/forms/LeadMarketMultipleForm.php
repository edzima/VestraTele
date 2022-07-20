<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAddress;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\searches\DuplicateLeadSearch;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

class LeadMarketMultipleForm extends Model {

	public array $leadsIds = [];
	public $creator_id;
	public $status;
	public string $details = '';

	private int $withoutAddressCount = 0;

	private ?LeadMarketOptions $options = null;

	public static function getStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	public function rules(): array {
		return [
			[['leadsIds', 'status', '!creator_id'], 'required'],
			['creator_id', 'integer'],
			['leadsIds', 'each', 'rule' => ['integer']],
			['details', 'string'],
			[['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['creator_id' => 'id']],
			[['leadsIds'], 'exist', 'allowArray' => true, 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => 'id'],
			['leadsIds', 'alreadyExistFilter'],
			['leadsIds', 'alreadyExistSameContactsFilter'],
			['leadsIds', 'withoutAddressFilter'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	public function attributeLabels() {
		return [
			'details' => Yii::t('lead', 'Details'),
		];
	}

	public function alreadyExistFilter(): void {
		if (!$this->hasErrors('leadsIds')) {
			$ids = LeadMarket::find()
				->select('lead_id')
				->andWhere(['lead_id' => $this->leadsIds])
				->distinct()
				->column();
			if (!empty($ids)) {
				$this->leadsIds = array_diff($this->leadsIds, $ids);
			}
		}
	}

	public function alreadyExistSameContactsFilter(): void {
		if (!$this->hasErrors('leadsIds')) {
			$duplicate = new DuplicateLeadSearch();
			$duplicate->id = $this->leadsIds;
			$dataProvider = $duplicate->search([]);
			/** @var ActiveQuery $query */
			$query = $dataProvider->query;
			$query->joinWith('market', false, 'LEFT OUTER JOIN');
			$query->andWhere(LeadMarket::tableName() . '.id IS NULL');
			$ids = $dataProvider->getKeys();
			if (!empty($ids)) {
				$this->leadsIds = array_diff($this->leadsIds, $ids);
			}
		}
	}

	public function withoutAddressFilter(): void {
		if (!$this->hasErrors('leadsIds')) {
			$ids = LeadAddress::find()
				->select('lead_id')
				->andWhere(['lead_id' => $this->leadsIds])
				->distinct()
				->column();
			$this->withoutAddressCount = count($this->leadsIds) - count($ids);
			$this->leadsIds = $ids;
		}
	}

	public function getWithoutAddressCount(): int {
		return $this->withoutAddressCount;
	}

	public function load($data, $formName = null): bool {
		return parent::load($data, $formName)
			&& $this->getOptions()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors);
	}

	public function save(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
		$rows = [];
		foreach ($this->leadsIds as $leadId) {
			$rows[$leadId] = [
				'creator_id' => $this->creator_id,
				'lead_id' => $leadId,
				'status' => $this->status,
				'details' => $this->details,
				'options' => $this->getOptions()->toJson(),
			];
		}
		return LeadMarket::getDb()->createCommand()
			->batchInsert(LeadMarket::tableName(), [
				'creator_id',
				'lead_id',
				'status',
				'details',
				'options',
			], $rows)->execute();
	}

	public function saveReports(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}

		$statuses = Lead::find()
			->select(['status_id'])
			->indexBy('id')
			->andWhere(['id' => $this->leadsIds])
			->column();
		$rows = [];
		foreach ($statuses as $id => $status) {
			$rows[] = [
				'owner_id' => $this->creator_id,
				'lead_id' => $id,
				'status_id' => $status,
				'old_status_id' => $status,
				'details' => Yii::t('lead', 'Move Lead to Market'),
			];
		}
		return LeadReport::getDb()->createCommand()
			->batchInsert(LeadReport::tableName(), [
				'owner_id',
				'lead_id',
				'status_id',
				'old_status_id',
				'details',
			], $rows)->execute();
	}

	public function getOptions(): LeadMarketOptions {
		if ($this->options === null) {
			$this->options = new LeadMarketOptions();
		}
		return $this->options;
	}

	public function setOptions(LeadMarketOptions $options): void {
		$this->options = $options;
	}

}
