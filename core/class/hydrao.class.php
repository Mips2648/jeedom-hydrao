<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../../../core/php/core.inc.php';

use Mips\HydraoClient\AccessToken;
use Mips\HydraoClient\Client;

class hydrao extends eqLogic {
	use MipsEqLogicTrait;

	public static $_encryptConfigKey = array('username', 'password', 'apikey');

	public static function cron() {
		/** @var hydrao */
		foreach (self::byType(__CLASS__, true) as $eqLogic) {
			$autorefresh = $eqLogic->getConfiguration('autorefresh');
			if ($autorefresh == '')  continue;
			try {
				$cron = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
				if ($cron->isDue()) {
					$eqLogic->refreshHydraoData();
				}
			} catch (Exception $e) {
				log::add(__CLASS__, 'error', "Error during refresh of {$eqLogic->getHumanName()}: " . $e->getMessage());
			}
		}
	}

	/**
	 * Get access token from cache.
	 *
	 * @return AccessToken
	 */
	private static function getAccessTokenFromCache() {
		if (cache::exist('hydrao::accessToken')) {
			log::add(__CLASS__, 'debug', "get token from cache");
			return new AccessToken(cache::byKey('hydrao::accessToken')->getValue());
		}
		return null;
	}

	/**
	 * Save access token to cache.
	 *
	 * @param  AccessToken $accessToken
	 */
	private static function setAccessTokenToCache($accessToken) {
		log::add(__CLASS__, 'debug', "set token to cache, token is valid until " . date('Y-m-d H:i:s', $accessToken->getExpires()));
		cache::set('hydrao::accessToken', $accessToken->jsonSerialize());
	}

	/** @var \Mips\HydraoClient\Client */
	private static $_client;

	private static function getClient() {
		if (hydrao::$_client)
			return hydrao::$_client;

		$apikey = config::byKey('apikey', __CLASS__);
		hydrao::$_client = new Client($apikey, log::getLogger(__CLASS__));

		$accessToken = hydrao::getAccessTokenFromCache();
		if ($accessToken !== null && !$accessToken->hasExpired()) {
			log::add(__CLASS__, 'debug', "existing valid token, opening session");

			try {
				$newAccessToken = hydrao::$_client->openSession($accessToken);
				hydrao::setAccessTokenToCache($newAccessToken);
				return hydrao::$_client;
			} catch (\Throwable $th) {
				log::add(__CLASS__, 'info', $th->getMessage());
			}
		}

		log::add(__CLASS__, 'debug', "session open failed or no valid token in cache");
		$username = config::byKey('username', __CLASS__);
		$password = config::byKey('password', __CLASS__);

		try {
			$newAccessToken = hydrao::$_client->newSession($username, $password);
			hydrao::setAccessTokenToCache($newAccessToken);
			return hydrao::$_client;
		} catch (\Throwable $th) {
			hydrao::$_client = null;
			throw $th;
		}
	}

	public static function syncDevices() {
		log::add(__CLASS__, 'info', 'Start sync');
		$client = hydrao::getClient();
		$newEqlogic = false;

		sleep(1);
		$result = $client->users()->me();
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
				$user->setConfiguration('autorefresh', rand(0, 59) . ' 4 * * *');
				$user->setName(__('Tableau de bord', __FILE__));
				$user->save();
				$newEqlogic = true;
			}
			sleep(1);
			$user->refreshHydraoData();
		} else {
			log::add(__CLASS__, 'warning', "Error while getting new user data: ({$result->getHttpStatusCode()}){$result->getHttpError()} - response received: {$result->getResponseBody()}");
		}

		sleep(1);
		$result = $client->showerHeads()->get();
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
					$user->setConfiguration('autorefresh', rand(0, 59) . ' */2 * * *');
					$eqLogic->save();
					$newEqlogic = true;
				}

				$dateTime = (new DateTime($showerHead->getLastSyncDate()))->format('Y-m-d H:i:s');
				$eqLogic->checkAndUpdateCmd('lastSyncDate', $dateTime);

				sleep(1);
				$eqLogic->refreshHydraoData();
			}
		} else {
			log::add(__CLASS__, 'warning', "Error while getting new showerHeads: ({$result->getHttpStatusCode()}){$result->getHttpError()} - response received: {$result->getResponseBody()}");
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
			$result = $client->userStats()->get();
			if ($result->isSuccess()) {
				$userStats = $result->getData();
				log::add(__CLASS__, 'debug', "userStats: {$userStats}");
				$this->checkAndUpdateCmd('average_volume', $userStats->getAverageVolumeValue());
				$this->checkAndUpdateCmd('average_duration', $userStats->getAverageDurationValue());
				$this->checkAndUpdateCmd('total_energy_saved', $userStats->getTotalEnergySavedValue());
				$this->checkAndUpdateCmd('total_volume_saved', $userStats->getTotalVolumeSavedValue());
				$this->checkAndUpdateCmd('total_money_saved', $userStats->getTotalMoneySavedValue());
			} else {
				log::add(__CLASS__, 'warning', "Error while getting userStats: ({$result->getHttpStatusCode()}){$result->getHttpError()} - response received: {$result->getResponseBody()}");
			}
			$result = $client->advice()->get();
			if ($result->isSuccess()) {
				$this->checkAndUpdateCmd('advice', $result->getData()->getDescription());
			} else {
				log::add(__CLASS__, 'warning', "Error while getting advice: ({$result->getHttpStatusCode()}){$result->getHttpError()} - response received: {$result->getResponseBody()}");
			}
		} catch (\Throwable $th) {
			log::add(__CLASS__, 'error', 'error get UserStats:' . $th->getMessage());
		}
	}

	private function refreshShowerStats(client $client) {
		try {
			$result = $client->showerHeads()->showerHead($this->getLogicalId())->stats(100);
			if ($result->isSuccess()) {
				$showerStats = $result->getData();
				log::add(__CLASS__, 'debug', 'shower:' . $showerStats);
				$this->checkAndUpdateCmd('volume_average', $showerStats->getVolumeAverage());
			} else {
				log::add(__CLASS__, 'warning', "Error while getting stats for showerHead '{$this->getLogicalId()}': ({$result->getHttpStatusCode()}){$result->getHttpError()} - response received: {$result->getResponseBody()}");
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	private function refreshShowers(Client $client) {
		$newShowers = 0;
		try {
			$lastShowerId = $this->getConfiguration('last_shower_id', 0);
			$result = $client->showerHeads()->showerHead($this->getLogicalId())->showers(config::byKey('syncLimit', __CLASS__, 500), $lastShowerId);
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
			} else {
				log::add(__CLASS__, 'warning', "Error while getting showers for showerHead '{$this->getLogicalId()}': ({$result->getHttpStatusCode()}){$result->getHttpError()} - response received: {$result->getResponseBody()}");
			}
		} catch (\Throwable $th) {
			log::add(__CLASS__, 'error', 'error get showers:' . $th->getMessage());
		}
		return $newShowers;
	}

	public function refreshHydraoData() {
		$client = hydrao::getClient();
		$type = $this->getConfiguration('type');
		switch ($type) {
			case 'showerHead':
				log::add(__CLASS__, 'info', 'Refresh showerHead');
				if ($this->refreshShowers($client) > 0) {
					sleep(1);
					$this->refreshShowerStats($client);
				}
				break;
			case 'user':
				log::add(__CLASS__, 'info', 'Refresh user stats');
				$this->refreshUserStats($client);
				break;
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
