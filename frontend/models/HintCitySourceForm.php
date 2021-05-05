<?php

namespace frontend\models;

use common\models\hint\HintCity;
use common\models\hint\HintCitySource;
use common\models\hint\HintSource;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class HintCitySourceForm extends Model {

	public $source_id;
	public $phone;
	public $rating;
	public $status;
	public $details;

	private HintCity $hintCity;
	private ?HintCitySource $model = null;

	private array $sources = [];

	public function rules(): array {
		return [
			[['source_id', 'rating', 'phone', 'status'], 'required'],
			[['phone', 'rating', 'details', 'status'], 'string'],
			['phone', PhoneValidator::class, 'country' => 'PL'],
			['rating', 'in', 'range' => array_keys(static::getRatingsNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			['source_id', 'in', 'range' => array_keys($this->getSourcesNames())],
		];
	}

	public function getSourcesNames(): array {
		if (empty($this->sources)) {
			$sources = HintSource::find()
				->andWhere(['is_active' => true])
				->indexBy('id')
				->all();
			$hintSources = $this->hintCity->hintCitySources;
			foreach ($hintSources as $hintSource) {
				$sourceId = $hintSource->source_id;
				if ($this->getModel()->source_id !== $sourceId) {
					unset($sources[$sourceId]);
				}
			}
			$this->sources = ArrayHelper::map($sources, 'id', 'name');
		}
		return $this->sources;
	}

	public static function getRatingsNames(): array {
		return HintCitySource::getRatingsNames();
	}

	public static function getStatusesNames(): array {
		return HintCitySource::getStatusesNames();
	}

	public function attributeLabels(): array {
		return HintCitySource::instance()->attributeLabels();
	}

	public function setHintCity(HintCity $model) {
		$this->hintCity = $model;
	}

	public function getHintCity(): HintCity {
		return $this->hintCity;
	}

	public function setModel(HintCitySource $model): void {
		$this->model = $model;
		$this->hintCity = $model->hint;
		$this->status = $model->status;
		$this->source_id = $model->source_id;
		$this->details = $model->details;
		$this->phone = $model->phone;
		$this->rating = $model->rating;
	}

	public function getModel(): HintCitySource {
		if ($this->model === null) {
			$this->model = new HintCitySource();
		}
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->hint_id = $this->hintCity->id;
		$model->source_id = $this->source_id;
		$model->status = $this->status;
		$model->phone = $this->phone;
		$model->rating = $this->rating;
		$model->details = $this->details;
		return $model->save();
	}

}
