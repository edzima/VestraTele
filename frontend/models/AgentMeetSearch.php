<?php

namespace frontend\models;

class AgentMeetSearch extends IssueMeetSearch {

	public function rules(): array {
		return [
			[['id', 'type_id', 'status', 'tele_id', 'campaign_id', 'stateId'], 'integer'],
			[['phone', 'client_name', 'client_surname', 'created_at', 'updated_at', 'date_at', 'details', 'cityName'], 'safe'],
		];
	}

}
