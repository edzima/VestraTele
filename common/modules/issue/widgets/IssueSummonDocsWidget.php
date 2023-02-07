<?php

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\SummonDoc;
use common\widgets\GridView;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class IssueSummonDocsWidget extends GridView {

	public ?IssueInterface $issue = null;
	public $summary = false;

	public function init() {
		if (empty($this->caption)) {
			$this->caption = \Yii::t('issue', 'Summon Docs');
		}
		if (empty($this->issue) && empty($this->dataProvider)) {
			throw new InvalidConfigException('$issue or $dataProvider must be set.');
		}
		if (empty($this->dataProvider)) {
			$this->dataProvider = $this->getDataProviderFromIssue();
		}
		if (empty($this->columns)) {
			$this->columns = $this->getDefaultColumns();
		}
		parent::init();
	}

	private function getDataProviderFromIssue(): DataProviderInterface {
		$documents = [];
		foreach ($this->issue->getIssueModel()->summons as $summon) {
			foreach ($summon->docs as $doc) {
				$documents[$doc->id] = $doc;
			}
		}

		return new ArrayDataProvider([
			'allModels' => $documents,
			'modelClass' => SummonDoc::class,
		]);
	}

	private function getDefaultColumns(): array {
		return [
			'name',
			'date_at:date',
			//			'user',
			[
				'class' => \common\widgets\grid\ActionColumn::class,
				'buttons' => [
					'check' => function (string $url, SummonDoc $data): string {
						return Html::a(Html::icon('check'),
							['/issue/summon-doc/done', 'id' => $data->id]
						);
					},
					'uncheck' => function (string $url, SummonDoc $data): string {
						return Html::a(Html::icon('remove'),
							['summon/not', 'id' => $data->id]
						);
					},
				],
				'template' => '{check} {uncheck}',
			],
		];
	}

}
