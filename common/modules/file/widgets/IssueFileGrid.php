<?php

namespace common\modules\file\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\user\Worker;
use common\modules\file\models\IssueFile;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\DataColumn;
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

	public bool $groupByType = true;

	private array $typeModels = [];
	private array $renderTypes = [];

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

		if ($this->groupByType) {
			$this->typeModels = [];
			foreach ($this->dataProvider->getModels() as $model) {
				$this->typeModels[$model->file->file_type_id][] = $model;
			}
		}

		parent::init();
	}

	public function renderTableRow($model, $key, $index): string {
		if (!$this->groupByType || !isset($this->renderTypes[$model->file->file_type_id])) {
			$this->renderTypes[$model->file->file_type_id] = $model->file->file_type_id;
			return parent::renderTableRow($model, $key, $index);
		}
		return '';
	}

	public function defaultColumns(): array {
		return [
			[
				'noWrap' => true,
				'contentBold' => true,
				'value' => function (IssueFile $model) {
					return Html::a($model->file->fileType->name,
						[
							'/file/issue/upload',
							'issue_id' => $model->issue_id,
							'file_type_id' => $model->file->file_type_id,
						]);
				},
				'format' => 'html',
			],
			[
				'class' => 'kartik\grid\ExpandRowColumn',
				'width' => '50px',
				'value' => function () {
					return GridView::ROW_COLLAPSED;
				},
				'detail' => function (IssueFile $model) {
					$models = $this->typeModels[$model->file->file_type_id] ?? [];
					$content = [];
					foreach ($models as $typeModel) {
						$content[] = $this->renderFileLink($typeModel);
					}
					return implode('; ', $content);
				},
				'headerOptions' => ['class' => 'kartik-sheet-style'],
				'expandOneOnly' => true,
				'enableRowClick' => true,
				'visible' => $this->groupByType,
			],
			[
				'class' => DataColumn::class,
				'label' => Yii::t('file', 'File'),
				'options' => [
					'style' => 'width:70%',
				],
				'value' => function (IssueFile $issueFile): string {
					return $this->renderFileLink($issueFile);
				},
				'format' => 'html',
				'visible' => !$this->groupByType,
			],
			//	'details',

			//	'file.owner',
			[
				'class' => ActionColumn::class,
				'controller' => '/file/issue',
				'visibleButtons' => [
					'update' => false,
					'view' => function (IssueFile $model): string {
						return $model->file->owner_id === (Yii::$app->user->getId()) || Yii::$app->user->can(Worker::ROLE_ISSUE_FILE_MANAGER);
					},
					'delete' => function (IssueFile $model): string {
						return $model->file->owner_id === (Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_ISSUE_FILE_DELETE_NOT_SELF);
					},
				],
				'visible' => !$this->groupByType,
			],
		];
	}

	protected function renderFileLink(IssueFile $issueFile): string {
		return Html::issueFileLink($issueFile->file, $issueFile->issue_id);
	}

	public function issueDataProvider(): DataProviderInterface {
		return new ActiveDataProvider([
			'query' => $this->model
				->getIssueModel()
				->getIssueFiles(),
		]);
	}
}
