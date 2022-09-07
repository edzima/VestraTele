<?php

namespace common\modules\issue\widgets;

use backend\helpers\Url;
use common\models\issue\IssueInterface;
use Yii;
use yii\bootstrap\ButtonDropdown;

class IssueSmsButtonDropdown extends ButtonDropdown {

	public string $route;
	public $options = [
		'class' => 'btn-default',
	];
	public $tagName = 'a';
	public $split = true;
	public IssueInterface $model;

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('common', 'Send SMS');
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		if (!isset($this->options['href'])) {
			$this->options['href'] = [
				$this->route,
				'id' => $this->model->getIssueId(),
			];
		}
	}

	public function defaultItems(): array {
		$items = [];
		foreach ($this->model->getIssueModel()->users as $issueUser) {
			if ($issueUser->user->profile->hasPhones()) {
				$items[] = [
					'label' => $issueUser->getTypeWithUser(),
					'linkOptions' => [
						'data-pjax' => 0,
					],
					'url' => Url::to([
						$this->route,
						'id' => $this->model->getIssueId(),
						'userType' => $issueUser->type,
					]),
				];
			}
		}
		return $items;
	}

}
