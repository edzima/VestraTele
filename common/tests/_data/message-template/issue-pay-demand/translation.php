<?php

return [
	[
		'templateId' => 1,
		'subject' => 'Sms About First Demand Pay(TYPE_1) for Customer.',
		'body' => 'TYPE_1. Not Payed: {payValue} in Issue: {issueType} Deadline: {deadlineAt} Your Agent: {agentName} - {agentPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 2,
		'subject' => 'Sms About First Demand Pay(TYPE_2/TYPE_3) for Customer.',
		'body' => 'TYPE_2/TYPE_3. Not Payed: {payValue}. Deadline: {deadlineAt} Your Agent: {agentName} - {agentPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 3,
		'subject' => 'Email. About First Demand Pay Issue(TYPE_1 and TYPE_2) for Customer.',
		'body' => 'Not Payed: {payValue}. Deadline: {deadlineAt} . Your Agent: {agentName} - {agentPhone}. Details: {settlementLink}',
		'language' => 'en-US',
	],
	[
		'templateId' => 4,
		'subject' => 'Email. About First Demand Payed: {payValue} {issue} (All types) for Worker.',
		'body' => 'Not Payed: {payValue} since {deadlineDays} days Customer: {customerName} - {customerPhone}. Details: {settlementLink}',
		'language' => 'en-US',
	],
	[
		'templateId' => 5,
		'subject' => 'Sms About Not Full Payed Pay(TYPE_1) for Workers.',
		'body' => 'Not Payed: {payValue} since {deadlineDays} days. Customer: {customerName} - {customerPhone}. Details: {settlementLink}',
		'language' => 'en-US',
	],
];
