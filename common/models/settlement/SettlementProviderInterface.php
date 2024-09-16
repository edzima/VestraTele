<?php

namespace common\models\settlement;

interface SettlementProviderInterface {

	public function getProviderType(): int;

	public function getProviderId(): int;

	public function providersTypesNames(): array;
}
