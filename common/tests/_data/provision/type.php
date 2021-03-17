<?php

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use yii\helpers\Json;

return [
	'agent-percent-25' => [
		'id' => ProvisionFixtureHelper::TYPE_AGENT_PERCENT_25,
		'name' => 'Agent Honorarium',
		'is_percentage' => 1,
		'is_active' => 1,
		'value' => 25,
		'data' => Json::encode([
			IssueProvisionType::KEY_DATA_ISSUE_USER_TYPE => IssueUser::TYPE_AGENT,
			IssueProvisionType::KEY_DATA_WITH_HIERARCHY => true,
			IssueProvisionType::KEY_DATA_CALCULATION_TYPES => [
				IssuePayCalculation::TYPE_HONORARIUM,
			],
		]),
	],
	'tele-percent-5' => [
		'name' => 'Tele Honorarium',
		'is_percentage' => 1,
		'is_active' => 1,
		'value' => 5,
		'data' => Json::encode([
			IssueProvisionType::KEY_DATA_ISSUE_USER_TYPE => IssueUser::TYPE_TELEMARKETER,
			IssueProvisionType::KEY_DATA_WITH_HIERARCHY => false,
			IssueProvisionType::KEY_DATA_CALCULATION_TYPES => [
				IssuePayCalculation::TYPE_HONORARIUM,
			],
		]),
	],
	'agent-administrative' => [
		'name' => 'Agent Administrative',
		'is_percentage' => 0,
		'is_active' => 1,
		'value' => 100,
		'data' => Json::encode([
			IssueProvisionType::KEY_DATA_ISSUE_USER_TYPE => IssueUser::TYPE_AGENT,
			IssueProvisionType::KEY_DATA_WITH_HIERARCHY => false,
			IssueProvisionType::KEY_DATA_CALCULATION_TYPES => [
				IssuePayCalculation::TYPE_ADMINISTRATIVE,
			],
		]),
	],

];
