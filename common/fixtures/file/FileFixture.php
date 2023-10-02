<?php

namespace common\fixtures\file;

use common\fixtures\UserFixture;
use common\modules\file\models\File;
use yii\test\ActiveFixture;

class FileFixture extends ActiveFixture {

	public $modelClass = File::class;

	public $depends = [
		UserFixture::class,
		FileTypeFixture::class,
	];
}
