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
use common\models\user\User;
use Yii;
use yii\base\Widget;

class IssueNoteWidget extends Widget {

	protected const CLASS_PINNED = 'panel-danger';
	protected const CLASS_SETTLEMENT = 'panel-settlement';
	protected const CLASS_STAGE_CHANGE = 'panel-warning';
	protected const CLASS_SMS = 'panel-info';
	protected const CLASS_DEFAULT = 'panel-primary';

	public IssueNote $model;
	public ?bool $editBtn = null;
	public ?bool $removeBtn = null;

	public array $options = [
		'class' => 'panel',
	];

	public function init() {
		parent::init();
		if ($this->removeBtn === null) {
			$this->removeBtn = Yii::$app->user->canDeleteNote($this->model);
		}
		if ($this->editBtn === null) {
			if ($this->model->isSms()) {
				$this->editBtn = Yii::$app->user->can(User::ROLE_ADMINISTRATOR);
			} else {
				$this->editBtn = Yii::$app->user->getId() === $this->model->user_id || Yii::$app->user->can(User::PERMISSION_NOTE_UPDATE);
			}
		}
		$this->ensureHtmlOptions();
	}

	private function ensureHtmlOptions(): void {
		Html::addCssClass($this->options, $this->getPanelClass());
	}

	private function getPanelClass(): string {
		if ($this->model->isPinned()) {
			return static::CLASS_PINNED;
		}
		if ($this->model->isSms()) {
			return static::CLASS_SMS;
		}
		if ($this->model->isForSettlement()) {
			return static::CLASS_SETTLEMENT;
		}
		if ($this->model->isForStageChange()) {
			return static::CLASS_STAGE_CHANGE;
		}
		return static::CLASS_DEFAULT;
	}

	public function run(): string {
		return $this->render('issue-note', [
			'model' => $this->model,
			'options' => $this->options,
			'editBtn' => $this->editBtn,
			'removeBtn' => $this->removeBtn,
		]);
	}

}
