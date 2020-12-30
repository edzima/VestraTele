<?php

namespace common\models\issue;

use common\models\entityResponsible\EntityResponsible;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\issue\search\ArchivedIssueSearch;
use common\models\issue\search\IssueTypeSearch;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\Worker;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch represents the model behind the search form of `common\models\issue\Issue`.
 */
abstract class IssueSearch extends Model
	implements ArchivedIssueSearch,
			   CustomerSearchInterface,
			   IssueTypeSearch,
			   SearchModel {

	public $issue_id;
	public $stage_id;
	public $type_id;
	public $entity_responsible_id;

	public string $created_at = '';
	public string $updated_at = '';
	public string $createdAtFrom = '';
	public string $createdAtTo = '';
	public string $customerLastname = '';

	public bool $withArchive = false;

	public $agent_id;
	public $lawyer_id;
	public $tele_id;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'issue_id', 'agent_id', 'stage_id', 'entity_responsible_id',
				], 'integer',
			],
			[['createdAtTo', 'createdAtFrom'], 'date', 'format' => DATE_ATOM],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
			['type_id', 'in', 'range' => array_keys($this->getStagesNames()),'allowArray' => true],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			[
				[
					'created_at', 'updated_at',
				], 'safe',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return array_merge([
			'issue_id' => Yii::t('common', 'Issue'),
			'createdAtFrom' => Yii::t('common', 'Created at from'),
			'createdAtTo' => Yii::t('common', 'Created at to'),
			'agent_id' => Worker::getRolesNames()[Worker::ROLE_AGENT],
			'lawyer_id' => Worker::getRolesNames()[Worker::ROLE_LAWYER],
			'tele_id' => Worker::getRolesNames()[Worker::ROLE_TELEMARKETER],
		], Issue::instance()->attributeLabels());
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	abstract public function search(array $params): ActiveDataProvider;

	protected function issueQueryFilter(IssueQuery $query): void {
		$this->archiveFilter($query);
		$this->agentFilter($query);
		$this->applyCustomerSurnameFilter($query);
		$this->createdAtFilter($query);
		$query->andFilterWhere([
			Issue::tableName() . '.id' => $this->issue_id,
			Issue::tableName() . '.stage_id' => $this->stage_id,
			Issue::tableName() . '.type_id' => $this->type_id,
			Issue::tableName() . '.entity_responsible_id' => $this->entity_responsible_id,
		]);
	}

	protected function issueWith(): array {
		return [
			'agent.userProfile',
			'customer.userProfile',
			'entityResponsible',
			'stage.types',
			'type',
		];
	}

	protected function createdAtFilter(QueryInterface $query): void {
		$query->andFilterWhere(['>=', 'issue.' . 'created_at', $this->createdAtFrom])
			->andFilterWhere(['<=', 'issue.' . 'created_at', $this->createdAtTo]);
	}

	protected function archiveFilter(IssueQuery $query): void {
		if (!$this->getWithArchive()) {
			$query->withoutArchives();
		}
	}

	protected function agentFilter(IssueQuery $query): void {
		if (!empty($this->agent_id)) {
			$query->agents([$this->agent_id]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			/** @var IssueQuery $query */
			$query->joinWith([
				'users c' => function (IssueUserQuery $query): void {
					$query->andWhere(['c.type' => IssueUser::TYPE_CUSTOMER]);
					$query->joinWith('user.userProfile customerProfile');
				},
			]);
			$query->andWhere(['like', 'customerProfile.lastname', $this->customerLastname . '%', false]);
		}
	}

	public function getWithArchive(): bool {
		return $this->withArchive;
	}

	public static function getTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public static function getEntityNames(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

	public function getStagesNames(): array {
		return IssueStage::getStagesNames($this->getWithArchive());
	}

	public static function getIssueTypesNames(): array {
		return IssueType::getTypesNames();
	}
	public function applyIssueTypeFilter(QueryInterface $query): void {
		$query->andFilterWhere([Issue::tableName() . '.type_id' => $this->type_id]);

	}
}
