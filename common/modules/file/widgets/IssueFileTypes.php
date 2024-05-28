<?php

namespace common\modules\file\widgets;

use common\models\issue\IssueInterface;
use common\modules\file\models\FileType;

class IssueFileTypes {

	public IssueInterface $model;
	public FileType $type;
	public array $files = [];
}
