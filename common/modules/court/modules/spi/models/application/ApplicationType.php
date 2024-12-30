<?php

namespace common\modules\court\modules\spi\models\application;

interface ApplicationType {

	public const APPLICATION_TYPE_AUTOTRANSCRIPTIONS = 'AUTOTRANSCRIPTION';
	public const APPLICATION_TYPE_EPROTOCOL = 'EPROTOCOL';
	public const APPLICATION_TYPE_LAWSUIT = 'LAWSUIT';
	public const APPLICATION_TYPE_NONRESIDENT = 'NONRESIDENT';

	public function getApplicationType(): string;
}
