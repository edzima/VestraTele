<?php

return [
	[
		'templateId' => 1,
		'subject' => 'Sms About Payed Pay(TYPE_1) for Customer.',
		'body' => 'Pay Payed: {payValue}. Your Agent: {agentName} - {agentPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 2,
		'subject' => 'Sms About Payed Pay(TYPE_2) for Customer.',
		'body' => 'Pay Payed: {payValue}. Your Agent: {agentName} - {agentPhone}',
		'language' => 'en-US',
	],
	[
		'templateId' => 3,
		'subject' => 'Email. About Payed Pay Issue(TYPE_1 and TYPE_2) for Customer.',
		'body' => 'Pay Payed: {payValue}. Your Agent: {agentName} - {agentPhone}. Details: {settlementLink}',
		'language' => 'en-US',
	],
	[
		'templateId' => 4,
		'subject' => 'Sms. Pay Payed: {payValue} (TYPE_1) for Worker.',
		'body' => 'Pay Payed: {payValue}. Customer: {customerName} - {customerPhone}. Details: {settlementLink}',
		'language' => 'en-US',
	],
	[
		'templateId' => 5,
		'subject' => 'Email. Pay Payed: {payValue} {issue} (All types) for Worker.',
		'body' => 'Pay Payed: {payValue}. Customer: {customerName} - {customerPhone}. Details: {settlementLink}',
		'language' => 'en-US',
	],
	[
		'templateId' => 6,
		'subject' => 'Sms About Not Full Payed Pay(TYPE_1) for Customer.',
		'body' => 'Part Pay Payed: {payValue}. Your Agent: {agentName} - {agentPhone}',
		'language' => 'en-US',
	],
];
