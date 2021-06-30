<?php

namespace common\modules\reminder\controllers;

class RelatedController extends Controller {

	/**
	 * @param int $id
	 * @return string
	 * @todo
	 */
	public function actionCreate(int $id): string {
		$relatedModel = $this->module->relatedModel;
	}
}
