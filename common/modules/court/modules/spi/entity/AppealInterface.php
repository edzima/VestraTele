<?php

namespace common\modules\court\modules\spi\entity;

interface AppealInterface {

	public const APPEAL_BIALYSTOK = 'bialystok';
	public const APPEAL_GDANSK = 'gdansk';
	public const APPEAL_KATOWICE = 'katowice';
	public const APPEAL_KRAKOW = 'krakow';
	public const APPEAL_LUBLIN = 'lublin';
	public const APPEAL_LODZ = 'lodz';
	public const APPEAL_POZNAN = 'poznan';
	public const APPEAL_RZESZOW = 'rzeszow';
	public const APPEAL_SZCZECIN = 'szczecin';
	public const APPEAL_WARSZAWA = 'warszawa';
	public const APPEAL_WROCLAW = 'wroclaw';

	public function getAppeal(): string;
}
