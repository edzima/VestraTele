<?php

namespace common\fixtures\file;

use common\fixtures\UserFixture;
use common\modules\file\models\FileAccess;
use yii\test\ActiveFixture;

class FileAccessFixture extends ActiveFixture {

	public $modelClass = FileAccess::class;

	public $depends = [
		UserFixture::class,
		FileFixture::class,
	];
}
