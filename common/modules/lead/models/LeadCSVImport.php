<?php

namespace common\modules\lead\models;

use common\modules\lead\models\import\LeadImportStrategy;
use console\jobs\CsvImportJob;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\ImportInterface;
use Yii;
use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\FileHelper;
use yii\queue\closure\Behavior;
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

	public string $queueDir = '@runtime/lead-csv';

	public int $pushLimit = 10000;

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
			[['nameColumn', 'dateColumn'], 'default', 'value' => null],
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

	public function getRowsCount(): int {
		return count(file($this->csvFile->tempName));
	}

	public function push(): ?string {
		$job = new CsvImportJob();
		$job->importStrategy = $this->importStrategy();
		$fileName = $this->getQueueDir() . DIRECTORY_SEPARATOR . uniqid() . '.' . $this->csvFile->extension;
		$this->csvFile->saveAs($fileName);
		$reader = $this->reader();
		$reader->filename = $fileName;
		$job->reader = $reader;
		$queue = Yii::$app->queue;
		$queue->attachBehavior('closureJob', Behavior::class);
		return $queue->push($job);
	}

	public function import(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
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
		$import = new LeadImportStrategy();
		$import->status_id = $this->status_id;
		$import->source_id = $this->source_id;
		$import->phoneColumn = $this->phoneColumn;
		$import->dateColumn = $this->dateColumn;
		$import->nameColumn = $this->nameColumn;
		$import->namePrefix = $this->csvFile->getBaseName();
		$import->provider = $this->provider;
		return $import;
	}

	private function getQueueDir(): string {
		$queueDir = Yii::getAlias($this->queueDir);
		if (!file_exists($queueDir)) {
			FileHelper::createDirectory($queueDir);
		}
		return $queueDir;
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
