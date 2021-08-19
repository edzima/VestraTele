<?php

use common\helpers\EmailTemplateKeyHelper;

return [
	[
		'id' => 1,
		'key' => 'settlement.create.honorarium.customer.issueTypes(1)',
	],
	[
		'id' => 2,
		'key' => 'settlement.create.honorarium.worker.issueTypes(1)',
	],
	[
		'id' => 3,
		'key' => EmailTemplateKeyHelper::generateKey([
			EmailTemplateKeyHelper::SETTLEMENT_PAY_PAID,
			EmailTemplateKeyHelper::CUSTOMER,
			EmailTemplateKeyHelper::issueTypesKeyPart([1]),
		]),
	],
	[
		'id' => 4,
		'key' => EmailTemplateKeyHelper::generateKey([
			EmailTemplateKeyHelper::SETTLEMENT_PAY_PAID,
			EmailTemplateKeyHelper::WORKER,
			EmailTemplateKeyHelper::issueTypesKeyPart([1]),
		]),
	],
];
