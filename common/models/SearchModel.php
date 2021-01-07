<?php

namespace common\models;

use yii\data\DataProviderInterface;

interface SearchModel {

	/**
	 * @return string
	 */
	public function formName();

	public function search(array $params): DataProviderInterface;

}
