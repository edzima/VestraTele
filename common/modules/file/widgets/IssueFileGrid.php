<?php

namespace common\modules\file\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\modules\file\models\IssueFile;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class IssueFileGrid extends GridView {

	public ?IssueInterface $model = null;

	public $summary = false;

	public $emptyText = '';
	public $showOnEmpty = false;

	public function init(): void {
		if (empty($this->caption)) {
			$this->caption = Yii::t('issue', 'Files');
		}
		if ($this->model === null && $this->dataProvider === null) {
			throw new InvalidConfigException('$model or $dataProvider must be set.');
		}
		if ($this->dataProvider === null) {
			$this->dataProvider = $this->issueDataProvider();
		}
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	public function defaultColumns(): array {
		return [
			[
				'label' => Yii::t('file', 'File'),
				'value' => function (IssueFile $issueFile): string {
					$name = Html::encode($issueFile->file->getNameWithType());
					if ($issueFile->file->isForUser(Yii::$app->user->getId())) {
						return Html::a(
							$name, [
								'/file/issue/download',
								'issue_id' => $issueFile->issue_id,
								'file_id' => $issueFile->file_id,
							]
						);
					}
					return $name;
				},
				'format' => 'html',
			],
			//	'details',
			'file.typeName',
			'file.owner',
			[
				'class' => ActionColumn::class,
				'controller' => '/file/issue',
			],
		];
	}

	public function issueDataProvider(): DataProviderInterface {
		return new ActiveDataProvider([
			'query' => $this->model
				->getIssueModel()
				->getIssueFiles(),
		]);
	}
}
