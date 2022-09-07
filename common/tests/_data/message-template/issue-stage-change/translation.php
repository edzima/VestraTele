<?php

return [
	[
		'templateId' => 1,
		'subject' => 'Email to Workers about Issue: {issue} new Stage: {stage} from Previous: {previousStage}',
		'body' => 'In Issue: {issue} change stage from: {previousStage} to {stage}. Customer: {customerName} - {customerPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 2,
		'subject' => 'SMS to Customer for Day Reminder',
		'body' => 'In Issue: {issue} change stage from: {previousStage} to {stage}. Customer: {customerName} - {customerPhone}',
		'language' => 'en-US',
	],
];
