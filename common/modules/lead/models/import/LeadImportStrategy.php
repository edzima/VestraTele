<?php

namespace common\modules\lead\models\import;

use common\helpers\ArrayHelper;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\Lead;
use DateTime;
use Exception;
use ruskid\csvimporter\MultipleImportStrategy;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class LeadImportStrategy extends MultipleImportStrategy {

	public int $phoneColumn;
	public string $namePrefix;
	public ?int $nameColumn;

	public int $source_id;
	public $status_id;

	private int $index = 0;
	public ?string $provider;
	public $dateColumn;
	public string $dateFormat = 'Y-m-d H:i';

	public function __construct() {
		$this->tableName = Lead::tableName();
		$this->configs = $this->configStrategy();
		$this->skipImport = function ($row): bool {
			return $this->skipImport($row);
		};
		parent::__construct();
	}

	public function import(&$data): int {
		$this->index = 0;
		return parent::import($data);
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

					return $this->namePrefix . '.' . $this->index;
				},
			],
			[
				'attribute' => 'phone',
				'value' => function (array $line): string {
					$phone = $line[$this->phoneColumn];
					$model = new LeadForm(['phone' => $phone]);
					$model->validate(['phone']);
					return $model->getPhone();
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

		$skip = !$model->validate(['phone']);
		if ($skip) {
			Yii::warning('Skip Lead Row: ' . VarDumper::dumpAsString($row)
				. '. With errors: ' . VarDumper::dumpAsString($model->getErrors()),
				'lead.csvImport'
			);
		}
		return !$model->validate(['phone']);
	}

}
