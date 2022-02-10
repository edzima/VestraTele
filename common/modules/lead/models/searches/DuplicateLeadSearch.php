<?php

namespace common\modules\lead\models\searches;

use common\models\SearchModel;
use common\modules\lead\models\DuplicateLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class DuplicateLeadSearch extends DuplicateLead implements SearchModel {

	public const STATUS_SAME = 'same';
	public const STATUS_VARIOUS = 'various';

	public $status;
	public $type_id;

	public function rules(): array {
		return [
			[['status', 'name', 'phone', 'provider'], 'string'],
			[['status_id', 'type_id', 'source_id'], 'integer'],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$this->load($params);
		$sub = Lead::find()
			->alias('duplicateLead')
			->addSelect([
				'duplicateLead.id',
				'duplicateLead.name',
				'duplicateLead.phone',
				'duplicateLead.provider',
				'duplicateLead.source_id',
				'duplicateLead.status_id',
				'duplicateCount' => 'count(*)',
			])
			->groupBy(['duplicateLead.phone', 'duplicateLead.email'])
			->andFilterWhere(['duplicateLead.provider' => $this->provider])
			->andFilterWhere(['duplicateLead.status_id' => $this->status_id])
			->andFilterWhere(['duplicateLead.source_id' => $this->source_id])
			->andFilterWhere(['like', 'duplicateLead.name', $this->name])
			->having('COUNT(*) >1');

		if (!empty($this->type_id)) {
			$sub->type($this->type_id);
		}
		if (!empty($this->phone)) {
			$sub->withPhoneNumber($this->phone);
		}

		$query = DuplicateLead::find()
			->from([
				'duplicateLead' => $sub,
			])
			->joinWith('samePhoneLeads sameLead')
			->joinWith('sameEmailLeads sameLead')
			->andWhere('duplicateLead.id <> sameLead.id')
			->distinct();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'attributes' => [
					'name',
					'phone',
					'duplicateCount',
				],
			],
		]);

		$this->applyStatusFilter($query);

		if (YII_ENV_TEST) {
			codecept_debug($query->createCommand()->getRawSql());
		}

		return $dataProvider;
	}

	public function getAllIds(ActiveQuery $query): array {
		$query = clone $query;
		$query->select('duplicateLead.id');
		return $query->column();
	}

	protected function applyStatusFilter(ActiveQuery $query): void {
		switch ($this->status) {
			case static::STATUS_SAME:
				$query->andWhere('sameLead.status_id = duplicateLead.status_id');
				break;
			case static::STATUS_VARIOUS:
				$query->andWhere('sameLead.status_id <> duplicateLead.status_id');
				break;
		}
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getStatusFilterNames(): array {
		return [
			static::STATUS_VARIOUS => Yii::t('lead', 'Various'),
			static::STATUS_SAME => Yii::t('lead', 'Same'),
		];
	}

	public static function getSourcesNames(): array {
		return LeadSource::getNames();
	}

}
