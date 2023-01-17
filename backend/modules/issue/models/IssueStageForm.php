<?php

namespace backend\modules\issue\models;

use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class IssueStageForm extends Model {

	public string $name = '';
	public string $short_name = '';
	public $posi;

	private ?IssueStage $model = null;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name', 'typesIds'], 'required'],
			[['posi', 'days_reminder'], 'integer'],
			['posi', 'default', 'value' => 0],
			[['name', 'short_name'], 'string', 'max' => 255],
			[
				['name', 'short_name'],
				'unique',
				'targetClass' => IssueStage::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
		];
	}

	public function getModel(): IssueStage {
		if ($this->model === null) {
			$this->model = new IssueStage();
		}
		return $this->model;
	}

	public function setModel(IssueStage $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->short_name = $model->short_name;
		$this->posi = $model->posi;
	}

	public function attributeLabels(): array {
		return array_merge(IssueStage::instance()->attributeLabels(), [
			'typesIds' => Yii::t('issue', 'Types'),
		]);
	}

	public function save(bool $validate = true) {
		if ($validate && !$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->name = $this->name;
		$model->short_name = $this->short_name;
		$model->posi = $this->posi;

		if (!$model->save()) {
			return false;
		}

		return true;
	}

}
