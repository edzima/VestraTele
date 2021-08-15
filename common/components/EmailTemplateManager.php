<?php

namespace common\components;

use backend\helpers\EmailTemplateKeyHelper;
use Yii;
use yii\data\ActiveDataProvider;
use ymaker\email\templates\components\TemplateManager;
use ymaker\email\templates\models\EmailTemplate;
use ymaker\email\templates\queries\EmailTemplateQuery;

class EmailTemplateManager extends TemplateManager {

	/**
	 * @param string $key
	 * @param string|null $language
	 * @return EmailTemplate[]|null indexed by Key
	 */
	public function getTemplatesLikeKey(string $key, string $language = null): ?array {
		$language = $language ?: Yii::$app->language;
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $this->repository->getDataProvider();
		$dataProvider->pagination = false;
		/** @var $query EmailTemplateQuery */
		$query = $dataProvider->query;
		$query->andWhere(['like', 'key', $key]);
		$query->withTranslation($language);
		$models = $dataProvider->getModels();
		if (empty($models)) {
			Yii::warning("Not found templates like key: $key.", 'emailTemplate');
			return null;
		}
		$templates = [];
		/** @var \ymaker\email\templates\entities\EmailTemplate[] $models */
		foreach ($models as $model) {
			$translation = $model->getTranslation($language);
			if ($translation && !$translation->isNewRecord) {
				$templates[$model->key] = EmailTemplate::buildFromEntity($translation);
			}
		}
		return $templates;
	}

	public function getIssueTypeTemplatesLikeKey(string $key, int $typeId, string $language = null): ?EmailTemplate {
		$templates = $this->getTemplatesLikeKey($key, $language);
		if (empty($templates)) {
			Yii::warning("Not found templates like key: $key for $language.", 'emailTemplate');
			return null;
		}
		foreach ($templates as $templateKey => $template) {
			if (EmailTemplateKeyHelper::isForIssueType($templateKey, $typeId)) {
				return $template;
			}
		}
		Yii::warning("Not found templates like key: $key for $language for Issue Type ID: $typeId.", 'emailTemplate');

		return null;
	}
}
