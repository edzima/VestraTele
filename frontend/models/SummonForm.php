<?php

namespace frontend\models;

use common\models\issue\Summon;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Form model for Summon in frontend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonForm extends Model {

	public $status;
	public ?string $realize_at;
	public ?string $realized_at;

	private Summon $model;

	/**
	 * {@inheritdoc}
	 *
	 * @param Summon $model
	 */
	public function __construct(Summon $model, $config = []) {
		$this->setModel($model);
		parent::__construct($config);
	}

	protected function setModel(Summon $model): void {
		if ($model->isNewRecord) {
			throw new InvalidArgumentException('$model must not be new record.');
		}
		$this->model = $model;
		$this->status = $model->status;
		$this->realize_at = $model->realize_at;
		$this->realized_at = $model->realized_at;
	}

	public function rules(): array {
		return [
			[['status'], 'required'],
			[['status'], 'integer'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[['realize_at', 'realized_at'], 'safe'],
			[['realize_at', 'realized_at'], 'date', 'format' => 'yyyy-MM-dd HH:ii'],
		];
	}

	public function attributeLabels(): array {
		return $this->getModel()->attributeLabels();
	}

	public function getModel(): Summon {
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->model;
		$model->status = $this->status;
		$model->realize_at = $this->realize_at;
		$model->realized_at = $this->realized_at;
		return $model->save();
	}

	public static function getStatusesNames(): array {
		return Summon::getStatusesNames();
	}
}
