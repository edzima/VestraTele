<?php

namespace frontend\models;

use common\models\issue\Issue;
use common\models\issue\IssueSearch as BaseIssueSearch;
use common\models\user\Worker;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class IssueSearch extends BaseIssueSearch {

	public $agents;

	public $onlyAsAgent = false;
	public $onlyAsTele = false;
	public $onlyAsLawyer = false;

	public $isAgent = false;
	public $isTele = false;
	public $isLawyer = false;

	public $user_id;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[
				[
					'id', 'agent_id', 'stage_id', 'type_id',
				], 'integer',
			],
			[['createdAtTo', 'createdAtFrom'], 'date', 'format' => DATE_ATOM],
			[['onlyAsAgent', 'onlyAsTele'], 'boolean'],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
			[
				[
					'created_at', 'updated_at', 'client_surname', 'victim_surname',
				], 'safe',
			],
		];
	}

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'onlyAsTele' => 'Jako tele',
				'onlyAsAgent' => 'Jako agent',
				'onlyAsLawyer' => 'Jako prawnik',
			];
	}

	/**
	 * @inheritdoc
	 */
	public function search(array $params): ActiveDataProvider {

		$query = Issue::find();

		$query->with([
			'agent.userProfile',
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
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}
		$this->archiveFilter($query);
		$query->andWhere(['or', ['lawyer_id' => $this->user_id], ['agent_id' => $this->agents], ['tele_id' => $this->user_id]]);

		if ($this->isAgent) {
			$query->andWhere(['or', ['lawyer_id' => $this->user_id], ['agent_id' => $this->agents], ['tele_id' => $this->user_id]]);
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'stage_id' => $this->stage_id,
			'type_id' => $this->type_id,
			'agent_id' => $this->agent_id,
		]);

		if ($this->onlyAsTele) {
			$query->andWhere(['tele_id' => $this->user_id]);
		}
		if ($this->onlyAsAgent) {
			$query->andWhere(['agent_id' => $this->user_id]);
		}
		if ($this->onlyAsLawyer) {
			$query->andWhere(['lawyer_id' => $this->user_id]);
		}

		$query->andFilterWhere(['like', 'client_surname', $this->client_surname])
			->andFilterWhere(['>=', 'created_at', $this->createdAtFrom])
			->andFilterWhere(['<=', 'created_at', $this->createdAtTo])
			->andFilterWhere(['like', 'victim_surname', $this->victim_surname]);

		return $dataProvider;
	}

	public function getAgentsList(): array {
		if ($this->isTele || $this->isLawyer) {
			return Worker::getSelectList([Worker::ROLE_AGENT, Worker::PERMISSION_ISSUE]);
		}
		return Worker::getSelectList([], true, function (Query $query) {
			$query->andWhere(['id' => $this->agents]);
		});
	}

}
