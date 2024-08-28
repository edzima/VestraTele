<?php

namespace common\modules\lead\models;

interface LeadDealStage {

	public const DEAL_STAGE_QUALIFIED = 50;
	public const DEAL_STAGE_CONTRACT_SENT = 75;
	public const DEAL_STAGE_CLOSED_WON = 100;
	public const DEAL_STAGE_CLOSED_LOST = 200;

	public function getDealStage(): ?int;

	public function dealStagesNames(): array;
}
