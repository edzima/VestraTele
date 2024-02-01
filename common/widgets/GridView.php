<?php

namespace common\widgets;

use common\widgets\grid\DataColumn;
use kartik\grid\GridView as BaseGridView;

class GridView extends BaseGridView {

	public $pageSummaryPosition = self::POS_TOP;

	public $dataColumnClass = DataColumn::class;

	public bool $filterAllowedEmpty = true;

	protected function getClientOptions() {
		$options = parent::getClientOptions();
		$options['filterAllowedEmpty'] = $this->filterAllowedEmpty;
		return $options;
	}

}
