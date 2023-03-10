<?php

namespace common\modules\lead\widgets;

use common\components\message\MessageTemplate;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\widgets\ButtonDropdown;
use Yii;

class LeadSmsBtnWidget extends ButtonDropdown {

	public ActiveLead $model;
	public ?User $user = null;

	public $tagName = 'a';

	public $options = [
		'class' => 'btn btn-success',
	];
	public $split = true;

	public function defaultItems(): array {
		return $this->templateItems();
	}

	public function init(): void {
		if ($this->user === null) {
			$this->user = Yii::$app->user->getIdentity();
		}

		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('lead', 'Send SMS');
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		if (!isset($this->options['href'])) {
			$this->options['href'] = ['/lead/sms/push', 'id' => $this->model->getId()];
		}
	}

	public function run() {
		if ($this->user === null) {
			return '';
		}
		if ($this->user->getPhone() === null) {
			$this->options['disabled'] = true;
			$this->options['title'] = Yii::t('common', '{user} has not set Phone number.', [
				'user' => $this->user->getFullName(),
			]);
		}
		return parent::run();
	}

	private function welcomeSmsItem(): array {
		return [
			'label' => Yii::t('lead', 'Send Welcome SMS'),
			'url' => ['/lead/sms/welcome', 'id' => $this->model->getId()],
		];
	}

	public function templateItems(): array {
		$items = [];
		$templates = Yii::$app->messageTemplate->getLeadTypeTemplatesLikeKey('lead.sms', $this->model->getSource()->getType()->getID());
		foreach ($templates as $key => $template) {
			$meesage = $this->getMessage($template);
			$items[] = [
				'label' => $template->getSubject(),
				'url' => ['/lead/sms/template', 'key' => $key, 'id' => $this->model->getId()],
				'options' => [
					'title' => $meesage,
				],
				'linkOptions' => [
					'data-method' => 'POST',
					'data-confirm' => Yii::t('lead', 'Are you sure you want to send SMS: {message}?', [
						'message' => $meesage,
					]),
				],
			];
		}
		return $items;
	}

	protected function getMessage(MessageTemplate $template): string {
		if ($this->user) {
			$template->parseBody([
				'userName' => $this->user->getProfile()->firstname,
				'userPhone' => Yii::$app->formatter->asTel($this->user->getPhone(), [
					'asLink' => false,
				]),
			]);
		}

		return $template->getSmsMessage();
	}
}
