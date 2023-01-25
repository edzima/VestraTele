<?php

namespace backend\modules\issue\models;

use common\models\issue\event\IssueUserEvent;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssuePayCalculation;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

class IssueUserChangeHandler extends BaseObject {

	public int $user_id;
	private IssueUserEvent $event;

	public function __construct(IssueUserEvent $event, $config = []) {
		$this->event = $event;
		parent::__construct($config);
	}

	public function parse(): void {
		$this->parseSettlements();
	}

	protected function parseSettlements(): int {
		$count = 0;
		foreach ($this->getSettlements() as $settlement) {
			if ($this->parseSettlement($settlement)) {
				$count++;
			}
		}
		if ($count > 0) {
			IssuePayCalculation::getProvisionControlSettlementCount(true);
		}
		return $count;
	}

	/**
	 * @return IssuePayCalculation[]
	 */
	protected function getSettlements(): array {
		return $this->getIssue()->payCalculations;
	}

	protected function getIssue(): Issue {
		if ($this->event->sender instanceof IssueInterface) {
			return $this->event->sender->getIssueModel();
		}
		return $this->event->model->getIssueModel();
	}

	protected function parseSettlement(IssuePayCalculation $settlement): bool {
		if (!$this->beforeParseSettlement($settlement)) {
			return false;
		}
		$settlement->markAsProvisionControl();
		$settlement->save(false);
		$this->afterParseSettlement($settlement);
		return true;
	}

	protected function beforeParseSettlement(IssuePayCalculation $settlement): bool {
		return !$settlement->isProvisionControl() || $settlement->hasProvisions();
	}

	protected function afterParseSettlement(IssuePayCalculation $settlement): void {
		$this->sendEmailAboutSettlementWithProvisions($settlement);
		$this->saveNote($settlement);
	}

	public function sendEmailAboutSettlementWithProvisions(IssuePayCalculation $settlement): bool {
		$subject = Yii::t('issue', 'Change User in Issue: {issue}.', [
				'issue' => $this->getIssue()->getIssueName(),
			]) . ' ' . $this->getTitle();
		return Yii::$app
			->mailer
			->compose(
				['html' => 'issueUserChangeForSettlementWithProvisions-html', 'text' => 'issueUserChangeForSettlementWithProvisions-text'],
				[
					'title' => $subject,
					'issue' => $this->getIssue(),
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo(Yii::$app->params['provisionEmail'])
			->setSubject($subject)
			->send();
	}

	public function getTitle(): string {
		switch ($this->event->name) {
			case IssueUserEvent::EVENT_AFTER_LINK_USER_CREATE:
				return Yii::t('issue', 'Add {user} as {type}.', [
					'user' => $this->event->model->user->getFullName(),
					'type' => $this->event->model->getTypeName(),
				]);
			case IssueUserEvent::EVENT_AFTER_LINK_USER_UPDATE:
				return Yii::t('issue', 'Update {user} as {type}.', [
					'user' => $this->event->model->user->getFullName(),
					'type' => $this->event->model->getTypeName(),

				]);
			case IssueUserEvent::EVENT_UNLINK_USER:
				return Yii::t('issue', 'Delete {user} as {type}.', [
					'user' => $this->event->model->user->getFullName(),
					'type' => $this->event->model->getTypeName(),
				]);
		}
		throw new InvalidConfigException('Invalid $name');
	}

	public function saveNote(IssuePayCalculation $settlement): bool {
		$note = IssueNoteForm::createSettlementProvisionControl($settlement);
		$note->user_id = $this->user_id;
		$note->title = $this->getTitle();
		return $note->save();
	}
}
