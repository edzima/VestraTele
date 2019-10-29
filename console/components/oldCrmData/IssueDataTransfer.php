<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-03
 * Time: 16:15
 */

namespace console\components\oldCrmData;

use common\models\City;
use common\models\issue\Issue;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\IssueStage;
use common\models\issue\Provision;
use common\models\Wojewodztwa;
use console\components\oldCrmData\exceptions\CityNotFoundExcepion;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class IssueDataTransfer extends DataTransfer {

	public $model = Issue::class;
	public $oldTableName = '{{%issues}}';
	public $removeAll = false;
	public $changeAutoincreament = false;

	private $states = [];
	private $entityResponsible = [];

	private const STAGES_MAP = [
		'Archiwum' => 6,
		'Archiwum tymczasowe' => 7,
		'SĄD' => 5,
		'Negocjacje Ugodowe' => 4,
		'Przekazanie do Kancelarii' => 14,
		'Kompletowanie dokumentów' => 1,
		'Postępowanie Likwidacyjne' => 2,
		'Postępowanie odwoławcze' => 3,
	];

	private const TYPE_ACCIDENT = 1;
	private const TYPES_MAP = [
		'np' => 2,
		'sd' => 3,
	];

	public function init() {
		parent::init();
		if ($this->changeAutoincreament) {
			$this->changeAutoincrementID();
		}

		$this->queryCondition = function (Query $query) {
			$query->andWhere(['id' => $this->getNotTransferedIds()]);
		};
	}

	private function changeAutoincrementID(): void {
		Yii::$app->db->createCommand(new Expression('alter table {{%issue}} auto_increment=1431'))->execute();
	}

	protected function createNewModel(): ActiveRecord {
		$model = parent::createNewModel();
		$model->detachBehaviors();
		return $model;
	}

	protected function getAttributesData(array $row): array {
		$data = [];
		$data['id'] = $row['id'];
		$data['client_first_name'] = trim($row['client_name']);
		$data['client_surname'] = trim($row['client_surname']);
		$clientCity = $this->getCity($row['client_city'], $row['client_region'], $row['client_province']);
		$data['client_city_id'] = $clientCity->id;
		$data['client_street'] = trim($row['client_street']);
		$data['client_city_code'] = $this->preparePostcode($row['client_postal']);
		$data['client_phone_1'] = $this->preparePhone($row['client_phone1']);
		$data['client_phone_2'] = $this->preparePhone($row['client_phone2']);

		$data['victim_first_name'] = trim($row['victim_name']);
		$data['victim_surname'] = trim($row['victim_surname']);
		if ($row['client_city'] !== $row['victim_city']) {
			$victimCity = $this->getCity($row['victim_city'], $row['victim_region'], $row['victim_province']);
			$data['victim_city_id'] = $victimCity->id;
		}
		$data['victim_city_code'] = $this->preparePostcode($row['victim_postal']);
		$data['victim_street'] = trim($row['victim_street']);
		$data['victim_phone'] = $this->preparePhone($row['victim_phone1']);

		$data['agent_id'] = $this->getUserId($row['issue_user']);
		$data['entity_responsible_id'] = $this->getEntityResponsibleId($row['issue_insurer']);
		$data['type_id'] = $this->getTypeId($row['issue_status']);
		$data['stage_id'] = $this->getStageId($row['issue_stage']);
		if ($data['stage_id'] === IssueStage::ARCHIVES_ID) {
			$data['archives_nr'] = 'nieznane';
		}
		$provisionBase = (float) $row['issue_money'];
		if ($provisionBase > 0) {
			$data['provision_base'] = $provisionBase;
		}
		$provisionValue = (float) $row['issue_percentage'];
		if ($provisionValue > 0) {
			$data['provision_value'] = $provisionValue;
		}
		$data['provision_type'] = Provision::TYPE_PERCENTAGE;

		$data['created_at'] = $row['datestamp'];
		$data['updated_at'] = $row['edit_datestamp'];

		$data['date'] = $row['issue_date'];
		$data['lawyer_id'] = $this->getUserId($row['issue_lawyer']);
		$data['details'] = json_encode($row, JSON_PRETTY_PRINT);
		return $data;
	}

	private function getUserId(int $oldId): int {
		return Yii::$app->userData->getUserId($oldId);
	}

	private function preparePostcode(string $postcode) {
		return substr(trim($postcode), 0, 6);
	}

	private function preparePhone(string $phone) {
		return substr(trim($phone), 0, 15);
	}

	protected function afterSaveModel(ActiveRecord $model, array $row): void {
		parent::afterSaveModel($model, $row);
		/** @var $model Issue */

		$this->saveNotes($model->id);
	}

	private function saveNotes(int $issueId): void {
		$notes = new IssueNoteDataTransfer(['issueId' => $issueId]);
		$notes->transfer();
	}

	private function getTypeId(string $statusShortCut): int {
		return static::TYPES_MAP[$statusShortCut] ?? static::TYPE_ACCIDENT;
	}

	private function getCity(string $name, string $state, string $province): City {
		try {
			$city = $this->findCity($name, $state, $province);
		} catch (CityNotFoundExcepion $exception) {
			$city = City::getNotExistCity();
		}
		return $city;
	}

	private function findCity(string $name, string $state, string $province): City {
		$cities = $this->findCities($name);
		$stateID = $this->getStateId(trim($state));
		$province = ucfirst(strtolower(trim($province)));
		$count = count($cities);
		if ($count === 1) {
			$this->message('Find only one city');
			return reset($cities);
		}
		foreach ($cities as $city) {
			$powiat = $city->powiatRel;
			if ($powiat === null) {
				$this->message('Remove city without powiat');
				$city->delete();
				continue;
			}
			if ($powiat->name === $province) {
				$this->message('Find good powiat with province');
				return $city;
			}
			if ($city->wojewodztwo_id === $stateID) {
				$this->message('Find good city with state');
				return $city;
			}
		}
		$this->message("City not found: name: $name");

		throw new CityNotFoundExcepion('Not found city: ' . $name . ' Province: ' . $province);
	}

	/**
	 * @param string $name
	 * @return City[]
	 */
	protected function findCities(string $name): array {
		return City::find()
			->with('powiatRel')
			->where(['name' => $name])
			->all();
	}

	private function getStateId(string $stateName): int {
		return $this->getStates()[$stateName] ?? $this->addState($stateName)->id;
	}

	private function getStates(): array {
		if (empty($this->states)) {
			$this->states = ArrayHelper::map(Wojewodztwa::find()
				->select('id,name')
				->asArray()
				->all(), 'name', 'id');
		}
		return $this->states;
	}

	private function addState(string $name): Wojewodztwa {
		$model = new Wojewodztwa();
		$model->name = $name;
		$this->message("Add new state: $name");
		$model->save();

		$this->states[$model->name] = $model->id;
		return $model;
	}

	private function getEntityResponsibleId(string $name): int {
		return $this->getEntitiesResponsible()[strtolower(trim($name))];
	}

	private function getEntitiesResponsible(): array {
		if (empty($this->entityResponsible)) {
			$this->entityResponsible = ArrayHelper::map(EntityResponsible::find()
				->select('id,LOWER(name) as name')
				->asArray()
				->all(),
				'name',
				'id');
		}
		return $this->entityResponsible;
	}

	private function getStageId(string $name): int {
		return static::STAGES_MAP[$name];
	}

	public function getIds(): array {
		return (new Query())
			->select('id')
			->from($this->oldTableName)
			->column($this->oldDb);
	}

	public function getNotTransferedIds(): array {
		return (new Query())
			->select('id')
			->from($this->oldTableName)
			->andWhere([
				'not in',
				'id',
				Issue::find()->select('id')->column(),
			])
			->column($this->oldDb);
	}

}