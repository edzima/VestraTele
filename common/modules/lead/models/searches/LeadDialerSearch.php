<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\components\LeadDialer as LeadDialerComponent;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

class LeadDialerSearch extends Model {

	private static array $IDS = [];
	private static array $DIALERS_NAMES = [];

	private LeadDialerComponent $dialer;

	public string $from_at = '';
	public string $to_at = '';

	public function rules(): array {
		return [
			[['from_at', 'to_at'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return [
			'from_at' => Yii::t('lead', 'From At'),
			'to_at' => Yii::t('lead', 'To At'),
		];
	}

	public array $dataProviderOptions = [
		'class' => ActiveDataProvider::class,
	];

//	public function __construct(LeadDialer $dialer, $config = []) {
//		$this->dialer = $dialer;
//		parent::__construct($config);
//	}

	public function getNewWithoutUserDataProvider(): ?DataProviderInterface {
		if (!$this->dialer->withNewWithoutUser) {
			return null;
		}
		$query = Lead::find()
			->withoutUsers()
			->andWhere([Lead::tableName() . '.status_id' => LeadStatusInterface::STATUS_NEW]);

		return $this->createDataProvider($query);
	}

	public function getCallingDataProvider(): DataProviderInterface {
		$query = LeadReport::find()
			->andWhere([LeadReport::tableName() . '.status_id' => $this->dialer->callingStatus])
			->andWhere([LeadReport::tableName() . '.owner_id' => $this->getDialerId()])
			->joinWith('lead')
			->andWhere([Lead::tableName() . '.status_id' => $this->dialer->callingStatus])
			->orderBy([LeadReport::tableName() . '.created_at' => SORT_DESC]);

		$this->dateReportFilter($query);

		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

	public function getAnsweredDataProvider(): DataProviderInterface {
		$query = LeadReport::find()
			->andWhere([LeadReport::tableName() . '.status_id' => $this->dialer->notAnsweredStatus])
			->andWhere([LeadReport::tableName() . '.owner_id' => $this->getDialerId()])
			->joinWith('lead')
			->andWhere([Lead::tableName() . '.status_id' => $this->dialer->notAnsweredStatus])
			->orderBy([LeadReport::tableName() . '.created_at' => SORT_DESC]);

		$this->dateReportFilter($query);

		return $this->createDataProvider($query);
	}

	public function getNotAnsweredDataProvider(): DataProviderInterface {
		$query = LeadReport::find()
			->andWhere([LeadReport::tableName() . '.status_id' => $this->dialer->answeredStatus])
			->andWhere([LeadReport::tableName() . '.owner_id' => $this->getDialerId()])
			->joinWith('lead')
			->andWhere([Lead::tableName() . '.status_id' => $this->dialer->answeredStatus])
			->orderBy([LeadReport::tableName() . '.created_at' => SORT_DESC]);
		$this->dateReportFilter($query);
		return $this->createDataProvider($query);
	}

	public function getToCallDataProvider(): DataProviderInterface {
		$query = $this->dialer->notAnsweredLeadsQuery();
		return $this->createDataProvider($query);
	}

	protected function dateReportFilter(ActiveQuery $query): void {
		if (!empty($this->from_at)) {
			$query->andWhere(['>=', LeadReport::tableName() . '.created_at', date('Y-m-d 00:00:00', strtotime($this->from_at))]);
		}
		if (!empty($this->to_at)) {
			$query->andWhere(['<=', LeadReport::tableName() . '.created_at', date('Y-m-d 23:59:59', strtotime($this->to_at))]);
		}
	}

	protected function createDataProvider(QueryInterface $query): ActiveDataProvider {
		$options = $this->dataProviderOptions;
		$options['query'] = $query;
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($options);
	}

	public function getDialerId(): int {
		return $this->dialer->userId;
	}

	public function getDialerName(): string {
		return static::getDialersNames()[$this->getDialerId()];
	}

	public static function getDialersNames(): array {
		if (empty(static::$DIALERS_NAMES)) {
			return static::$DIALERS_NAMES = User::getSelectList(static::getDialersIds(), false);
		}
		return static::$DIALERS_NAMES;
	}

	public static function getDialersIds(): array {
		if (empty(static::$IDS)) {
			static::$IDS = LeadUser::userIds(LeadUser::TYPE_DIALER);
		}
		return static::$IDS;
	}

	public function search(array $params = []) {
		$query = LeadDialer::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		return $dataProvider;
	}
}
