<?php

namespace common\models\issue;

use common\models\entityResponsible\EntityResponsible;
use common\models\issue\query\IssueQuery;
use common\models\user\Worker;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch represents the model behind the search form of `common\models\issue\Issue`.
 */
class IssueSearch extends Issue {

	public string $createdAtFrom = '';
	public string $createdAtTo = '';
	public string $customerLastname = '';

	public bool $withArchive = false;

	public $agent_id;
	public $lawyer_id;
	public $tele_id;

	private ?array $stages = null;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'id', 'agent_id', 'tele_id', 'lawyer_id', 'stage_id', 'type_id', 'entity_responsible_id',
				], 'integer',
			],
			[['createdAtTo', 'createdAtFrom', 'accident_at'], 'date', 'format' => DATE_ATOM],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
			[
				[
					'created_at', 'updated_at', 'customerLastname',
				], 'safe',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return array_merge([
			'createdAtFrom' => Yii::t('common', 'Created at from'),
			'createdAtTo' => Yii::t('common', 'Created at to'),
			'agent_id' => Worker::getRolesNames()[Worker::ROLE_AGENT],
			'lawyer_id' => Worker::getRolesNames()[Worker::ROLE_LAWYER],
			'tele_id' => Worker::getRolesNames()[Worker::ROLE_TELEMARKETER],
		], parent::attributeLabels());
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
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
	public function search(array $params): ActiveDataProvider {
		$query = Issue::find();

		$query->with([
			'agent.userProfile',
			'customer.userProfile',
			'type',
			'stage.types',
		]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$this->agentFilter($query);
		$this->teleFilter($query);
		$this->lawyerFilter($query);
		$this->archiveFilter($query);
		$this->customerFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'stage_id' => $this->stage_id,
			'type_id' => $this->type_id,
			'entity_responsible_id' => $this->entity_responsible_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'accident_at' => $this->accident_at,
		]);

		$query->andFilterWhere(['>=', 'created_at', $this->createdAtFrom])
			->andFilterWhere(['<=', 'created_at', $this->createdAtTo]);

		return $dataProvider;
	}

	protected function archiveFilter(IssueQuery $query): void {
		if (!$this->withArchive) {
			$query->withoutArchives();
		}
	}

	protected function agentFilter(IssueQuery $query): void {
		if (!empty($this->agent_id)) {
			$query->agents([$this->agent_id]);
		}
	}

	protected function customerFilter(IssueQuery $query): void {
		if (!empty($this->customerLastname)) {
			$query->joinWith('users.user.userProfile');
			$query->andWhere(['like', 'user_profile.lastname', $this->customerLastname]);
		}
	}

	protected function lawyerFilter(IssueQuery $query): void {
		if (!empty($this->lawyer_id)) {
			$query->lawyers([$this->lawyer_id]);
		}
	}

	protected function teleFilter(IssueQuery $query): void {
		if (!empty($this->tele_id)) {
			$query->tele([$this->tele_id]);
		}
	}

	public static function getTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function getStagesNames(): array {
		if ($this->stages === null) {
			$this->stages = ArrayHelper::map(IssueStage::find()->all(), 'id', 'nameWithShort');
			if (!$this->withArchive) {
				unset($this->stages[IssueStage::ARCHIVES_ID]);
			}
		}
		return $this->stages;
	}

	public static function getEntityNames(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}
}
