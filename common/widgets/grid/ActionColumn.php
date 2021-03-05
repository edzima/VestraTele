<?php

namespace common\widgets\grid;

use kartik\grid\ActionColumn as BaseActionColumn;

class ActionColumn extends BaseActionColumn {

	public $header = '';

	public $contentOptions = [
		'class' => 'action-column',
	];
}
