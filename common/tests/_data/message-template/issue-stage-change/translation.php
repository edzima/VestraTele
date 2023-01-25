<?php

return [
	[
		'templateId' => 1,
		'subject' => 'Global Email to Workers about Issue: {issue} new Stage: {stage} from Previous: {previousStage}',
		'body' => 'In Issue: {issue} change stage from: {previousStage} to {stage}. Customer: {customerName} - {customerPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 2,
		'subject' => 'SMS to Customer for Day Reminder',
		'body' => 'In Issue: {issue} change stage from: {previousStage} to {stage}. Customer: {customerName} - {customerPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 3,
		'subject' => 'Dedicated Email Subject for Stage: Completing Documents to Workers about Issue: {issue} only to Completing documents stage',
		'body' => 'Change Stage to Completing Documents. Customer: {customerName} - {customerPhone}',
		'language' => 'en-US',
	],
];
