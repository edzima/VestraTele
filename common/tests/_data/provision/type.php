<?php

use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\ProvisionType;
use yii\helpers\Json;

return [
	'agent-percent-25' => [
		'name' => 'Agent Honorarium',
		'is_percentage' => 1,
		'value' => 25,
		'data' => Json::encode([
			ProvisionType::KEY_DATA_ISSUE_USER_TYPE => IssueUser::TYPE_AGENT,
			ProvisionType::KEY_DATA_WITH_HIERARCHY => true,
			ProvisionType::KEY_DATA_CALCULATION_TYPES => [
				IssuePayCalculation::TYPE_HONORARIUM,
			],
		]),
	],
	'tele-percent-5' => [
		'name' => 'Tele Honorarium',
		'is_percentage' => 1,
		'value' => 5,
		'data' => Json::encode([
			ProvisionType::KEY_DATA_ISSUE_USER_TYPE => IssueUser::TYPE_TELEMARKETER,
			ProvisionType::KEY_DATA_WITH_HIERARCHY => false,
			ProvisionType::KEY_DATA_CALCULATION_TYPES => [
				IssuePayCalculation::TYPE_HONORARIUM,
			],
		]),
	],
	'agent-administrative' => [
		'name' => 'Agent Administrative',
		'is_percentage' => 0,
		'value' => 100,
		'data' => Json::encode([
			ProvisionType::KEY_DATA_ISSUE_USER_TYPE => IssueUser::TYPE_AGENT,
			ProvisionType::KEY_DATA_WITH_HIERARCHY => false,
			ProvisionType::KEY_DATA_CALCULATION_TYPES => [
				IssuePayCalculation::TYPE_ADMINISTRATIVE,
			],
		]),
	],

];
