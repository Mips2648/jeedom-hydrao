<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../../../core/php/core.inc.php';

use Mips\HydraoClient\Client;

class hydrao extends eqLogic {
	use MipsEqLogicTrait;

	public static $_encryptConfigKey = array('username', 'password', 'apikey');

	public static function hourlyRefresh() {
		if (config::byKey('autorefresh', 'hydrao', 0) == 0) return;

		$client = hydrao::getClient();
		/** @var hydrao */
		foreach (eqLogic::byType(__CLASS__, true) as $hydrao) {
			try {
				$hydrao->refreshHydraoData($client, false);
			} catch (Exception $e) {
			}
		}
	}

	public static function cronDaily() {
		/** @var hydrao */
		foreach (eqLogic::byType(__CLASS__, true) as $hydrao) {
			if ($hydrao->getConfiguration('type') != 'user') continue;
			try {
				$hydrao->refreshHydraoData();
			} catch (Exception $e) {
			}
		}
	}

	private static function getClient() {
		$username = config::byKey('username', __CLASS__);
		$password = config::byKey('password', __CLASS__);
		$apikey = config::byKey('apikey', __CLASS__);
		$client = new Client($apikey, log::getLogger(__CLASS__));
		if (!$client->login($username, $password)) {
			throw new Exception("Login failed");
		}
		return $client;
	}

	public static function syncDevices() {
		log::add(__CLASS__, 'info', 'Start sync');
		$client = hydrao::getClient();
		$newEqlogic = false;

		$result = $client->Users()->me();
		if ($result->isSuccess()) {
			$me = $result->getData();
			/**
			 * @var hydrao
			 */
			$user = eqLogic::byLogicalId($me->getId(), __CLASS__);
			if (!is_object($user)) {
				log::add(__CLASS__, 'info', "Creating new user with logicalId={$me->getId()}");
				$user = new self();
				$user->setLogicalId($me->getId());
				$user->setEqType_name(__CLASS__);
				$user->setIsEnable(1);
				$user->setIsVisible(1);
				$user->setConfiguration('type', 'user');
				$user->setName(__('Tableau de bord', __FILE__));
				$user->save();
				$newEqlogic = true;
			}
			$user->refreshHydraoData($client);
		}

		$result = $client->ShowerHeads()->get();
		if ($result->isSuccess()) {
			foreach ($result->getData() as $showerHead) {
				log::add(__CLASS__, 'debug', "showerHead:{$showerHead}");
				$logicalId = $showerHead->getDeviceUUID();
				/**
				 * @var hydrao
				 */
				$eqLogic = eqLogic::byLogicalId($logicalId, __CLASS__);
				if (!is_object($eqLogic)) {
					log::add(__CLASS__, 'info', "Creating new showerHead with logicalId={$logicalId}");
					$eqLogic = new self();
					$eqLogic->setLogicalId($logicalId);
					$eqLogic->setEqType_name(__CLASS__);
					$eqLogic->setIsEnable(1);
					$eqLogic->setIsVisible(1);

					$eqLogic->setName($showerHead->getLabel());
					$eqLogic->setConfiguration('mac_address', $showerHead->getMACAddress());
					$eqLogic->setConfiguration('hw_version', $showerHead->getHWVersion());
					$eqLogic->setConfiguration('fw_version', $showerHead->getFWVersion());
					$eqLogic->setConfiguration('shower_type', $showerHead->getType());
					$eqLogic->setConfiguration('type', 'showerHead');
					$eqLogic->save();
					$newEqlogic = true;
				}

				$dateTime = (new DateTime($showerHead->getLastSyncDate()))->format('Y-m-d H:i:s');
				$eqLogic->checkAndUpdateCmd('lastSyncDate', $dateTime);


				$eqLogic->refreshHydraoData($client);
			}
		} else {
			log::add(__CLASS__, 'warning', "getShowerHeads: ({$result->getHttpStatusCode()}){$result->getHttpError()}");
		}
		if ($newEqlogic) {
			event::add('hydrao::newDevice');
		}

		return true;
	}

	public function createCommands($syncValues = false) {
		log::add(__CLASS__, 'info', "Creating commands for {$this->getName()}");

		$this->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', $this->getConfiguration('type'));

		if ($syncValues) {
			$this->refreshHydraoData();
		}

		return true;
	}

	public function getImage() {
		$shower_type = $this->getConfiguration('shower_type', 'none');
		if (file_exists(__DIR__ . "/../config/{$shower_type}.png")) {
			return "plugins/hydrao/core/config/{$shower_type}.png";
		}

		return parent::getImage();
	}

