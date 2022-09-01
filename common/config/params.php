<?php

return [
	'adminEmail' => $_ENV['EMAIL_ADMIN'],
	'supportEmail' => $_ENV['EMAIL_SUPPORT'],
	'senderEmail' => $_ENV['EMAIL_SENDER'],
	'senderName' => 'Vestra CRM mailer',
	'user.passwordResetTokenExpire' => 3600,
	'user.passwordMinLength' => 8,
	'phoneInput.preferredCountries' => [
		'pl',
		'ua',
	],
];
