<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:34
 */

namespace frontend\models;

use common\models\issue\Issue;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class IssueSearch extends Issue {

	public $agents;

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
					'id', 'agent_id', 'stage_id', 'type_id', 'entity_responsible_id',
				], 'integer',
			],
			[
				[
					'created_at', 'updated_at', 'client_surname', 'victim_surname',
				], 'safe',
			],
		];
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = Issue::find();

		$query->with(['agent.userProfile', 'type', 'stage.types']);

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
			// $query->where('0=1');
			return $dataProvider;
		}

		if (!$this->isAgent) {
			if ($this->isTele) {
				$query->andFilterWhere(['tele_id' => $this->user_id]);
			}

			if ($this->isLawyer) {
				$query->andFilterWhere(['lawyer_id' => $this->user_id]);
			}
		} else {
			if ($this->isLawyer) {
				$query->andWhere(['or', ['lawyer_id' => $this->user_id], ['agent_id' => $this->agents]]);
			}
			if (!$this->isTele && !$this->isLawyer) {
				$query->andWhere(['agent_id' => $this->agents]);
			}
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'stage_id' => $this->stage_id,
			'type_id' => $this->type_id,
			'agent_id' => $this->agent_id,
		]);

		$query->andFilterWhere(['like', 'client_surname', $this->client_surname])
			->andFilterWhere(['like', 'victim_surname', $this->victim_surname]);

		return $dataProvider;
	}

	public function getAgentsList(): array {
		if ($this->isTele || $this->isLawyer) {
			return User::getSelectList([User::ROLE_AGENT]);
		}
		return User::getSelectList([User::ROLE_AGENT], function (Query $query) {
			$query->andWhere(['id' => $this->agents]);
		});
	}
}
