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

	public function init() {
		if ($this->contentBold) {
			Html::addCssStyle($this->contentOptions, 'font-weight:bold');
		}
		if ($this->contentCenter) {
			Html::addCssClass($this->contentOptions, 'text-center');
		}
		if ($this->ellipsis) {
			Html::addCssClass($this->contentOptions, 'ellipsis');
		}
		parent::init();
	}
}
