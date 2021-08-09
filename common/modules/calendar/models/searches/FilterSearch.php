<?php

namespace common\modules\calendar\models\searches;

use common\modules\calendar\models\Filter;

interface FilterSearch {

	/**
	 * @return Filter[]
	 */
	public function getFilters(): array;

}
