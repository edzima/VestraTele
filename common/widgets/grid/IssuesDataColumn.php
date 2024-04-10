<?php

namespace common\widgets\grid;

use Closure;
use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\IssueInterface;
use Yii;

class IssuesDataColumn extends DataColumn {

	public string $separator = "<br>";
	public bool $asLink = true;
	public bool $customerLastname = true;

	public $format = 'raw';
	public $issuesAttribute = 'issues';

	public $noWrap = true;

	public ?Closure $issueValue = null;

	public function init() {
		parent::init();

		if (empty($this->value)) {
			$this->value = function ($model) {
				return $this->defaultValue($model);
			};
		}
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Issues');
		}
	}

	protected function defaultValue($model): string {
		$issues = $model->{$this->issuesAttribute};
		$content = [];
		foreach ($issues as $issue) {
			$content[] = $this->issueValue($issue);
		}
		return implode($this->separator, $content);
	}

	protected function issueValue(IssueInterface $issue) {
		if ($this->issueValue) {
			return call_user_func($this->issueValue, $issue);
		}

		if ($this->asLink) {
			return Html::a($issue->getIssueName(), Url::issueView($issue->getIssueId()));
		}
		return $issue->getIssueName();
	}

}
