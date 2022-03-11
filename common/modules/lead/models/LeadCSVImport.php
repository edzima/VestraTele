<?php

namespace common\modules\lead\models;

use common\helpers\ArrayHelper;
use common\modules\lead\models\forms\LeadForm;
use DateTime;
use Exception;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\ImportInterface;
use ruskid\csvimporter\MultipleImportStrategy;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\Json;
use yii\web\UploadedFile;

class LeadCSVImport extends Model {

	public const DELIMITER_COMMA = ',';
	public const DELIMITER_SEMICOLON = ';';
	public const DELIMITER_COLON = ':';

	/**
	 * @var UploadedFile
	 */
	public $csvFile;

	public int $phoneColumn = 0;
	public $nameColumn = 1;
	public $dateColumn = 2;

	public string $csvDelimiter = self::DELIMITER_SEMICOLON;
	public int $startFromLine = 1;

	public int $status_id = LeadStatus::STATUS_NEW;
	public $source_id;
	public ?string $provider = null;

	private int $index = 0;
	public string $dateFormat = 'Y-m-d H:i';

	public function behaviors(): array {
		return [
			'typecast' => [
				'class' => AttributeTypecastBehavior::class,
			],
		];
	}

	public static function getProvidersNames(): array {
		return Lead::getProvidersNames();
	}

	public function rules(): array {
		return [
			[['source_id', 'status_id', 'phoneColumn', 'csvDelimiter', 'startFromLine'], 'required'],
			[['phoneColumn', 'nameColumn', 'dateColumn', 'startFromLine'], 'integer', 'min' => 0],
			[['nameColumn', 'dateColumn'], 'default', 'value' => 'null'],
			[['source_id', 'status_id'], 'integer'],
			[
				'!csvFile',
				'file',
				'extensions' => 'csv',
				'skipOnEmpty' => false,
				'checkExtensionByMimeType' => false,
			],
			['source_id', 'in', 'range' => array_keys(static::getSourcesNames())],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['provider', 'in', 'range' => array_keys(static::getProvidersNames())],
			['csvDelimiter', 'in', 'range' => static::delimiters()],
		];
	}

	public function attributeLabels(): array {
		return [
			'csvFile' => Yii::t('csv', 'File'),
			'source_id' => Yii::t('lead', 'Source'),
			'status_id' => Yii::t('lead', 'Status'),
			'provider' => Yii::t('lead', 'Provider'),
			'phoneColumn' => Yii::t('lead', 'Phone'),
			'nameColumn' => Yii::t('lead', 'Name'),
			'dateColumn' => Yii::t('lead', 'Date At'),
			'csvDelimiter' => Yii::t('csv', 'Delimiter'),
			'startFromLine' => Yii::t('csv', 'Start From Line'),
		];
	}

	public function import(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
		$this->index = 0;
		$importer = new CSVImporter();
		$importer->setData($this->reader());
		return $importer->import($this->importStrategy());
	}

	protected function reader(): CSVReader {
		$reader = new CSVReader(['filename' => $this->csvFile->tempName]);
		$reader->filename = $this->csvFile->tempName;
		$reader->fgetcsvOptions = $this->fgetcsvOptions();
		$reader->startFromLine = $this->startFromLine;
		return $reader;
	}

	protected function fgetcsvOptions(): array {
		return [
			'length' => 0,
			'delimiter' => $this->csvDelimiter,
			'enclosure' => '"',
			'escape' => "\\",
		];
	}

	protected function importStrategy(): ImportInterface {
		return new MultipleImportStrategy([
			'tableName' => Lead::tableName(),
			'configs' => $this->configStrategy(),
			'skipImport' => function (array $row): bool {
				return $this->skipImport($row);
			},
		]);
	}

	protected function configStrategy(): array {
		return [
			[
				'attribute' => 'name',
				'value' => function (array $line) {
					if (is_int($this->nameColumn)) {
						$name = $line[$this->nameColumn] ?? null;
						if (!empty($name)) {
							return $name;
						}
					}

					return $this->csvFile->getBaseName() . '.' . $this->index;
				},
			],
			[
				'attribute' => 'phone',
				'value' => function (array $line): string {
					$phone = $line[$this->phoneColumn];
					$model = new LeadForm(['phone' => $phone]);
					$model->validate(['phone']);
					return $model->phone;
				},
				'unique' => true,
			],
			[
				'attribute' => 'source_id',
				'value' => function (): int {
					return $this->source_id;
				},
			],
			[
				'attribute' => 'status_id',
				'value' => function (): int {
					return $this->status_id;
				},
			],
			[
				'attribute' => 'provider',
				'value' => function (): ?string {
					return $this->provider;
				},
			],
			[
				'attribute' => 'date_at',
				'value' => function (array $row): string {
					if (is_int($this->dateColumn) && isset($row[$this->dateColumn])) {
						try {
							return (new DateTime($row[$this->dateColumn]))
								->format($this->dateFormat);
						} catch (Exception $exception) {
							return date($this->dateFormat);
						}
					}
					return date($this->dateFormat);
				},
			],
			[
				'attribute' => 'data',
				'value' => function (array $row): string {
					try {
						return Json::encode($row);
					} catch (InvalidArgumentException $e) {
						if ($e->getCode() === JSON_ERROR_UTF8) {
							$row = ArrayHelper::toUtf8($row);
							return Json::encode($row);
						}
						throw $e;
					}
				},
			],
		];
	}

	protected function skipImport(array $row): bool {
		$this->index++;
		$phone = $row[$this->phoneColumn] ?? null;
		if (!$phone) {
			return true;
		}
		$model = new LeadForm(['phone' => $phone]);
		$model->phone = $phone;

		return !$model->validate(['phone']);
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getSourcesNames(): array {
		return LeadSource::getNames();
	}

	public static function delimiters(): array {
		return [
			static::DELIMITER_COMMA => ',',
			static::DELIMITER_COLON => ':',
			static::DELIMITER_SEMICOLON => ';',
		];
	}

}
