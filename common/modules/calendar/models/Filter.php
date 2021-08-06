<?php

namespace common\modules\calendar\models;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\BaseObject;

/**
 *
 * @property-read array $itemOptions
 */
class Filter extends BaseObject implements Arrayable {

	use ArrayableTrait;

	public string $id;
	public string $label;
	public FilterOptions $options;

	public bool $isActive = true;

	public function getItemOptions(): array {
		return $this->options->toArray();
	}

	public function fields(): array {
		return [
			'id',
			'label',
			'isActive',
			'itemOptions',
		];
	}

}
