<?php

namespace common\modules\court\modules\spi\models\court;

use yii\base\Model;

class Court extends Model {

	public int $id;
	public string $name;
	public string $address1;
	public string $address2;
	public int $identifier;
	public ?bool $disabled;
	public bool $receiveWritings;
}
