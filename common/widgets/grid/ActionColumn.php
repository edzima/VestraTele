<?php

namespace common\widgets\grid;

use common\helpers\Html;
use kartik\grid\ActionColumn as BaseActionColumn;

class ActionColumn extends BaseActionColumn {

	public $contentOptions = [
		'class' => 'action-column',
	];

	public $header = '';

	public bool $noPrint = true;

	public function init() {
		parent::init();
		if ($this->noPrint) {
			Html::addNoPrintClass($this->headerOptions);
			Html::addNoPrintClass($this->contentOptions);
			Html::addNoPrintClass($this->footerOptions);
		}
	}

}
