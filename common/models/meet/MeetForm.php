<?php

namespace common\models\meet;

use common\models\address\Address;
use common\models\issue\IssueMeet;
use common\models\User;
use frontend\models\IssueMeetSearch;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\base\Model;

/**
 * Class MeetForm
 *
 * @property integer $id
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class MeetForm extends Model {

	public $agentId;
	public $clientName;
	public $clientSurname;
	public $dateStart;
	public $dateEnd;
	public $details;
	public $email;
	public $phone;
	public $status;
	public $typeId;

	/** @var Address */
	public $address;

	/**
	 * @var IssueMeet
	 */
	private $model;

	public function rules(): array {
		return [
			[['clientName', 'dateStart', 'typeId', 'status'], 'required'],
			[['clientName', 'clientSurname', 'phone', 'email', 'details'], 'string'],
			[['status', 'typeId'], 'integer'],
			['email', 'email'],
			['phone', PhoneValidator::class, 'country' => 'PL'],
			[
				'phone', 'required', 'when' => function () {
				return empty($this->email);
			},
				'enableClientValidation' => false,
				'message' => 'Numer wymagany, gdy nie ma e-mail.',
			],
			[
				'email', 'required', 'when' => function () {
				return empty($this->phone);
			}, 'enableClientValidation' => false,
				'message' => 'Mail wymagany, gdy nie ma numeru telefonu.',
			],
			[['dateStart', 'dateEnd'], 'safe'],
			[['dateStart', 'dateEnd'], 'date', 'format' => 'yyyy-MM-dd HH:mm'],
			['dateEnd', 'compare', 'compareAttribute' => 'dateStart', 'operator' => '>', 'enableClientValidation' => false],
			['typeId', 'in', 'range' => array_keys(static::getTypesNames())],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],

		];
	}

	public function attributeLabels(): array {
		return [
			'typeId' => 'Typ',
			'email' => 'E-mail',
			'clientName' => 'Imie',
			'clientSurname' => 'Nazwisko',
			'phone' => 'Telefon',
			'dateStart' => 'Data Wysyłki/Spotkania/Akcji',
			'dateEnd' => 'Koniec Wysyłki/Spotkania/Akcji',
			'details' => 'Szczegóły',
			'agentId' => 'Agent',
		];
	}

	public function getAddress(): Address {
		if ($this->address === null) {
			$this->address = new Address();
		}
		$this->address->requiredCity = false;
		return $this->address;
	}

	public function load($data, $formName = null) {
		return $this->getAddress()->load($data, null)
			&& parent::load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return $this->getAddress()->validate($attributeNames, $clearErrors)
			&& parent::validate($attributeNames, $clearErrors);
	}

	public function getName(): string {
		return $this->getModel()->getClientFullName();
	}

	private function getModel(): IssueMeet {
		if ($this->model === null) {
			$this->model = new IssueMeet();
		}
		return $this->model;
	}

	public function setModel(IssueMeet $model): void {
		$this->model = $model;
		$this->address = $model->getAddress();
		$this->agentId = $model->agent_id;
		$this->clientName = $model->client_name;
		$this->clientSurname = $model->client_surname;
		$this->dateStart = $model->date_at;
		$this->dateEnd = $model->date_end_at;
		$this->details = $model->details;
		$this->phone = $model->phone;
		$this->email = $model->email;
		$this->typeId = $model->type_id;
		$this->status = $model->status;
	}

	public function getIsNewRecord(): bool {
		return $this->getModel()->isNewRecord;
	}

	public function getId(): int {
		return $this->getModel()->id;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$this->setModelValues($model);

		return $model->save(false);
	}

	protected function setModelValues(IssueMeet $model): void {
		$model->setAddress($this->getAddress());
		$model->agent_id = $this->agentId;
		$model->client_name = $this->clientName;
		$model->client_surname = $this->clientSurname;
		$model->phone = $this->phone;
		$model->email = $this->email;

		$model->date_at = $this->dateStart;
		$model->date_end_at = $this->dateEnd;
		$model->details = $this->details;
		$model->status = $this->status;
		$model->type_id = $this->typeId;
	}

	public static function getTypesNames(): array {
		return IssueMeet::getTypesNames();
	}

	public static function getStatusNames(): array {
		return IssueMeet::getStatusNames();
	}

	public static function getCampaignNames(): array {
		return IssueMeetSearch::getCampaignNames();
	}

	public static function getAgentsNames(): array {
		return User::getSelectList([User::ROLE_MEET, User::ROLE_AGENT]);
	}
}
