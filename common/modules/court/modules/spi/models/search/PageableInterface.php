<?php

namespace common\modules\court\modules\spi\models\search;

interface PageableInterface {

	public function getPage(): int;

	public function getSize(): int;

}
