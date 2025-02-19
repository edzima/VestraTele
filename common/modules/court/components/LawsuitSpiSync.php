<?php

namespace common\modules\court\components;

use common\modules\court\models\Lawsuit;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\repository\RepositoryManager;
use Yii;
use yii\base\Component;
use yii\di\Instance;
use yii\helpers\Console;

class LawsuitSpiSync extends Component {

	private RepositoryManager $repositoryManager;

	public $sessionSync = [
		'class' => LawsuitSessionsSync::class,
	];

	private ?int $sessionsCount;

	public function __construct(RepositoryManager $manager, array $config = []) {
		$this->repositoryManager = $manager;
		parent::__construct($config);
	}

	public function all(string $interval = '1 hour'): int {
		$count = 0;
		foreach (Lawsuit::find()
			->andWhere(['spi_last_sync_at' => null])
			->orWhere(['<', "DATE_ADD( spi_last_sync_at, INTERVAL $interval)", date(DATE_ATOM)])
			->with('court')
			->batch() as $models) {
			foreach ($models as $model) {
				if ($this->one($model)) {
					$count++;
				}
			}
		}
		return $count;
	}

	public function one(Lawsuit $model, LawsuitViewIntegratorDto $spiLawsuit = null): bool {
		$appeal = $model->court->getSPIAppealWithParents();
		if ($appeal === null) {
			return false;
		}
		if ($spiLawsuit === null) {
			$spiLawsuit = $this->findSpiLawsuit($model);
		}
		if ($spiLawsuit === null) {
			return false;
		}
		if (!$this->shouldSync($model, $spiLawsuit)) {
			if (Yii::$app->request->isConsoleRequest) {
				Console::output('Lawsuit: ' . $spiLawsuit->signature . ' in Appeal: ' . $appeal . ' has already synced.');
			}
			$model->updateAttributes([
				'spi_last_sync_at' => date(DATE_ATOM),
			]);
			return false;
		}
		if (Yii::$app->request->isConsoleRequest) {
			Console::output('Sync Lawsuit: ' . $spiLawsuit->signature . ' in Appeal: ' . $appeal);
		}

		$attributes = $this->getLawsuitAttributes($spiLawsuit);
		$attributes['spi_confirmed_user'] = null;
		$attributes['spi_last_sync_at'] = date(DATE_ATOM);
		$model->updateAttributes($attributes);
		$this->sessionsSync($model, $spiLawsuit->id);
		return true;
	}

	protected function sessionsSync(Lawsuit $lawsuit, int $spiLawsuitId): ?bool {
		$sync = $this->getSessionSync();
		if ($sync === null) {
			$this->sessionsCount = null;
			return null;
		}
		$sessions = $this->repositoryManager
			->getCourtSessions()
			->setAppeal($lawsuit->court->getSPIAppealWithParents())
			->getByLawsuit($spiLawsuitId)
			->getModels();
		$this->sessionsCount = $sync->sync($lawsuit, $sessions);
		return true;
	}

	protected function getSessionSync(): ?LawsuitSessionsSync {
		if (empty($this->sessionSync)) {
			return null;
		}
		$this->sessionSync = Instance::ensure($this->sessionSync, LawsuitSessionsSync::class);
		return $this->sessionSync;
	}

	protected function shouldSync(Lawsuit $model, LawsuitViewIntegratorDto $spiLawsuit): bool {
		return strtotime($model->spi_last_update_at) !== strtotime($spiLawsuit->lastUpdate);
	}

	protected function findSpiLawsuit(Lawsuit $model): ?LawsuitViewIntegratorDto {
		return $this->repositoryManager
			->getLawsuits()
			->setAppeal($model->court->getSPIAppealWithParents())
			->findBySignature($model->signature_act, $model->court->name, true, false);
	}

	private function getLawsuitAttributes(LawsuitViewIntegratorDto $spiLawsuit): array {
		return [
			'result' => $spiLawsuit->result,
			'spi_last_update_at' => date(DATE_ATOM, strtotime($spiLawsuit->lastUpdate)),
			'updated_at' => date(DATE_ATOM, strtotime($spiLawsuit->modificationDate)),
		];
	}

	public function getSessionsCount(): ?int {
		return $this->sessionsCount;
	}
}
