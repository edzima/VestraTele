<?php

namespace frontend\models;

class AgentMeetSearch extends IssueMeetSearch {

	public static function getStatusNames(): array {
		$names = parent::getStatusNames();
		unset($names[static::STATUS_NEW]);
		return $names;
	}

	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'tele_id'], 'integer'],
			[['phone', 'client_name', 'client_surname', 'created_at', 'updated_at', 'date_at', 'details', 'cityName'], 'safe'],
		];
	}

}