	public function postInsert() {
		$this->createCommands();
	}

	private function refreshUserStats(Client $client) {
		try {
			$result = $client->UserStats()->get();
			if ($result->isSuccess()) {
				$userStats = $result->getData();
				log::add(__CLASS__, 'debug', "userStats: {$userStats}");
				$this->checkAndUpdateCmd('average_volume', $userStats->getAverageVolumeValue());
				$this->checkAndUpdateCmd('average_duration', $userStats->getAverageDurationValue());
				$this->checkAndUpdateCmd('total_energy_saved', $userStats->getTotalEnergySavedValue());
				$this->checkAndUpdateCmd('total_volume_saved', $userStats->getTotalVolumeSavedValue());
				$this->checkAndUpdateCmd('total_money_saved', $userStats->getTotalMoneySavedValue());
			}
			$result = $client->Advice()->get();
			if ($result->isSuccess()) {
				$this->checkAndUpdateCmd('advice', $result->getData()->getDescription());
			}
		} catch (\Throwable $th) {
			log::add(__CLASS__, 'error', 'error get UserStats:' . $th->getMessage());
		}
	}

	private function refreshShowerStats(client $client) {
		try {
			$result = $client->ShowerHeads()->showerHead($this->getLogicalId())->stats(100);
			if ($result->isSuccess()) {
				$showerStats = $result->getData();
				log::add(__CLASS__, 'debug', 'shower:' . $showerStats);
				$this->checkAndUpdateCmd('volume_average', $showerStats->getVolumeAverage());
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	private function refreshShowers(Client $client) {
		$newShowers = 0;
		try {
			$lastShowerId = $this->getConfiguration('last_shower_id', 0);
			$result = $client->ShowerHeads()->showerHead($this->getLogicalId())->showers(config::byKey('syncLimit', __CLASS__, 500), $lastShowerId);
			if ($result->isSuccess()) {
				foreach (array_reverse($result->getData()) as $shower) {
					if ($lastShowerId == $shower->getId()) continue;
					$lastShowerId = $shower->getId();
					log::add(__CLASS__, 'debug', "shower: {$shower}");
					$dateTime = (new DateTime($shower->getDate()))->format('Y-m-d H:i:s');
					$this->checkAndUpdateCmd('volume', $shower->getVolume(), $dateTime);
					$this->checkAndUpdateCmd('temperature', $shower->getTemperature(), $dateTime);
					$this->checkAndUpdateCmd('soapingTime', $shower->getSoapingTime(), $dateTime);
					$this->checkAndUpdateCmd('flow', $shower->getFlow(), $dateTime);
					$this->checkAndUpdateCmd('duration', $shower->getDuration(), $dateTime);
					$this->checkAndUpdateCmd('numberOfSoapings', $shower->getNumberOfSoapings(), $dateTime);
					++$newShowers;
				}
				log::add(__CLASS__, 'info', "All showers synchronized, new:{$newShowers}");
				$this->setConfiguration('last_shower_id', $lastShowerId);
				$this->save(true);
			}
		} catch (\Throwable $th) {
			log::add(__CLASS__, 'error', 'error get showers:' . $th->getMessage());
		}
		return $newShowers;
	}

	public function refreshHydraoData(?Client $client = null, $includeUserStats = true) {
		$client ?: ($client = hydrao::getClient());
		$type = $this->getConfiguration('type');
		switch ($type) {
			case 'showerHead':
				log::add(__CLASS__, 'info', 'Refresh showerHead');
				if ($this->refreshShowers($client) > 0) {
					$this->refreshShowerStats($client);
				}
				break;
			case 'user':
				if ($includeUserStats) {
					log::add(__CLASS__, 'info', 'Refresh user stats');
					$this->refreshUserStats($client);
					break;
				}
			default:
				log::add(__CLASS__, 'warning', "Unknown hydrao eqLogic type: ({$type})");
				break;
		}
	}
}

class hydraoCmd extends cmd {

	public function execute($_options = array()) {
		/**
		 * @var hydrao
		 */
		$eqLogic = $this->getEqLogic();
		log::add('hydrao', 'debug', "action:{$this->getLogicalId()} on {$eqLogic->getLogicalId()}-{$eqLogic->getName()}");
		switch ($this->getLogicalId()) {
			case 'refresh':
				$eqLogic->refreshHydraoData();
				break;
			default:
				log::add(__CLASS__, 'warning', __('Commande inconnue:', __FILE__) . $this->getLogicalId());
		}
	}
}
