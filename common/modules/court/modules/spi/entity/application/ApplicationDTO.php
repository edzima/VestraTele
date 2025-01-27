<?php

namespace common\modules\court\modules\spi\entity\application;

use yii\base\Model;
use yii\httpclient\Response;

class ApplicationDTO extends Model implements
	ApplicationType {

	public int $courtId;
	public string $courtName;

	public string $department;

	public string $departmentFullName;
	public string $repertory;

	public int $repertoryId;

	public int $year;

	public string $roleInLawsuit;
	public string $represented;

	public ?string $courtSessionDate = null;

	public string $type;

	public ?int $status = null;

	public ?string $comments = null;

	public ?array $parameters = null;

	public ?int $lawsuitId = null;
	public string $lawsuitNumber = '';

	public ?int $courtSessionId = null;
	public string $signature = '';

	private ?int $applicationId; // Long
	private ?string $pzUrl; // Adres do PZ
	private ?string $autotranscriptionType; // Typ autotranskrypcji
	private ?string $participantName; // Imię u czestnika
	private ?string $participantSurname; // Nazwisko uczestnika
	private ?string $participantEmail; // Email uczestnika
	private ?string $sessionStart; // Data od
	private ?string $sessionEnd; // Data do
	private ?string $courtRoom; // Sala
	private ?string $applicationNumber; // Numer wniosku
	private ?string $errorMessage; // Treść błędu
	private ?string $nonresidentCourt; // Identyfikator sądu dla rozprawy odmiejscowionej
	private ?string $nonresidentDate; // Data dla rozprawy odmiejscowionej
	private ?bool $paperApplication; // Czy akta papierowe
	private ?bool $eprotocolApplication; // Czy eprotokół
	private string $createdDate; // Data utworzenia rekordu wniosku
	private string $modificationDate; // Ostatnia data modyfikacji rekordu wniosku

	public static function createFromResponse(Response $response): ?self {
		if ($response->isOk) {
			$data = $response->getData();
			$data = array_filter($data, function ($item) {
				return $item !== null;
			});
			return new self($data);
		}
		return null;
	}

	public function rules(): array {
		return [
			[
				[
					'type',
					'courtId', 'courtName', 'department', 'reportory',
					'reportoryId', 'year', 'roleInLawsuit',
					'lawsuitNumber', 'departmentFullName',
				],
				'required',
			],
			[
				[
					'courtSessionDate', 'required', 'when' => function (): bool {
					return $this->type !== self::APPLICATION_TYPE_LAWSUIT;
				},
				],
			],
			[
				[
					['represented'], 'required', 'when' => function (): bool {
					return $this->type === self::APPLICATION_TYPE_LAWSUIT;
				},
				],
			],
		];
	}

	/**
	 * @return array the attribute labels.
	 */
	public function attributeLabels(): array {
		return [
			'comments' => 'Komentarze',
			'roleInLawsuit' => 'Rola w sprawie',
			'courtId' => 'ID Sądu',
			'lawsuitId' => 'ID Sprawy',
			'lawsuitNumber' => 'Numer Sprawy',
			'department' => 'Wydział',
			'repertory' => 'Repertorium',
			'type' => 'Typ',
			'repertoryId' => 'ID Repertorium',
			'courtSessionId' => 'ID Posiedzenia Sądu',
			'courtSessionDate' => 'Data Posiedzenia',
			'signature' => 'Sygnatura',
			'year' => 'Rok',
			'applicationId' => 'ID Wniosku',
			'pzUrl' => 'Adres do PZ',
			'autotranscriptionType' => 'Typ Autotranskrypcji',
			'participantName' => 'Imię Uczestnika',
			'participantSurname' => 'Nazwisko Uczestnika',
			'participantEmail' => 'Email Uczestnika',
			'sessionStart' => 'Data Od',
			'sessionEnd' => 'Data Do',
			'courtRoom' => 'Sala',
			'applicationNumber' => 'Numer Wniosku',
			'errorMessage' => 'Treść Błędu',
			'nonresidentCourt' => 'ID Sądu dla Rozprawy Odmiejscowionej',
			'nonresidentDate' => 'Data dla Rozprawy Odmiejscowionej',
			'paperApplication' => 'Czy Akta Papierowe?',
			'eprotocolApplication' => 'Czy Eprotokół?',
			'createdDate' => 'Data Utworzenia Rekordu Wniosku',
			'modificationDate' => 'Ostatnia Data Modyfikacji Rekordu Wniosku',
		];
	}

	public function getApplicationType(): string {
		return $this->type;
	}

}
