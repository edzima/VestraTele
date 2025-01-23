<?php

namespace common\modules\court\modules\spi\models\lawsuit;

interface LawsuitInterface {

	public function getId(): int;

	public function getSignature(): string;

	public function getNumber(): int;

	public function getYear(): int;

	public function getCourtName(): string;

	public function isVisible(): bool;

}
