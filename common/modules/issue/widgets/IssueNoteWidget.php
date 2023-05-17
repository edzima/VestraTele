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
	protected const CLASS_SETTLEMENT = 'panel-success';
	protected const CLASS_STAGE_CHANGE = 'panel-warning';
	protected const CLASS_SMS = 'panel-info';
	protected const CLASS_DEFAULT = 'panel-primary';
	protected const CLASS_SELF = 'panel-default';

	protected const CLASS_BASE = 'panel';
	protected const CLASS_COLLAPSE = 'collapse';
	protected const CLASS_PROVISION_CONTROL = 'panel-warning';
	protected const CLASS_USER_FRONTEND = self::CLASS_DEFAULT;

	public ?int $compareIssueId = null;

	public IssueNote $model;
	public ?bool $editBtn = null;
	public ?bool $removeBtn = null;

	public array $collapseTypes = [];
	public array $options = [];

	public static function getTypeKindClass(string $type = null): string {
		if ($type) {
			return 'type-' . str_replace(['.'], ['_'], $type);
		}
		return 'without-type';
	}

	public function init() {
		parent::init();
		if ($this->removeBtn === null) {
			$this->removeBtn = Yii::$app->user->canDeleteNote($this->model);
		}
		if ($this->editBtn === null) {
			if ($this->model->isSms()) {
				$this->editBtn = false;
			} else {
				$this->editBtn = Yii::$app->user->getId() === $this->model->user_id || Yii::$app->user->can(User::PERMISSION_NOTE_UPDATE);
			}
		}
		$this->options['id'] = 'issue-note-'.$this->model->id;
		$this->ensureHtmlOptions();
	}

	private function ensureHtmlOptions(): void {
		Html::addCssClass($this->options, static::CLASS_BASE);
		Html::addCssClass($this->options, $this->getPanelClass());
		$type = $this->model->getTypeKind();
		if ($type) {
			Html::addCssClass($this->options, $type);
		}
		foreach ($this->collapseTypes as $type) {
			if ($this->model->isType($type)) {
				Html::addCssClass($this->options, static::CLASS_COLLAPSE);
			}
		}
	}

	private function getPanelClass(): string {
		if ($this->model->isPinned()) {
			return static::CLASS_PINNED;
		}
		if ($this->model->isUserFrontend()) {
			return static::CLASS_USER_FRONTEND;
		}
		if ($this->model->isSelf()) {
			return static::CLASS_SELF;
		}
		if ($this->model->isSms()) {
			return static::CLASS_SMS;
		}
		if ($this->model->isForSettlement()) {
			if ($this->model->isForSettlementProvisionControl()) {
				return static::CLASS_PROVISION_CONTROL;
			}
			return static::CLASS_SETTLEMENT;
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
		if (!$this->shouldRender()) {
			return '';
		}
		return $this->render('issue-note', [
			'model' => $this->model,
			'compareIssueId' => $this->compareIssueId,
			'options' => $this->options,
			'editBtn' => $this->editBtn,
			'removeBtn' => $this->removeBtn,
		]);
	}

	public function shouldRender(): bool {
		if ($this->model->isSelf()) {
			return Yii::$app->user->getId() === $this->model->user_id;
		}
		if ($this->model->isForSettlementProvisionControl()) {
			return Yii::$app->user->can(User::PERMISSION_PROVISION);
		}

		return true;
	}

}
