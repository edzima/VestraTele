<?php

namespace common\helpers;

use yii\helpers\BaseFileHelper;

class FileHelper extends BaseFileHelper {

	public static function getMimeTypeFromExtension(string $extension, $magicFile = null) {
		$mimeTypes = static::loadMimeTypes($magicFile);
		$ext = strtolower($extension);
		$ext = strtolower($ext);
		if (isset($mimeTypes[$ext])) {
			return $mimeTypes[$ext];
		}
		return null;
	}
}
