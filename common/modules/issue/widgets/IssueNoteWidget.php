<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:05
 */

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\models\issue\IssueNote;
use yii\base\Widget;

class IssueNoteWidget extends Widget {

	public IssueNote $model;
	public bool $removeBtn = true;

	public array $options = [
		'class' => 'panel',
	];

	public function init() {
		parent::init();
		if ($this->model->isPinned()) {
			Html::addCssClass($this->options, 'panel-warning');
		} else {
			Html::addCssClass($this->options, $this->model->isForSettlement() ? 'panel-success' : 'panel-primary');
		}
	}

	public function run(): string {
		return $this->render('issue-note', [
			'model' => $this->model,
			'options' => $this->options,
			'removeBtn' => $this->removeBtn,
		]);
	}

}
