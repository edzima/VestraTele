<?php

namespace common\modules\file\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\modules\file\models\File;
use common\modules\file\models\FileType;
use common\widgets\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class IssueFileGrid extends GridView {

	public ?IssueInterface $model = null;

	public $summary = false;

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
		$this->rowOptions = static function (IssueFileTypes $model): array {
			$options = [];
			if (empty($model->files)) {
				Html::addCssClass($options, 'warning');
			}
			return $options;
		};

		parent::init();
	}

	public function defaultColumns(): array {
		return [

			[
				'noWrap' => true,
				'contentBold' => true,
				'value' => function (IssueFileTypes $model) {
					return Html::a($model->type->name,
						[
							'/file/issue/upload',
							'issue_id' => $model->model->getIssueId(),
							'file_type_id' => $model->type->id,
						]);
				},
				'format' => 'html',
			],

			[
				'label' => Yii::t('issue', 'Files Count'),
				'width' => '60px',
				'noWrap' => true,
				'contentBold' => true,
				'contentCenter' => true,
				'value' => function (IssueFileTypes $model) {
					return count($model->files);
				},
			],
			[
				'class' => 'kartik\grid\ExpandRowColumn',
				'width' => '50px',
				'value' => function (IssueFileTypes $model) {
					if (!empty($model->files)) {
						return GridView::ROW_COLLAPSED;
					}
					return '';
				},
				'detail' => function (IssueFileTypes $model) {
					$files = $model->files;
					$content = [];
					foreach ($files as $file) {
						$content[] = $this->renderFileLink($file);
					}
					return Html::ul($content, [
						'encode' => false,
					]);
				},
				'headerOptions' => ['class' => 'kartik-sheet-style'],
				'expandOneOnly' => true,
				'enableRowClick' => true,
			],
		];
	}

	protected function renderFileLink(File $file): string {
		return Html::issueFileLink($file, $this->model->getIssueId());
	}

	public function issueDataProvider(): DataProviderInterface {
		$arrayDataProvider = new ArrayDataProvider([]);
		$types = FileType::getTypes(true);
		if (empty($types)) {
			return $arrayDataProvider;
		}
		$models = $this->model
			->getIssueModel()
			->getIssueFiles()
			->all();

		$issueFileTypes = [];
		foreach ($types as $type) {
			$fileTypes = new IssueFileTypes();
			$fileTypes->type = $type;
			$fileTypes->model = $this->model;
			foreach ($models as $model) {
				if ($model->file->file_type_id === $type->id) {
					$fileTypes->files[] = $model->file;
				}
			}
			$issueFileTypes[] = $fileTypes;
		}
		$arrayDataProvider->allModels = $issueFileTypes;
		return $arrayDataProvider;
	}
}
