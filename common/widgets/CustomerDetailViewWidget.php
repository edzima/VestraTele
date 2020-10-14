<?php

namespace common\widgets;

use yii\widgets\DetailView;

class CustomerDetailViewWidget extends DetailView {

	public $attributes = [
		'fullName',
		'email:email',
		'phone',
	];

}
