<?php

namespace frontend\models\search;

use common\models\issue\query\SummonQuery;
use common\models\issue\search\SummonSearch as BaseSummonSearch;
use common\models\issue\Summon;
use common\models\user\CustomerSearchInterface;
use common\models\user\User;
use yii\data\ActiveDataProvider;

/**
 * Summon search model for frontend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonSearch extends BaseSummonSearch {

	public $user_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'created_at', 'updated_at', 'realized_at', 'deadline_at', 'start_at', 'issue_id', 'owner_id', '!user_id'], 'integer'],
			[['title', 'customerPhone'], 'safe'],
			['doc_types_ids', 'in', 'range' => array_keys(static::getDocTypesNames()), 'allowArray' => true],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		/** @var SummonQuery $query */
		$query = $provider->query;
		if (empty($this->status)) {
			$query->active();
		}
		if (!empty($this->user_id)) {
			$query->user($this->user_id);
		}
		return $provider;
	}

	public function getOwnersNames(): array {
		if (empty($this->user_id)) {
			return parent::getOwnersNames();
		}
		return User::getSelectList(
			Summon::find()
				->select('owner_id')
				->distinct()
				->user($this->user_id)
				->column(), false
		);
	}
}
