<?php

namespace common\modules\lead\models\import;

use common\modules\lead\entities\FBCampaign;
use common\modules\lead\models\LeadCost;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\UploadedFile;

class FBAdsCostImport extends Model {

	public $file;

	public bool $createCampaigns = true;

	public string $separator = ',';
	public int $startFromLine = 1;

	public const COLUMN_CAMPAIGN_ENTITY_ID = 'campaign.entity_id';
	public const COLUMN_ADSET_ENTITY_ID = 'adset.entity_id';

	public const COLUMN_AD_ENTITY_ID = 'ad.entity_id';

	public const COLUMN_CAMPAIGN_NAME = 'campaign.name';
	public const COLUMN_ADSET_NAME = 'adset.name';

	public const COLUMN_AD_NAME = 'ad.name';

	public const COLUMN_VALUE = 'value';
	public const COLUMN_DATE = 'date';

	public array $columns = [
		self::COLUMN_CAMPAIGN_ENTITY_ID => 0,
		self::COLUMN_ADSET_ENTITY_ID => 1,
		self::COLUMN_AD_ENTITY_ID => 2,
		self::COLUMN_CAMPAIGN_NAME => 3,
		self::COLUMN_ADSET_NAME => 4,
		self::COLUMN_AD_NAME => 5,
		self::COLUMN_DATE => 6,
		self::COLUMN_VALUE => 7,
	];

	public string $sortableColumns = '';

	private FBCampaign $fbCampaign;

	public function init(): void {
		parent::init();
		$this->fbCampaign = new FBCampaign();
	}

	public function rules(): array {
		return [
			[['file', 'columns', 'createCampaigns'], 'required'],
			[['createCampaigns'], 'boolean'],
			['file', 'file'],
			[
				'columns', 'validateKeyValue',
			],
			[['sortableColumns'], 'string'],
		];
	}

	public function attributeLabels(): array {
		return [
			'file' => Yii::t('lead', 'CSV File'),
			'createCampaigns' => Yii::t('lead', 'Create Campaigns'),
			'sortableColumns' => Yii::t('lead', 'Sortable Columns'),
		];
	}

	public function beforeValidate(): bool {
		$parent = parent::beforeValidate();
		if (!empty($this->sortableColumns)) {
			$this->columns = array_flip(explode(',', $this->sortableColumns));
		}
		return $parent;
	}

	public function validateKeyValue($attribute, $params, $validator) {
		$value = $this->$attribute;
		$required = [self::COLUMN_DATE, self::COLUMN_VALUE];
		foreach ($required as $key) {
			if (!isset($value[$key])) {
				$this->addError($attribute, 'Columns value with key: "' . $key . '" is required.');
			}
		}
	}

	public function import(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}

		$fileName = $this->file instanceof UploadedFile ? $this->file->tempName : $this->file;
		$file = fopen($fileName, 'r');
		$i = 0;
		$rows = [];
		while (($data = fgetcsv($file, 1000, $this->separator)) !== false) {
			if ($i >= $this->startFromLine) {
				$campaignId = $this->getLeadCampaignId($data);
				if ($campaignId) {
					$value = $this->getDataValue($data, static::COLUMN_VALUE);
					$date = $this->getDataValue($data, static::COLUMN_DATE);
					$rows[] = [
						'campaign_id' => $campaignId,
						'date_at' => $date,
						'value' => $value,
					];
				}
			}
			$i++;
		}

		fclose($file);
		if (empty($rows)) {
			return 0;
		}

		return LeadCost::batchUpsert([
			'campaign_id',
			'date_at',
			'value',
		], $rows);
	}

	protected function getDataValue(array $row, string $columnKey): string {
		if (!isset($this->columns[$columnKey])) {
			throw new InvalidConfigException('Column: ' . $columnKey . ' must be set.');
		}
		$column = $this->columns[$columnKey];
		if (!isset($row[$column])) {
			throw new InvalidConfigException('Column: ' . $column . ' must be in rows.');
		}
		return $row[$column];
	}

	private function getLeadCampaignId(array $row): ?int {
		$pixel = $this->fbCampaign;
		$pixel->createCampaigns = $this->createCampaigns;
		$pixel->setAttributes($this->getPixelCampaignAttributes($row));
		$model = $pixel->getLeadCampaign();
		if ($model) {
			return $model->id;
		}
		return null;
	}

	private function getPixelCampaignAttributes(array $row): array {
		return [
			'campaignId' => $this->getDataValue($row, static::COLUMN_CAMPAIGN_ENTITY_ID),
			'campaignName' => $this->getDataValue($row, static::COLUMN_CAMPAIGN_NAME),
			'adsetId' => $this->getDataValue($row, static::COLUMN_ADSET_ENTITY_ID),
			'adsetName' => $this->getDataValue($row, static::COLUMN_ADSET_NAME),
			'adName' => $this->getDataValue($row, static::COLUMN_AD_NAME),
			'adId' => $this->getDataValue($row, static::COLUMN_AD_ENTITY_ID),
		];
	}

	public function getSortableItemsData(): array {
		return [
			self::COLUMN_CAMPAIGN_ENTITY_ID => [
				'content' => Yii::t('lead', 'Campaign Entity ID'),
			],
			self::COLUMN_ADSET_ENTITY_ID => [
				'content' => Yii::t('lead', 'Adset Entity ID'),
			],
			self::COLUMN_AD_ENTITY_ID => [
				'content' => Yii::t('lead', 'Ad Entity ID'),
			],
			self::COLUMN_CAMPAIGN_NAME => [
				'content' => Yii::t('lead', 'Campaign Name'),
			],
			self::COLUMN_ADSET_NAME => [
				'content' => Yii::t('lead', 'Adset Name'),
			],
			self::COLUMN_AD_NAME => [
				'content' => Yii::t('lead', 'Ad Name'),
			],
			self::COLUMN_DATE => [
				'content' => Yii::t('lead', 'Date At'),
			],
			self::COLUMN_VALUE => [
				'content' => Yii::t('lead', 'Value'),
			],

		];
	}

}
