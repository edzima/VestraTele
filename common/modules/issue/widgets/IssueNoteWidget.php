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

	protected const CLASS_PINNED = 'panel-danger';
	protected const CLASS_SETTLEMENT = 'panel-settlement';
	protected const CLASS_DEFAULT = 'panel-primary';

	public IssueNote $model;
	public bool $removeBtn = true;

	public array $options = [
		'class' => 'panel',
	];

	public function init() {
		parent::init();
		$this->ensureHtmlOptions();
	}

	private function ensureHtmlOptions(): void {
		if ($this->model->isPinned()) {
			Html::addCssClass($this->options, static::CLASS_PINNED);
		} else {
			Html::addCssClass($this->options, $this->model->isForSettlement()
				? static::CLASS_SETTLEMENT
				: static::CLASS_DEFAULT
			);
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
