<?php

namespace common\modules\court\components;

use common\models\Address;
use common\modules\court\models\Court;
use edzima\teryt\models\Simc;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\base\Component;
use yii\base\InvalidValueException;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Console;

class CourtImporter extends Component {

	public string $tableName = '{{%court}}';

	public string $fileName;

	/**
	 * @var string|array|Connection
	 */
	public $db = 'db';

	public int $columnAppeal = 0;

	public int $columnType = 2;

	public int $columnName = 3;

	public int $columnStreet = 4;

	public int $columnPostalCode = 5;
	public int $columnCityName = 6;

	public int $columnPhone = 7;
	public int $columnFax = 8;
	public int $columnEmail = 9;

	public int $startFromRow = 1;
	/**
	 * @var bool|mixed
	 */
	public bool $withAddress = true;

	public string $csvSeparator = ';';

	public function init(): void {
		parent::init();
		$this->db = Instance::ensure($this->db, Connection::class);
	}

	public function getLastUpdateAt(): ?string {
		$query = new Query();
		$query->from($this->tableName);
		return $query->max('updated_at', $this->db);
	}

	public function import(): int {
		$data = $this->getData();
		$appeals = [];
		$count = 0;
		foreach ($data as $index => $row) {
			if ($index >= $this->startFromRow) {
				$appeal = $row[$this->columnAppeal] ?? null;
				if ($appeal) {
					$appeals[$row[$this->columnAppeal]][] = $row;
				}
			}
		}

		foreach ($appeals as $courts) {
			$appealId = null;
			$regionId = null;
			foreach ($courts as $court) {
				$name = $court[$this->columnName];
				$model = $this->getModel($name);
				$model->type = $court[$this->columnType];
				$model->phone = $court[$this->columnPhone];
				$model->fax = $court[$this->columnFax];
				$model->email = $court[$this->columnEmail];
				$model->updated_at = date('Y-m-d');
				if ($model->isAppeal()) {
					if ($model->save()) {
						$count++;
					}
					$appealId = $model->id;
				}
				if ($model->isRegional()) {
					if ($appealId === null) {
						throw new InvalidValueException('Appeal ID must be set for Regional Court');
					}
					$model->parent_id = $appealId;
					if ($model->save()) {
						$count++;
					}
					$regionId = $model->id;
				}
				if ($model->isDistrict()) {
					if ($regionId === null) {
						throw new InvalidValueException('Region ID must be set for District Court');
					}
					$model->parent_id = $regionId;
					if ($model->save()) {
						$count++;
					}
				}
				if ($this->withAddress) {
					$this->saveAddresses($model, $court);
				}
			}
		}
		return $count;
	}

	protected function getData() {
		$ext = pathinfo($this->fileName, PATHINFO_EXTENSION);
		if ($ext === 'csv') {
			return $this->csvToArray();
		}
		return $this->spreadsheetToArray();
	}

	protected function csvToArray(): array {
		$data = [];
		$csv = fopen($this->fileName, 'r');

		while (!feof($csv)) {
			$data[] = fgetcsv($csv, 1000, $this->csvSeparator);
		}

		fclose($csv);
		return $data;
	}

	protected function spreadsheetToArray(): array {
		$spreadsheet = IOFactory::load($this->fileName);
		$worksheet = $spreadsheet->getActiveSheet();
		return $worksheet->toArray();
	}

	protected function getModel(string $name): Court {
		$model = Court::find()
			->andWhere(['name' => $name])
			->one();
		if ($model) {
			return $model;
		}
		$model = new Court();
		$model->name = $name;
		return $model;
	}

	private function findCityId(string $name): int {
		$cities = Simc::find()->andWhere(['name' => $name])->onlyCities()->asArray()->all();
		if (count($cities) === 0) {
			throw new InvalidValueException('Not found City for name: ' . $name);
		}
		if (count($cities) === 1) {
			return $cities[0]['id'];
		}
		Console::output('Find more than one Cities with name: ' . $name);
		Console::output(print_r($cities));
		return (int) Console::prompt('ID for Valid Cities');
	}

	private function saveAddresses(Court $model, array $row): void {
		$model->unlinkAll('addresses', true);
		$cities = $this->trimParts($row[$this->columnCityName]);
		$streets = $this->trimParts($row[$this->columnStreet]);
		$postalCodes = $this->trimParts($row[$this->columnPostalCode]);
		foreach ($streets as $index => $street) {
			$address = new Address();
			$address->info = $street;
			$address->postal_code = $this->getPart($postalCodes, $index);
			$address->city_id = $this->findCityId($this->getPart($cities, $index));
			if ($address->save()) {
				$model->link('addresses', $address);
			}
		}
	}

	private function getPart(array $parts, int $index): ?string {
		if (empty($parts)) {
			return null;
		}
		if (count($parts) === 1) {
			return reset($parts);
		}
		if (isset($parts[$index])) {
			return $parts[$index];
		}
		$index--;
		return $this->getPart($parts, $index);
	}

	private function trimParts(string $text): array {
		$parts = explode(';', $text);
		return array_map('trim', $parts);
	}

}
