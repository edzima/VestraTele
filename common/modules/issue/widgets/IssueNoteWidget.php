<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:05
 */

namespace common\modules\issue\widgets;

use yii\base\Widget;

class IssueNoteWidget extends Widget {

	public $model;

	public function run() {
		return $this->render('issue-note', ['model' => $this->model]);
	}

}