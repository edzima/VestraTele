<?php

namespace backend\modules\provision\widgets;

use backend\helpers\Url;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserData;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\QueryInterface;

class UserProvisionsWidget extends Widget {

	public ProvisionUserData $userData;

	public bool $withFrom = true;
	public bool $withTypeDetail = true;

	public $dataProviderConfig = [
		'pagination' => false,
		'sort' => false,
	];

	public array $extraProvisionsColumns = [];

	public function run(): string {
		return $this->render('user-provisions', [
			'model' => $this->userData,
			'selfDataProvider' => $this->getSelfiesDataProvider(),
			'fromDataProvider' => $this->withFrom ? $this->getFromDataProvider() : null,
			'parentsWithoutProvisionsDataProvider' => $this->getParentsWithoutProvisionsDataProvider(),
			'toDataProvider' => $this->getToDataProvider(),
			'allChildesDataProvider' => $this->withFrom ? $this->getAllChildesWithoutProvisionsDataProvider() : null,
			'extraProvisionsColumns' => $this->extraProvisionsColumns,
		]);
	}

	public function getCreateSelfUrl(int $typeId = null): string {
		if ($typeId === null) {
			$typeId = $this->userData->type->id ?? null;
		}
		return Url::toRoute([
			'/provision/user/create-self',
			'userId' => $this->userData->getUser()->id,
			'typeId' => $typeId,
		]);
	}

	public function getFromDataProvider(): DataProviderInterface {
		return $this->createActiveDataProvider(
			$this->userData->getFromQuery()->with('fromUser.userProfile', 'type')
		);
	}

	public function getToDataProvider(): DataProviderInterface {
		return $this->createActiveDataProvider(
			$this->userData->getToQuery()->with('toUser.userProfile', 'type')
		);
	}

	public function getSelfiesDataProvider(): DataProviderInterface {
		return $this->createActiveDataProvider(
			$this->userData->getSelfQuery()->with('type')
		);
	}

	public function getParentsWithoutProvisionsDataProvider(): ?DataProviderInterface {
		$query = $this->userData->getAllParentsQueryWithoutProvision();
		if ($query) {
			return $this->createActiveDataProvider($query->orderByLastname());
		}
		return null;
	}

	public function getAllChildesWithoutProvisionsDataProvider(): ?DataProviderInterface {
		$query = $this->userData->getAllChildesQueryWithoutProvision();
		if ($query) {
			return $this->createActiveDataProvider($query->orderByLastname());
		}
		return null;
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	private function createActiveDataProvider(QueryInterface $query): ActiveDataProvider {
		$config = $this->dataProviderConfig;
		if (!isset($config['class'])) {
			$config['class'] = ActiveDataProvider::class;
		}
		$query->joinWith('type');
		$query->orderBy([
			ProvisionType::tableName() . '.name' => SORT_ASC,
			ProvisionUser::tableName() . '.from_at' => SORT_ASC,
		]);
		$config['query'] = $query;

		return Yii::createObject($config);
	}

}
