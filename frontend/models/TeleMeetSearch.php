<?php

namespace frontend\models;

class TeleMeetSearch extends IssueMeetSearch {

	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'agent_id'], 'integer'],
			[['phone', 'client_name', 'client_surname', 'created_at', 'updated_at', 'date_at', 'details', 'cityName'], 'safe'],
		];
	}
}
