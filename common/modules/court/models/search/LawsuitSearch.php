<?php

namespace common\modules\court\models\search;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\user\query\UserProfileQuery;
use common\modules\court\models\Court;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\query\LawsuitQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * LawsuitSearch represents the model behind the search form of `common\modules\court\models\Lawsuit`.
 */
class LawsuitSearch extends Lawsuit {

	public $courtName;
	public $issue_id;
	public $customer;
	public $court_type;

	public $issueUserId;

	public $spiAppeal;

	public $onlyWithResult;

	public $spiToConfirm;
	public const SCENARIO_ISSUE_USER = 'issue_user_id';

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'spiToConfirm' => Yii::t('court', 'SPI to Confirm'),
				'court_type' => Yii::t('court', 'Type'),
				'onlyWithResult' => Yii::t('court', 'Only with Result'),
			];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['!issueUserId', 'required', 'on' => self::SCENARIO_ISSUE_USER],
			[['id', 'court_id', 'creator_id', 'issue_id'], 'integer'],
			[['onlyWithResult', 'spiToConfirm'], 'boolean'],
			[['is_appeal'], 'default', 'value' => null],
			[['courtName', 'result'], 'string'],
			[['customer', 'signature_act', 'details', 'created_at', 'updated_at', 'court_type', 'appeal'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params) {
		$query = Lawsuit::find();
		$query->joinWith('issues');
		$query->joinWith('court');
		$query->with('issues.customer.userProfile');
		$query->with('creator.userProfile');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		$this->applyIssueUserFilter($query);
		$this->applyCustomerFilter($query);
		$this->applySpiAppealFilter($query);
		$this->applyResultFilter($query);
		$this->applySPIToConfirmFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'court_id' => $this->court_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'creator_id' => $this->creator_id,
			'is_appeal' => $this->is_appeal,
			Court::tableName() . '.type' => $this->court_type,
		]);

		$query->andFilterWhere(['like', Lawsuit::tableName() . '.signature_act', $this->signature_act])
			->andFilterWhere(['like', Lawsuit::tableName() . '.details', $this->details])
			->andFilterWhere(['like', Lawsuit::tableName() . '.result', $this->details])
			->andFilterWhere(['like', Court::tableName() . '.name', $this->courtName])
			->andFilterWhere(['like', Issue::tableName() . '.id', $this->issue_id . '%', false]);

		$query->groupBy(Lawsuit::tableName() . '.id');

		return $dataProvider;
	}

	public static function getCourtsNames(): array {

		return ArrayHelper::map(
			Court::find()
				->andWhere([
					'id' => Lawsuit::find()
						->select('court_id')
						->distinct()
						->column(),
				])
				->asArray()
				->all(),
			'id', 'name');
	}

	private function applyCustomerFilter(ActiveQuery $query): void {
		if (!empty($this->customer)) {
			$query->joinWith([
				'issues.customer.userProfile' => function (UserProfileQuery $query) {
					$query->withFullName($this->customer);
				},
			]);
		}
	}

	private function applySpiAppealFilter(ActiveQuery $query): void {
		if (!empty($this->spiAppeal)) {
			$courts = Court::getCourtsIds($this->spiAppeal);
			$query->andWhere([
				'court_id' => $courts,
			]);
		}
	}

	public static function getCourtTypeNames(): array {
		return Court::getTypesNames();
	}

	private function applyIssueUserFilter(LawsuitQuery $query): void {
		if (!empty($this->issueUserId)) {
			$query->usersIssues((array) $this->issueUserId);
		}
	}

	private ?array $resultNames = null;

	public function getResultNames(): array {
		if ($this->resultNames === null) {
			$this->resultNames = Lawsuit::find()
				->select('result')
				->distinct()
				->indexBy('result')
				->column();
		}
		return $this->resultNames;
	}

	public function setResultNames(array $names): void {
		$this->resultNames = $names;
	}

	private function applySPIToConfirmFilter(ActiveQuery $query): void {
		if (strlen($this->spiToConfirm) === 1) {
			if ($this->spiToConfirm) {
				$query->andWhere(Lawsuit::tableName() . '.spi_confirmed_user IS NULL');
			} else {
				$query->andWhere(Lawsuit::tableName() . '.spi_confirmed_user IS NOT NULL');
			}
		}
	}

	private function applyResultFilter(LawsuitQuery $query): void {
		if (!empty($this->result)) {
			if ($this->result === Yii::t('court', 'Without Result')) {
				$this->onlyWithResult = true;
			} else {
				$query->andWhere([
					'like',
					Lawsuit::tableName() . '.result',
					$this->result,
				]);
			}
		}
		if (strlen($this->onlyWithResult) === 1) {
			if ($this->onlyWithResult) {
				$query->andWhere(Lawsuit::tableName() . '.result IS NOT NULL');
			} else {
				$query->andWhere(Lawsuit::tableName() . '.result IS NULL');
			}
		}
	}
}
