<?php

namespace common\modules\court\models;

use yii\base\Model;

class LawsuitSessionForm extends Model {

	private ?LawsuitSession $model = null;

	public int $lawsuit_id;

	public ?string $details = '';

	public ?string $date_at = '';
	public ?string $room = '';
	public ?string $location = null;

	public ?int $presence_of_the_claimant = null;
	public bool $is_cancelled = false;
	public ?string $url = null;

	private Lawsuit $lawsuit;

	public function rules(): array {
		return [
			[['lawsuit_id', 'presence_of_the_claimant', 'date_at'], 'required'],
			[['presence_of_the_claimant'], 'integer'],
			[['is_cancelled'], 'boolean'],
			[['date_at', 'room', 'details', 'location', 'url'], 'string'],
			['url', 'url'],
			[['date_at', 'room', 'details', 'location'], 'default', 'value' => null],
			['location', 'in', 'range' => array_keys(static::getLocationNames())],
			['presence_of_the_claimant', 'in', 'range' => array_keys(static::getPresenceOfTheClaimantNames())],
		];
	}

	public function attributeLabels(): array {
		return array_merge(LawsuitSession::instance()->attributeLabels(), [
		]);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->lawsuit_id = $this->lawsuit_id;
		$model->presence_of_the_claimant = $this->presence_of_the_claimant;
		$model->date_at = $this->date_at;
		$model->room = $this->room;
		$model->details = $this->details;
		$model->location = $this->location;
		$model->is_cancelled = $this->is_cancelled;
		$model->url = $this->url;
		if (!$model->save(false)) {
			return false;
		}
		return true;
	}

	public function setModel(LawsuitSession $model) {
		$this->model = $model;
		$this->lawsuit_id = $model->lawsuit_id;
		$this->date_at = $model->date_at;
		$this->room = $model->room;
		$this->details = $model->details;
		$this->location = $model->location;
		$this->presence_of_the_claimant = $model->presence_of_the_claimant;
		$this->is_cancelled = $model->is_cancelled;
		$this->url = $model->url;
		$this->setLawsuit($model->lawsuit);
	}

	public function getModel(): LawsuitSession {
		if ($this->model === null) {
			$this->model = new LawsuitSession();
		}
		return $this->model;
	}

	public static function getLocationNames(): array {
		return LawsuitSession::getLocationNames();
	}

	public static function getPresenceOfTheClaimantNames(): array {
		return LawsuitSession::getPresenceOfTheClaimantNames();
	}

	public function setLawsuit(Lawsuit $lawsuit) {
		$this->lawsuit_id = $lawsuit->id;
		$this->lawsuit = $lawsuit;
	}

	public function getLawsuit(): Lawsuit {
		return $this->lawsuit;
	}

}
