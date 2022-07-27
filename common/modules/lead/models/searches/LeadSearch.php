<?php

namespace common\modules\lead\models\searches;

use common\models\AddressSearch;
use common\models\query\PhonableQuery;
use common\models\SearchModel;
use common\models\user\User;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAddress;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadAnswerQuery;
use common\modules\lead\models\query\LeadQuery;
use common\validators\PhoneValidator;
use Yii;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * LeadSearch represents the model behind the search form of `common\modules\lead\models\Lead`.
 */
class LeadSearch extends Lead implements SearchModel {

	public const SCENARIO_USER = 'user';

	private const QUESTION_ATTRIBUTE_PREFIX = 'question';

	public bool $withoutUser = false;
	public bool $withoutReport = false;
	public bool $duplicateEmail = false;
	public $duplicatePhone;

	public bool $withoutArchives = true;

	public $fromMarket;

	public $withAddress;

	public $name = '';
	public $user_id;
	public $user_type;
	public $type_id;

	public $from_at;
	public $to_at;

	public string $reportsDetails = '';

	public $answers = [];
	public $closedQuestions = [];

	private array $questionsAttributes = [];

	private static ?array $QUESTIONS = null;

	public AddressSearch $addressSearch;
	private ?array $ids = null;

	public function __construct($config = []) {
		if (!isset($config['addressSearch'])) {
			$config['addressSearch'] = new AddressSearch();
		}
		parent::__construct($config);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'status_id', 'type_id', 'source_id', 'campaign_id'], 'integer'],
			['!user_id', 'required', 'on' => static::SCENARIO_USER],
			['!user_id', 'integer', 'on' => static::SCENARIO_USER],
			[['fromMarket', 'withoutUser', 'withoutReport', 'withoutArchives', 'duplicatePhone', 'duplicateEmail', 'withAddress'], 'boolean'],
			['name', 'string', 'min' => 3],
			[['date_at', 'data', 'phone', 'email', 'postal_code', 'provider', 'answers', 'closedQuestions', 'gridQuestions', 'user_type', 'reportsDetails'], 'safe'],
			['source_id', 'in', 'range' => array_keys($this->getSourcesNames())],
			['campaign_id', 'in', 'range' => array_keys($this->getCampaignNames())],
			['user_id', 'in', 'allowArray' => true, 'range' => array_keys(static::getUsersNames()), 'not' => static::SCENARIO_USER],
			[['from_at', 'to_at'], 'safe'],
			[array_keys($this->questionsAttributes), 'safe'],
			['phone', PhoneValidator::class],
		];
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(),
			[
				'withAddress' => Yii::t('lead', 'With Address'),
				'withoutArchives' => Yii::t('lead', 'Without Archives'),
				'withoutUser' => Yii::t('lead', 'Without User'),
				'withoutReport' => Yii::t('lead', 'Without Report'),
				'user_id' => Yii::t('lead', 'User'),
				'closedQuestions' => Yii::t('lead', 'Closed Questions'),
				'duplicateEmail' => Yii::t('lead', 'Duplicate Email'),
				'duplicatePhone' => Yii::t('lead', 'Duplicate Phone'),
				'user_type' => Yii::t('lead', 'Type'),
				'from_at' => Yii::t('lead', 'From At'),
				'to_at' => Yii::t('lead', 'To At'),
				'fromMarket' => Yii::t('lead', 'From Market'),
			]
		);
	}

	public function init() {
		parent::init();
		$this->ensureQuestionsAttributes();
	}

	private function ensureQuestionsAttributes(): void {
		if (empty($this->questionsAttributes)) {
			$attributes = [];
			foreach (static::questions() as $question) {
				$attributes[static::generateQuestionAttribute($question->id)] = '';
			}
			$this->questionsAttributes = $attributes;
		}
	}

	public function __get($name) {
		if ($this->isQuestionAttribute($name)) {
			if (isset($this->questionsAttributes[$name])) {
				return $this->questionsAttributes[$name];
			}
		}
		return parent::__get($name);
	}

	public function __set($name, $value) {
		if ($this->isQuestionAttribute($name)) {
			if (!isset($this->questionsAttributes[$name])) {
				throw new UnknownPropertyException();
			}
			$this->questionsAttributes[$name] = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	private function isQuestionAttribute($name): bool {
		return StringHelper::startsWith($name, static::QUESTION_ATTRIBUTE_PREFIX);
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
	public function search(array $params = []): ActiveDataProvider {
		$query = Lead::find()
			->joinWith('leadSource S')
			->with('status')
			->with('campaign')
			->with('owner.userProfile')
			->with('reports.answers')
			->with('reports.answers.question')
			->groupBy(Lead::tableName() . '.id');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'date_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);
		$this->addressSearch->load($params);

		if (!$this->validate()) {
			$query->where('0=1');
			Yii::warning($this->getErrors(), 'lead.search');
			return $dataProvider;
		}

		$this->applyAddressFilter($query);
		$this->applyAnswerFilter($query);
		$this->applyDateFilter($query);
		$this->applyDuplicates($query);
		$this->applyFromMarketFilter($query);
		$this->applyNameFilter($query);
		$this->applyUserFilter($query);
		$this->applyPhoneFilter($query);
		$this->applyStatusFilter($query);
		$this->applyReportFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			Lead::tableName() . '.id' => $this->id,
			Lead::tableName() . '.date_at' => $this->date_at,
			Lead::tableName() . '.campaign_id' => $this->campaign_id,
			Lead::tableName() . '.source_id' => $this->source_id,
			Lead::tableName() . '.provider' => $this->provider,
			'S.type_id' => $this->type_id,
		]);

		$query
			->andFilterWhere(['like', Lead::tableName() . '.data', $this->data])
			->andFilterWhere(['like', Lead::tableName() . '.email', $this->email])
			->andFilterWhere(['like', Lead::tableName() . '.postal_code', $this->postal_code]);

		if (YII_ENV_TEST) {
			codecept_debug($query->createCommand()->getRawSql());
		}

		return $dataProvider;
	}

	public function getAllIds(LeadQuery $query, bool $refresh = false): array {
		if ($refresh || $this->ids === null) {
			$query = clone $query;
			$query->select(Lead::tableName() . '.id');
			$this->applyDuplicates($query);
			$query->orderBy(['date_at' => SORT_DESC]);
			$this->ids = $query->column();
		}
		return $this->ids;
	}

	private function applyAddressFilter(ActiveQuery $query): void {
		if ($this->withAddress) {
			$query->joinWith('addresses.address.city');
			$query->andWhere(LeadAddress::tableName() . '.lead_id IS NOT NULL');
		}
		if ($this->addressSearch->validate()) {
			$query->joinWith([
				'addresses.address' => function (ActiveQuery $addressQuery) {
					$this->addressSearch->applySearch($addressQuery);
				},
			]);
		}
	}

	private function applyUserFilter(ActiveQuery $query): void {
		if (!empty($this->user_id)) {
			$query->joinWith('leadUsers');
			$query->andWhere([LeadUser::tableName() . '.user_id' => $this->user_id]);
			$query->andFilterWhere([LeadUser::tableName() . '.type' => $this->user_type]);
		}
		if ($this->withoutUser) {
			$query->joinWith('leadUsers', false, 'LEFT OUTER JOIN');
			$query->andWhere([LeadUser::tableName() . '.user_id' => null]);
		}
	}

	private function applyReportFilter(ActiveQuery $query) {
		if ($this->withoutReport) {
			$query->joinWith('reports', false, 'LEFT OUTER JOIN');
			$query->andWhere([LeadReport::tableName() . '.id' => null]);
		} else {
			if (!empty($this->reportsDetails)) {
				$query->joinWith('reports');
				$query->andWhere(['like', LeadReport::tableName() . '.details', $this->reportsDetails]);
			}
		}
	}

	private function applyAnswerFilter(ActiveQuery $query): void {
		if (!empty($this->closedQuestions)) {
			foreach ($this->closedQuestions as $closedQuestionId) {
				$this->answers[$closedQuestionId] = true;
			}
		}
		if (!empty($this->questionsAttributes)) {
			foreach ($this->questionsAttributes as $idWithPrefix => $value) {
				if ($value !== '') {
					$questionId = static::removeQuestionAttributePrefix($idWithPrefix);
					$question = static::questions()[$questionId] ?? null;
					if ($question) {
						if (!$question->hasPlaceholder()) {
							$value = (bool) $value;
						}
						$this->answers[$questionId] = $value;
					}
				}
			}
		}
		if (!empty($this->answers)) {

			//@todo fix multiple answers
			$query->joinWith([
				'reports.answers' => function (LeadAnswerQuery $answerQuery): void {
					$answerQuery->likeAnswers($this->answers);
				},
			], false);
		}
	}

	private function applyDuplicates(ActiveQuery $query): void {
		if ($this->duplicateEmail) {
			$query->addSelect([Lead::tableName() . '.*']);
		}
		if ($this->duplicateEmail) {
			$query->addSelect('COUNT(' . Lead::tableName() . '.email) as emailCount');
			$query->groupBy(Lead::tableName() . '.email');
			$query->having('emailCount > 1');
		}
		if ($this->duplicatePhone === null || $this->duplicatePhone === '') {
			return;
		}
		$query->addSelect([Lead::tableName() . '.*']);
		$query->addSelect(['COUNT(' . Lead::tableName() . '.phone) as phoneCount']);
		$query->groupBy(Lead::tableName() . '.phone');
		if ($this->duplicatePhone) {
			$query->having('phoneCount > 1');
		} else {
			$query->having('phoneCount = 1');
		}
	}

	private function applyNameFilter(ActiveQuery $query) {
		if (!empty($this->name)) {
			$query->andFilterWhere(['like', Lead::tableName() . '.name', $this->name]);
		}
	}

	public function getAddressSearch(): AddressSearch {
		return $this->addressSearch;
	}

	public function getCampaignNames(): array {
		if ($this->getScenario() === static::SCENARIO_USER) {
			return LeadCampaign::getNames($this->user_id);
		}
		return LeadCampaign::getNames();
	}

	public function getSourcesNames(): array {
		if ($this->getScenario() === static::SCENARIO_USER) {
			return LeadSource::getNames($this->user_id);
		}
		return LeadSource::getNames();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getTypesNames(): array {
		return LeadType::getNamesWithDescription();
	}

	public static function getUserTypesNames(): array {
		return LeadUser::getTypesNames();
	}

	public static function getUsersNames(string $type = null): array {
		return User::getSelectList(LeadUser::userIds($type));
	}

	/**
	 * @return LeadQuestion[]
	 */
	public static function questions(): array {
		if (static::$QUESTIONS === null) {
			static::$QUESTIONS = LeadQuestion::find()
				->showInGrid()
				->indexBy('id')
				->all();
		}
		return static::$QUESTIONS;
	}

	public static function getClosedQuestionsNames(): array {
		return ArrayHelper::map(
			LeadQuestion::find()
				->withoutPlaceholder()
				->all(),
			'id', 'name');
	}

	public static function generateQuestionAttribute(int $id): string {
		return static::QUESTION_ATTRIBUTE_PREFIX . $id;
	}

	private static function removeQuestionAttributePrefix(string $attribute): int {
		return substr($attribute, strlen(static::QUESTION_ATTRIBUTE_PREFIX));
	}

	private function applyPhoneFilter(PhonableQuery $query): void {
		if (!empty($this->phone)) {
			$query->withPhoneNumber($this->phone);
		}
	}

	private function applyStatusFilter(LeadQuery $query): void {
		if ((int) $this->status_id === LeadStatusInterface::STATUS_ARCHIVE) {
			$this->withoutArchives = false;
		}
		if ($this->withoutArchives && empty($this->status_id)) {
			$query->andWhere(['<>', Lead::tableName() . '.status_id', LeadStatusInterface::STATUS_ARCHIVE]);
		}
		$query->andFilterWhere([Lead::tableName() . '.status_id' => $this->status_id]);
	}

	private function applyDateFilter(LeadQuery $query) {
		if (!empty($this->from_at)) {
			$query->andWhere(['>=', Lead::tableName() . '.date_at', date('Y-m-d 00:00:00', strtotime($this->from_at))]);
		}
		if (!empty($this->to_at)) {
			$query->andWhere(['<=', Lead::tableName() . '.date_at', date('Y-m-d 23:59:59', strtotime($this->to_at))]);
		}
	}

	public function applyFromMarketFilter(LeadQuery $query): void {
		if ($this->fromMarket) {
			$query->joinWith('market');
			$query->andWhere(LeadMarket::tableName() . '.lead_id IS NOT NULL');
		}
	}
}
