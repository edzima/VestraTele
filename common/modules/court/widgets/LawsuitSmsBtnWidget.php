<?php

namespace common\modules\court\widgets;

use common\components\message\MessageTemplate;
use common\models\issue\IssueInterface;
use common\models\message\IssueMessagesForm;
use common\models\user\User;
use common\modules\court\models\Lawsuit;
use common\widgets\ButtonDropdown;
use Yii;

class LawsuitSmsBtnWidget extends ButtonDropdown {

	public Lawsuit $model;
	public IssueInterface $issue;
	public ?User $user = null;

	public $tagName = 'a';

	public $options = [
		'class' => 'btn btn-primary',
	];
	public $split = false;
	public ?array $templates = null;

	public function defaultItems(): array {
		return $this->templateItems();
	}

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('lead', 'Send SMS');
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
	}

	public function run(): string {
		if (empty($this->dropdown['items'])) {
			return '';
		}
		return parent::run();
	}

	public function templateItems(): array {
		$items = [];
		foreach ($this->getTemplates() as $key => $template) {
			$meesage = static::getSmsMessage($this->model, $template);
			$items[] = [
				'label' => $template->getSubject(),
				'url' => ['/court/lawsuit/sms-template', 'key' => $key, 'lawsuitId' => $this->model->id, 'issueId' => $this->issue->getIssueId()],
				'options' => [
					'title' => $meesage,
				],
				'linkOptions' => [
					'data-method' => 'POST',
					'data-confirm' => Yii::t('court', 'Are you sure you want to send SMS: {message}?', [
						'message' => $meesage,
					]),
				],
			];
		}
		return $items;
	}

	public static function getSmsMessage(Lawsuit $lawsuit, MessageTemplate $template): string {
		$template->parseBody([
			'courtName' => $lawsuit->court->name,
			'dueDateAt' => Yii::$app->formatter->asDate($lawsuit->due_at),
			'dueTimeAt' => Yii::$app->formatter->asTime($lawsuit->due_at, 'short'),
			'roomNr' => $lawsuit->room,
		]);
		return $template->getSmsMessage();
	}

	protected function getTemplates(): array {
		if ($this->templates === null) {
			$templates = (array) Yii::$app->messageTemplate->getTemplatesLikeKey('lawsuit.sms');

			$this->templates = array_filter(
				$templates,
				function (MessageTemplate $messageTemplate, string $key): bool {
					return IssueMessagesForm::isForIssueType($key, $this->issue->getIssueTypeId());
				},
				ARRAY_FILTER_USE_BOTH
			);
			if (empty($this->templates)) {
				Yii::warning('Not found Lawsuit SMS Template for Issue Type ID: ' . $this->issue->getIssueTypeId());
			}
		}
		return $this->templates;
	}
}
