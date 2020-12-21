<?php

namespace common\widgets\grid;

class CurrencyColumn extends DataColumn {

	public $noWrap = true;
	public bool $contentBold = true;

	public $attribute = 'value';
	public $format = 'currency';
	public $width = '100px';

}
