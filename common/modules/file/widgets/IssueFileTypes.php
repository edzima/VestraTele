<?php

namespace common\modules\file\widgets;

use common\models\issue\IssueInterface;
use common\modules\file\models\FileType;
use Yii;

class IssueFileTypes {

	public IssueInterface $model;
	public FileType $type;
	public array $files = [];

	public function isTypeForUser(int $userId) {
		return Yii::$app->fileAuth->isTypeForUser(
			$userId,
			$this->type,
			$this->model->getIssueModel()->getUserRoles($userId)
		);
	}
}
