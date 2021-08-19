<?php

namespace common\models\settlement;

interface TransferType {

	public const TRANSFER_TYPE_CASH = 'cash';
	public const TRANSFER_TYPE_BANK = 'bank-transfer';

	public function getTransferType(): ?string;

	public static function getTransfersTypesNames(): array;
}
