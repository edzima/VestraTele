<?php

namespace common\widgets\grid;

use common\helpers\Html;
use kartik\grid\DataColumn as BaseDataColumn;

/**
 * Class DataColumn
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class DataColumn extends BaseDataColumn {

	public bool $contentBold = false;
	public bool $contentCenter = false;
	public bool $ellipsis = false;

	protected function fetchContentOptions($model, $key, $index): array {
		$options = parent::fetchContentOptions($model, $key, $index);
		if ($this->contentBold) {
			Html::addCssStyle($options, 'font-weight:bold');
		}
		if ($this->contentCenter) {
			Html::addCssClass($options, 'text-center');
		}
		if ($this->ellipsis) {
			Html::addCssClass($options, 'ellipsis');
		}
		return $options;
	}
}
