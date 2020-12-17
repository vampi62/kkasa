<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
define('TEST_FILE',__DIR__.'/../../3rparty/KKPA/autoload.php');
define('KKASA_HSLCOLOR_LIB',__DIR__.'/../../3rparty/HSLColor/HSLColor.class.php');
define('KKPA_MIN_VERSION','2.3.8');
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/../php/kkasa.inc.php';

/*error_reporting(-1);
ini_set('display_errors', 'On');*/

if (!class_exists('KKPA\Clients\KKPAApiClient')) {
	if (file_exists(TEST_FILE))
	{
		require_once(dirname(__FILE__) . '/../../3rparty/KKPA/autoload.php');
	}
}
if (!class_exists('HSLColor')) {
	if (file_exists(KKASA_HSLCOLOR_LIB))
	{
		require_once(KKASA_HSLCOLOR_LIB);
	}
}

class kkasa extends eqLogic {
		const FEATURES = array(
			"TIM" => 'plug',
			"ENE" => 'power',
			"LED" => 'led',
			'DIM' => 'bulb',
			'TMP' => 'temp',
			'COL' => 'color'
		);

    /*     * *************************Attributs****************************** */
    private static $_client = null;
    private $_device = null;


    /*     * ***********************Methode static*************************** */
    public static function getClient() {
      if (self::$_client == null) {
        self::$_client =  new KKPA\Clients\KKPAApiClient(array(
          'username' => config::byKey('username', __CLASS__),
          'password' => config::byKey('password', __CLASS__),
          'cloud' 	 => config::byKey('cloud', __CLASS__),
          'base_uri' => config::byKey('base_uri', __CLASS__)
        ));
      }
			if (config::byKey('cloud', __CLASS__)==1) {
	      try
	      {
	        self::$_client->getAccessToken();
	      }
	      catch(KKPA\Exceptions\KKPAClientException $ex)
	      {
	        $error_msg = "[Global] An error happened  while trying to retrieve your tokens \n" . $ex->getMessage() . "\n";
	        log::add(__CLASS__, 'debug', $error_msg);
	      }
			}
      return self::$_client;
    }

		public static function getDebugInfo() {
			$ex = null;
			$conf = array(
				'username' 		=> (config::byKey('username', __CLASS__)) ? '***' : 'Undefined',
				'password' 		=> (config::byKey('password', __CLASS__)) ? '***' : 'Undefined',
				'cloud' 			=> config::byKey('cloud', __CLASS__),
				'cron_freq' 	=> config::byKey('cron_freq', __CLASS__),
				'offline_log' => config::byKey('offline_log', __CLASS__),
				'base_uri' 		=> config::byKey('base_uri', __CLASS__)
			);
  		log::add(__CLASS__, 'debug', '*** Conf:');
  		log::add(__CLASS__, 'debug', print_r($conf,true));
			$client = self::getClient();
  		$devicelist = $client->getDeviceList();
  		log::add(__CLASS__, 'debug', $client->toString());
  		log::add(__CLASS__, 'debug', '*** DeviceList:');
  		log::add(__CLASS__, 'debug', print_r($client->debug_last_request(),true));
  		foreach ($devicelist as $device) {
				try {
					log::add(
						__CLASS__,
						'debug',
						'***  Device '
							.$device->getVariable('deviceId',''). ' / '
							.$device->getVariable('child_id','')
					);
					$device->getSysInfo();
		  		log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					$getRealTime = $device->getRealTime();
					if (!is_null($getRealTime) && count($getRealTime)>0)
		  			log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
				} catch (KKPA\Exceptions\KKPASDKException $ex)
				{
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
				}
			}
			if ($ex != null)
			{
				throw $ex;
			}
		}

		public function getDevice() {
			if ($this->_device == null) {
				$client = self::getClient();
				if (config::byKey('cloud', __CLASS__)==1) {
					try {
						$this->_device = $client->getDeviceById(
							$this->getConfiguration('deviceId'),
							$this->getConfiguration('child_id')
						);
					}
					catch(KKPA\Exceptions\KKPADeviceException $ex)
					{
						if ($ex->getCode() == KKPA_DEVICE_OFFLINE || $ex->getCode() == KKPA_TIMEOUT) {
							$this->setInfo('offline',1);
						}
						throw $ex;
					}
					catch(Exception $ex)
					{
						throw $ex;
					}
				} else {
					$local_ip = $this->getConfiguration('local_ip');
					$local_port = $this->getConfiguration('local_port',9999);
					try {
						$this->_device = $client->getDeviceByIp($local_ip,$local_port,$this->getConfiguration('child_id'));
					} catch (KKPA\Exceptions\KKPAClientException $ex) {
						if ($ex->getCode()==KKPA_NO_ROUTE_TO_HOST) {
							log::add(__CLASS__,'Info',"Cannot reach $local_ip. Trying to autodetect IP of ".$this->getLogicalId());
							try {
								$this->_device = $client->getDeviceById(
									$this->getConfiguration('deviceId'),
									$this->getConfiguration('child_id')
								);
								if (is_object($this->_device)) {
									$local_ip = $this->_device->getVariable('local_ip','');
									$port_ip = intval($this->_device->getVariable('local_port',9999));
									log::add(__CLASS__,'Info',"IP found: $local_ip. Updating");
									if ($local_ip!='') {
										$this->setConfiguration("local_ip",$local_ip);
										$this->setConfiguration("local_port",$local_port);
										$this->save();
									}
								}
							} catch (KKPA\Exceptions\KKPAClientException $ex) {
								if ($ex->getCode()==KKPA_NOT_FOUND) {
									$this->setInfo('offline',1);
									throw $ex;
								}
							}
						} else {
							$this->setInfo('offline',1);
							throw $ex;
						}
					}
					catch (Exception $ex)
					{
						throw $ex;
					}
				}
			}
			if (is_null($this->_device))
				$this->setInfo('offline',1);
			else {
				if ($this->getInfo('offline',-1)!=0)
					$this->setInfo('offline',0);
			}
			return $this->_device;
		}

		public function featureString() {
			$result = array();
			$device = $this->getDevice();
			if ($device->is_featured('TIM')) $result[] = 'TIM';
			if ($device->is_featured('ENE')) $result[] = 'ENE';
			if ($device->is_featured('LED')) $result[] = 'LED';
			if ($device->is_featured('DIM')) $result[] = 'DIM';
			if ($device->is_featured('TMP')) $result[] = 'TMP';
			if ($device->is_featured('COL')) $result[] = 'COL';
			return implode("|",$result);
		}

		public function is_featured($feature) {
			if (strpos($this->getConfiguration('features',''),$feature)===false)
				return false;
			else {
				return true;
			}
		}

    public static function cron() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='1')
 		 		self::cronExec();
    }

		public static function cron5() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='5')
 		 		self::cronExec();
    }

	 public static function cron15() {
		 if (strval(config::byKey('cron_freq','kkasa','15'))=='15')
		 		self::cronExec();
	 }

	public static function cron30() {
		if (strval(config::byKey('cron_freq','kkasa','15'))=='30')
			 self::cronExec();
	}

    public static function cronHourly() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='60')
 		 		self::cronExec();
    }

		public static function cronDaily() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='3600')
 		 		self::cronExec();

		 // Once a day: update the firmware version
 		 foreach (self::byType(__CLASS__) as $eqLogic) {
 			 if ($eqLogic->getIsEnable())
 			 {
				 	$device = $eqLogic->getDevice(); //TODO: catch bind or connection error
 					$sysinfo = $device->getSysInfo();
 					$eqLogic->setConfiguration('sw_ver', $sysinfo['sw_ver']);
 					$eqLogic->setConfiguration('fwId', $sysinfo['fwId']);
 					$eqLogic->setConfiguration('oemId', $sysinfo['oemId']);
					$eqLogic->setConfiguration('features',$eqLogic->featureString());
 					$eqLogic->save();
 			 }
 		 }
		}

	 public static function cronExec() {
		 log::add(__CLASS__ ,'debug','[Global] Cron Execution');
		 foreach (self::byType(__CLASS__) as $kkasa) {
			 try {
				 if ($kkasa->getIsEnable())
				 {
					 $kkasa->syncRealTime();
				 }
			 } catch(KKPA\Exceptions\KKPADeviceException $ex)
			 {
				 if ($ex->getCode() == KKPA_DEVICE_OFFLINE || $ex->getCode() == KKPA_TIMEOUT)
				{
					$log_level = config::byKey('offline_log', __CLASS__,'error');
					if ($log_level!='error')
						log::add(__CLASS__,$log_level,sprintf('[%1$s] Device is offline',$kkasa->getLogicalId()));
					else
						throw $ex;
				} else {
					throw $ex;
				}
			} catch (KKPA\Exceptions\KKPAApiErrorType $ex)
			{
				log::add(__CLASS__,'debug',sprintf("Exception code is %s",$ex->getCode()));
				log::add(__CLASS__,'debug',sprintf("KKPA_MISSING_DEVICEID is %s",KKPA_MISSING_DEVICEID));
				if ($ex->getCode() == KKPA_MISSING_DEVICEID) //TODO: Catch not bind device
				{
					$device = $kkasa->getDevice();
					log::add(__CLASS__,'error',sprintf("Missing or incorrect format for deviceId of %s",$device->toString()));
				} else {
					throw $ex;
				}
			}
		 }
	 }

   public static function dependancy_info() {
		 	$log_id = __CLASS__ . '_update';
		  log::add($log_id,'debug','[Dep] Checking dependancy');
   		$return = array();
   		$return['log'] = $log_id;
   		$return['progress_file'] =  jeedom::getTmpFolder(__CLASS__) . '/dependancy_kkasa_in_progress';
   		if (file_exists(__DIR__.'/../../3rparty/KKPA/Clients/KKPAApiClient.php')) {
				try {
					$cur_ver = KKPA\Clients\KKPAApiClient::getVersion();
					$req_ver = KKPA_MIN_VERSION;
					if (version_compare($cur_ver,$req_ver,'<'))
					{
						$logstr = __('[Dep] Nouvelle version des dépendance requise(%s < %s). Merci de réinstaller les dépendances de kkasa',__FILE__);
						log::add(__CLASS__,'error',
							sprintf($logstr,$cur_ver,$req_ver)
						);
		   			$return['state'] = 'nok';
					} else
					{
   					$return['state'] = 'ok';
					}
				}
				catch (Exception $e)
				{
		   		$return['state'] = 'nok';
				}

   		} else {
   			$return['state'] = 'nok';
   		}
			if ($return['state']=='ok' && !file_exists(KKASA_HSLCOLOR_LIB))
				$return['state'] = 'nok';
			log::add(__CLASS__,'debug','[Dep] Dependancy_info: '.print_r($return,true));
   		return $return;
   	}

    public static function dependancy_install() {
 		 	$log_id = __CLASS__ . '_update';
  		log::remove($log_id);
      $path_3rd_party = __DIR__.'/../../3rparty/';
  		return array(
				'script' => __DIR__ . '/../../resources/install.sh ' . $path_3rd_party . ' ' . jeedom::getTmpFolder('kkasa'),
				'log' => log::getPathToLog($log_id)
			);
  	}

		public static function health() {
			if (self::dependancy_info()['state'] == 'nok')
				return array(array(
				'test' => __('Dépendances', __FILE__),
				'result' => 'KO',
				'advice' => __('Réinstallez les dépendances du plugin KKasa',__FILE__),
				'state' => false,
			));
			$return = array();
			$return[] = self::health_kkasa_version();
			$return[] = self::health_kkpa_version();
			$return[] = self::health_kasa_crendentials();
			$return[] = self::health_offline_plugs(end($return)['state']);
			return $return;
		}

		protected static function health_kkasa_version()
		{
			$update = update::byLogicalId('kkasa');
			if (is_object($update))
			{
				$state = ($update->getStatus()=='ok') ? 'OK' : 'KO';
			} else {
				$state = 'KO';
			}

			return array(
				'test' => __('Version KKASA', __FILE__),
				'result' => KKASA_VERSION,
				'advice' => ($state == 'OK') ? '' : __('Mettre à jour le plugin',__FILE__),
				'state' => $state,
			);
		}

		protected static function health_kkpa_version()
		{
			try {
				if (class_exists('KKPA\Clients\KKPAApiClient'))
					$kkpa_version = KKPA\Clients\KKPAApiClient::getVersion();
				else {
					$kkpa_version = 'KO';
				}
			} catch(Exception $ex)
			{
				$kkpa_version = 'KO';
			}
			$result = strtoupper(self::dependancy_info()['state']);
			return array(
				'test' => __('Version KKPA', __FILE__),
				'result' => $kkpa_version,
				'advice' => ($result == 'OK') ? '' : __('(ré)Installer les dépendance dans la configuration du plugin',__FILE__),
				'state' => ($result == 'OK'),
			);
		}

		protected static function health_kasa_crendentials()
		{
			if (intval(config::byKey('cloud', 'kkasa'))==0)
			{
				return array(
					'test' => __('Identification Kasa', __FILE__),
					'result' => "N/A",
					'advice' => __('Mode local : inutile',__FILE__),
					'state' => true,
				);
			}
			try
			{
				if (class_exists('KKPA\Clients\KKPAApiClient'))
				{
					$client = self::getClient();
					$client->getAccessToken();
					$state = true;
				} else {
					$state = false;
				}
			}
			catch(KKPA\Exceptions\KKPAClientException $ex)
			{
				$state = false;
			}
			return array(
				'test' => __('Identification Kasa', __FILE__),
				'result' => ($state) ? "OK" : "NOK",
				'advice' => ($state) ? '' : __('Vérifier vos identifiants Kasa dans la configuration du plugin',__FILE__),
				'state' => $state,
			);
		}

		protected static function health_offline_plugs($state)
		{
			$client = self::getClient();
			$nb_offline = 0;
			if ($state)
			{
  			$devicelist = $client->getDeviceList();
	  		foreach ($devicelist as $device) {
					try
					{
						$device->getRealTime();
					}
					catch(KKPA\Exceptions\KKPADeviceException $ex)
					{
						if ($ex->getCode() == KKPA_DEVICE_OFFLINE || $ex->getCode() == KKPA_TIMEOUT)
							$nb_offline++;
					}
				}
				$state = ($nb_offline == 0);
			}
			return array(
				'test' => __('Prises hors ligne', __FILE__),
				'result' => $nb_offline,
				'advice' => ($state) ? '' : __('Vérifiez que vos prises sont connectées au wifi',__FILE__),
				'state' => $state,
			);
		}

		public static function deleteAll() {
			log::add(__CLASS__, 'debug',"Delete all devices");
			$eqLogics = eqLogic::byType(__CLASS__);
			foreach($eqLogics as $eqLogic)
			{
				log::add(__CLASS__, 'debug',"Deletion of ".$eqLogic->getName());
				$eqLogic->remove();
			}
		}

    public static function syncWithKasa() {
			if (self::dependancy_info()['state'] == 'nok')
			{
				log::add(__CLASS__,'error',
					__('Réinstallez les dépendances du plugin KKasa',__FILE__)
				);
				return -1;
			}
  		$client = self::getClient();
  		$devicelist = $client->getDeviceList();
			log::add(__CLASS__, 'debug',"SCAN: ".count($devicelist)." devices found");
			$nb_devices = 0;
  		foreach ($devicelist as $device) {
				log::add(__CLASS__, 'debug',$device->toString());
				try
				{
	        $sysinfo     = $device->getSysInfo();
	  			$deviceId    = $sysinfo['deviceId'];
					$child_id		 = $device->getVariable('child_id','');
					$eqLogicalId = ($child_id=='') ? $deviceId : $child_id;
	  			$alias       = $sysinfo['alias'];
	  			$type  			 = $sysinfo['type'];
					$fwVer			 = $sysinfo['sw_ver'];
					$deviceName	 = $sysinfo['dev_name'];
					$deviceModel = $sysinfo['model'];
					$deviceMac	 = $sysinfo['mac'];
					$hwId				 = $sysinfo['hwId'];
					$fwId				 = $sysinfo['fwId'];
					$oemId			 = $sysinfo['oemId'];
					$deviceHwVer = $sysinfo['hw_ver'];

	  			$eqLogic = kkasa::byLogicalId($eqLogicalId, __CLASS__);
	  			if (!is_object($eqLogic)) {
						log::add(__CLASS__, 'debug',"$eqLogicalId found and will be added");
						log::add(__CLASS__, 'debug',print_r($sysinfo,true));
	  				$eqLogic = new self();
            foreach (jeeObject::all() as $object) {
                if (stristr($alias,$object->getName())){
                    $eqLogic->setObject_id($object->getId());
                    break;
                }
            }
	  				$eqLogic->setLogicalId($eqLogicalId);
	  				$eqLogic->setName($alias);
						$eqLogic->setConfiguration('deviceId', $deviceId);
						$eqLogic->setConfiguration('child_id', $child_id);
						$eqLogic->setConfiguration('type', $type);
						$eqLogic->setConfiguration('sw_ver', $fwVer);
						$eqLogic->setConfiguration('dev_name', $deviceName);
						$eqLogic->setConfiguration('model', $deviceModel);
						$eqLogic->setConfiguration('mac', $deviceMac);
						$eqLogic->setConfiguration('hwId', $hwId);
						$eqLogic->setConfiguration('fwId', $fwId);
						$eqLogic->setConfiguration('oemId', $oemId);
						$eqLogic->setConfiguration('hw_ver', $deviceHwVer);
						if ($device->getVariable('local_ip','')!='')
						{
							$eqLogic->setConfiguration(
								'local_ip',
								$device->getVariable('local_ip','')
							);
							$eqLogic->setConfiguration(
								'local_port',
								$device->getVariable('local_port',9999)
							);
						}

	  				$eqLogic->setEqType_name(__CLASS__);
	  				$eqLogic->setIsVisible(1);
	  				$eqLogic->setIsEnable(1);
	  				$eqLogic->save();
						$nb_devices++;
	  			} else {
						log::add(__CLASS__, 'debug',"$eqLogicalId found but already known");
					}
					$eqLogic->refreshWidget();
				} catch(KKPA\Exceptions\KKPADeviceException $ex)
				{
					if ($ex->getCode() == KKPA_DEVICE_OFFLINE || $ex->getCode() == KKPA_TIMEOUT)
					{
						log::add(__CLASS__, 'warning',
							sprintf(
								__('Equipement %s trouvé mais injoignable. Ignoré',__FILE__),
								$device->getVariable('deviceId','').' / '
								.$device->getVariable('child_id','')
							)
						);
					}
				}
	  	}
			return $nb_devices;
  	}

		public function syncRealTime()
		{
			log::add(
				__CLASS__,
				'debug',
				'Processing refresh of '
					.$this->getLogicalId()
			);
			$attempt = 0;
			$success = false;
			$changed = false;
			$device = $this->getDevice();
			if (is_null($device))
			{
				log::add(__CLASS__, 'error', "ERROR device is null on line ".__LINE__);
				throw new Exception("Device is null");
			}
			while((!$success) && $attempt < 3)
			{
				try
				{
		      $sysinfo = $device->getSysInfo();
					$this->refresh();
					$changed = $this->setInfo('state',$device->getState()) || $changed;
					$rssi = intval($sysinfo['rssi']);
					if ($rssi <= -30 && $rssi >= -90)
						$changed = $this->setInfo('rssi',$sysinfo['rssi']) || $changed;
					else {
						log::add(__CLASS__,'debug','[%1] incorrect rssi value: "%2" ==> ignored');
					}
					if ($device->is_featured('LED'))
						$changed = $this->setInfo('ledState',(!$sysinfo['led_off'])) || $changed;
					$this->setConfiguration('sw_ver', $sysinfo['sw_ver']);
					$this->setConfiguration('fwId', $sysinfo['fwId']);
					$this->setConfiguration('oemId', $sysinfo['oemId']);

					if ($device->is_featured('ENE'))
					{
						$data = $device->getRealTime();
						foreach($data as $key => $value)
						{
							switch($key)
							{
								case 'power_mw':
									$cmd_name = 'power';
									$value = $value/1000;
									break;
								case 'power':
									$cmd_name = 'power';
									$value = $value;
									break;
								case 'voltage_mv':
									$cmd_name = 'voltage';
									$value = $value/1000;
									break;
								case 'voltage':
									$cmd_name = 'voltage';
									$value = $value;
									break;
								case 'current_ma':
									$cmd_name = 'current';
									$value = $value/1000;
									break;
								case 'current':
									$cmd_name = 'current';
									$value = $value;
									break;
								case 'total_wh':
									$cmd_name = 'consumption';
									break;
								case 'total':
									$cmd_name = 'consumption';
									break;
								default:
									$cmd_name = '';
							}
							if ($cmd_name != '')
							{
								$changed = $this->setInfo($cmd_name,$value) || $changed;
							} else {
								continue;
							}
						}

						$data = $device->getTodayStats();
						$energy = (array_key_exists('energy',$data)) ? $data['energy'] : 0;
						log::add(__CLASS__, 'debug', "Today energy: " . $data['energy'] . "/ Moy: $energy");
						$changed = $this->setInfo('consu_today',$energy) || $changed;

						$data = $device->get7DaysStats();
						$energy = (array_key_exists('energy',$data)) ? round($data['energy']/7.0) : 0;
						log::add(__CLASS__, 'debug', "7days energy: " . $data['energy'] . "/ Moy: $energy");
						$changed = $this->setInfo('consu_7days',$energy) || $changed;

						$data = $device->get30DaysStats();
						$energy = (array_key_exists('energy',$data)) ? round($data['energy']/30.0) : 0;
						log::add(__CLASS__, 'debug', "30days energy: " . $data['energy'] . "/ Moy: $energy");
						$changed = $this->setInfo('consu_30days',$energy) || $changed;
					}

					if ($device->is_featured('DIM'))
					{
						if ($device->getState()==1)
							$changed = $this->setInfo('brightness',$device->getBrightness()) || $changed;
						else {
							$changed = $this->setInfo('brightness',0) || $changed;
						}
					}

					if ($device->is_featured('TMP'))
					{
						$lightState = $device->getLightState();
						$changed = $this->setInfo('color_temp',$lightState['color_temp']) || $changed;
					}

					if ($device->is_featured('COL'))
					{
						$lightState = $device->getLightState();
						$color = new HSLColor();
						$color->setHSV($lightState['hue'],$lightState['saturation']/100, $lightState['brightness']/100);
						$hex = $color->getRGBString();
						log::add(__CLASS__, 'debug', "GetColor: " . print_r($lightState,true)." => $hex");
						$changed = $this->setInfo('colorState',$hex) || $changed;
					}

					$success = true;

					$this->setInfo('offline',0);
					if ($changed) {
						$this->refreshWidget();
					}
				}
				catch(Exception $ex)
				{
					$attempt++;
		  		log::add(__CLASS__, 'debug', "ERROR during request - attempt #" . $attempt . "/3");
		  		log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2)
					{
						$this->setInfo('offline',1);
						throw $ex;
					}
				}
			}

		}

		public function setColor($hex)
		{
			$device = $this->getDevice();
			$success = false;
			$attempt = 0;
			while ((!$success) && $attempt < 3)
			{
				try
				{
					$temp = ($device->is_featured('TMP')) ? 0 : null;
					$color = new HSLColor();
					$color->setRGBString($hex);
					$hsv = $color->getHSV();
					log::add(__CLASS__, 'debug', "SetColor: $hex => ".print_r($hsv,true));
					$hue = max(0,min(360,intval($hsv['h'])));
					$saturation = max(0,min(100,intval($hsv['s']*100)));
					$brightness = max(0,min(100,intval($hsv['v']*100)));
					$device->setLightState($temp,$hue,$saturation,$brightness);
					$this->setInfo('color_temp',0);
					sleep(1.5);
					$this->syncRealTime();
					$success = true;
				}
				catch(Exception $ex)
				{
					$attempt++;
					log::add(__CLASS__, 'debug', "ERROR during request - attempt #".$attempt . "/3");
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2)
					{
						$this->setInfo('offline',1);
						throw $ex;
					}
				}
			}
		}

		public function setTempColor($temp)
		{
			$device = $this->getDevice();
			$success = false;
			$attempt = 0;
			while ((!$success) && $attempt < 3)
			{
				try
				{
					$temp = min(6500,max(2700,intval($temp)));
					$hue = ($device->is_featured('COL')) ? 0 : null;
					$saturation = ($device->is_featured('COL')) ? 0 : null;
					$brightness = null;
					$device->setLightState($temp,$hue,$saturation,$brightness);
					$this->setInfo('color','#ffffff');
					sleep(1.5);
					$this->syncRealTime();
					$success = true;
				}
				catch(Exception $ex)
				{
					$attempt++;
					log::add(__CLASS__, 'debug', "ERROR during request - attempt #".$attempt . "/3");
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2)
					{
						$this->setInfo('offline',1);
						throw $ex;
					}
				}
			}
		}

		public function setBrightness($level)
		{
			$device = $this->getDevice();
			$success = false;
			$attempt = 0;
			while ((!$success) && $attempt < 3)
			{
				try
				{
					$level = max(0,min(100,intval($level)));
					if ($level>0)
					{
						$color_temp = null;
						$hue = null;
						$saturation = null;
						$brightness = $level;
						$device->switchOn();
						sleep(0.1);
						$device->setLightState($color_temp,$hue,$saturation,$brightness);
					} else {
						$device->switchOff();
					}
					sleep(1.5);
					$this->syncRealTime();
					$success = true;
				}
				catch(Exception $ex)
				{
					$attempt++;
					log::add(__CLASS__, 'debug', "ERROR during request - attempt #".$attempt . "/3");
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2)
					{
						$this->setInfo('offline',1);
						throw $ex;
					}
				}
			}
		}

		public function setState($state)
		{
			$device = $this->getDevice();
			$success = false;
			$attempt = 0;
			while ((!$success) && $attempt < 3)
			{
				try
				{
					$state = boolval($state);
					if ($state)
					{
						$device->switchOn();
					} else {
						$device->switchOff();
					}
					sleep(1.5);
					$this->syncRealTime();

					// Refresh father
					if ($this->getConfiguration('child_id','')!='')
					{
						log::add(__CLASS__, 'debug',"Refreshing father");
						$eqLogics = eqLogic::byType(__CLASS__);
						foreach($eqLogics as $eqLogic)
						{
							if ($eqLogic->getConfiguration('deviceId','')==$this->getConfiguration('deviceId','')
								&& $eqLogic->getConfiguration('child_id','')!=$this->getConfiguration('child_id',''))
							{
								$eqLogic->syncRealTime();
							}
						}
					}

					// Refresh children
					if ($device->has_children())
					{
						log::add(__CLASS__, 'debug',"Refreshing children");
						$eqLogics = eqLogic::byType(__CLASS__);
						foreach($eqLogics as $eqLogic)
						{
							if ($eqLogic->getConfiguration('deviceId','')==$this->getConfiguration('deviceId','')
								&& $eqLogic->getConfiguration('child_id','')!='')
							{
								$eqLogic->syncRealTime();
							}
						}
					}
					$success = true;
				}
				catch(Exception $ex)
				{
					$attempt++;
					log::add(__CLASS__, 'debug', "ERROR during request - attempt #".$attempt . "/3");
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2)
					{
						$this->setInfo('offline',1);
						throw $ex;
					}
				}
			}
		}

		public function toogleLedState()
		{
			$device = $this->getDevice();
			$ledState = !($device->getLedState());
			$success = false;
			$attempt = 0;
			while ((!$success) && $attempt < 3)
			{
				try
				{
					$device->setLedState($ledState);
					sleep(1.5);
					$this->syncRealTime();
					$success = true;
				}
				catch(Exception $ex)
				{
					$attempt++;
					log::add(__CLASS__, 'debug', "ERROR during request - attempt #".$attempt . "/3");
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2)
					{
						$this->setInfo('offline',1);
						throw $ex;
					}
				}
			}
		}

		public function getImgFilePath() {
			switch($this->getConfiguration('type'))
			{
				case 'IOT.SMARTPLUGSWITCH':
					switch (substr($this->getConfiguration('model',''),0,5))
					{
						case 'HS300':
						case 'KP300':
						case 'KP200':
						case 'KP400':
						case 'KP303':
							return ($this->getConfiguration('child_id','')=='') ? 'multi1.png' : 'slot1.png';
							break;

						case 'HS200':
						case 'HS220':
							return 'switch1.png';
							break;

						case 'HS100':
						case 'HS105':
						case 'HS110':
						default:
							return 'plug1.png';
							break;
					}
					break;

				case 'IOT.SMARTBULB':
					switch (substr($this->getConfiguration('model',''),0,5))
					{
						case 'LB100':
							return 'lb100.png';
							break;

						case 'LB120':
							return 'lb120.png';
							break;

						case 'LB130':
							return 'lb130.png';
							break;
					}
					break;
			}
			return false;
		}

		public function getImage() {
			return 'plugins/kkasa/docs/assets/images/' . $this->getImgFilePath();
		}

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
			$this->setConfiguration('features',$this->featureString());
    }

    public function postInsert() {
			$this->loadCmdFromConf('all');
			$this->syncRealTime();
    }

    public function preSave() {
    }

		public function loadCmdFromConf($cmd='all',$force=0) {
			//$device = $this->getDevice();
			if ($cmd!='all')
				$cmdSets = array($cmd);
			else {
				foreach($this->getCmd() as $curCmd)
					$curCmd->remove();
				$cmdSets = array('basic');
				foreach(self::FEATURES as $feature => $cmdType)
				{
					if ($this->is_featured($feature))
					{
						$cmdSets[] = $cmdType;
					}
				}
			}
			$nb_cmd = 0;
			foreach($cmdSets as $cmdSet)
			{
				$filename = dirname(__FILE__) . '/../config/' . $cmdSet.'.json';
				if (!is_file($filename)) {
					throw new \Exception("File $filename does not exist");
				}
				$device = is_json(file_get_contents($filename), array());
				if (!is_array($device) || !isset($device['commands'])) {
					break;
				}
				foreach($device['commands'] as $key => $cmd)
				{
					if (array_key_exists('logicalId',$cmd))
						$id = $cmd['logicalId'];
					else
					{
						if (array_key_exists('name',$cmd))
							$id = $cmd['name'];
						else {
							$id = '';
						}
					}
					$curCmd = $this->getCmd(null, $id);
					if ($force==1 && is_object($curCmd)) {
						$curCmd->remove();
					} elseif (($force == 0) && is_object($curCmd)) {
						unset($device['commands'][$key]);
						continue;
					}
					if (array_key_exists('name',$cmd))
						$cmd['name'] = __($cmd['name'],__FILE__);
				}
				if (count($device['commands'])>0)
				{
					$this->import($device);
				}
				$nb_cmd += count($device['commands']);
			}
			return $nb_cmd;
		}

    public function postSave() {
    }

    public function preUpdate() {

    }

    public function postUpdate() {

    }

    public function preRemove() {

    }

    public function postRemove() {

    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
		public function setInfo($cmd_name,$value)
		{
			$cmd = $this->getCmd(null,$cmd_name);
			if (is_object($cmd)) {
				$maxValue = $cmd->getConfiguration('maxValue',false);
				if ($maxValue!==false)
				{
					if ($value > $maxValue)
					{
						$logstr = '[%s$1] %s$2 value(%f$3) > maxValue(%f$4)';
						$log = sprintf($logstr,$this->getLogicalId(),$cmd_name,$value,$maxValue);
						log::add(__CLASS__,'error',$log);
						return false;
					}
				}
				$minValue = $cmd->getConfiguration('minValue',false);
				if ($minValue!==false)
				{
					if ($value < $minValue)
					{
						$logstr = '[%s$1] %s$2 value(%f$3) < minValue(%f$4)';
						$log = sprintf($logstr,$this->getLogicalId(),$cmd_name,$value,$minValue);
						return false;
					}
				}
				$cmd->refresh();
				$changed = $this->checkAndUpdateCmd($cmd_name, $value);
				$logstr = '[%1$s] set: %2$s to %3$s';
				$log = sprintf($logstr,$this->getLogicalId(),$cmd->getName(),$value);
				log::add(__CLASS__,'debug',$log);
				$cmd->event($value,null,0);
				return $changed;
			}
			return false;
		}

		public function getInfo($cmd_name,$default=null)
		{
			$cmd = $this->getCmd(null,$cmd_name);
			if (is_object($cmd)) {
				$cmd->refresh();
				return $cmd->getValue();
			}
			return $default;
		}

		public function import($_configuration, $_dontRemove = false) {
			$cmdClass = $this->getEqType_name() . 'Cmd';
			if (isset($_configuration['configuration'])) {
				foreach ($_configuration['configuration'] as $key => $value) {
					$this->setConfiguration($key, $value);
				}
			}
			if (isset($_configuration['category'])) {
				foreach ($_configuration['category'] as $key => $value) {
					$this->setCategory($key, $value);
				}
			}
			$cmd_order = 0;
			foreach($this->getCmd() as $liste_cmd)
			{
				if ($liste_cmd->getOrder()>$cmd_order)
					$cmd_order = $liste_cmd->getOrder()+1;
			}
			$link_cmds = array();
			$link_actions = array();
			$arrayToRemove = [];
			if (isset($_configuration['commands'])) {
				foreach ($_configuration['commands'] as $command) {
					$cmd = null;
					foreach ($this->getCmd() as $liste_cmd) {
						if ((isset($command['logicalId']) && $liste_cmd->getLogicalId() == $command['logicalId'])
						|| (isset($command['name']) && $liste_cmd->getName() == $command['name'])) {
							$cmd = $liste_cmd;
							break;
						}
					}
					try {
						if ($cmd === null || !is_object($cmd)) {
							$cmd = new $cmdClass();
							$cmd->setOrder($cmd_order);
							$cmd->setEqLogic_id($this->getId());
						} else {
							$command['name'] = $cmd->getName();
							if (isset($command['display'])) {
								unset($command['display']);
							}
						}
						utils::a2o($cmd, $command);
						$cmd->setConfiguration('logicalId', $cmd->getLogicalId());
						$cmd->save();
						if (isset($command['value'])) {
							$link_cmds[$cmd->getId()] = $command['value'];
						}
						if (isset($command['configuration']) && isset($command['configuration']['updateCmdId'])) {
							$link_actions[$cmd->getId()] = $command['configuration']['updateCmdId'];
						}
						$cmd_order++;
					} catch (Exception $exc) {
						log::error('kkasa','error','Error importing '.$command['name']);
						throw $exc;
					}
					$cmd->event('');
				}
			}
			if (count($link_cmds) > 0) {
				foreach ($this->getCmd() as $eqLogic_cmd) {
					foreach ($link_cmds as $cmd_id => $link_cmd) {
						if ($link_cmd == $eqLogic_cmd->getLogicalId()) { // diff kkasa
							$cmd = cmd::byId($cmd_id);
							if (is_object($cmd)) {
								$cmd->setValue($eqLogic_cmd->getId());
								$cmd->save();
							}
						}
					}
				}
			}
			if (count($link_actions) > 0) {
				foreach ($this->getCmd() as $eqLogic_cmd) {
					foreach ($link_actions as $cmd_id => $link_action) {
						if ($link_action == $eqLogic_cmd->getName()) {
							$cmd = cmd::byId($cmd_id);
							if (is_object($cmd)) {
								$cmd->setConfiguration('updateCmdId', $eqLogic_cmd->getId());
								$cmd->save();
							}
						}
					}
				}
			}
			$this->save();
		}
}

class kkasaCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
			if ($this->getType() == 'info') {
				return;
			}
			if ($this->getType() == '') {
				return '';
			}
			$eqLogic = $this->getEqLogic();
			if ($this->getLogicalId() == 'refresh') {
				$eqLogic->syncRealTime();
			}
			if ($this->getLogicalId() == 'on') {
				$eqLogic->setState(1);
			}
			if ($this->getLogicalId() == 'off') {
				$eqLogic->setState(0);
			}
			if ($this->getLogicalId() == 'toogleLed') {
				$eqLogic->toogleLedState();
			}
			if ($this->getLogicalId() == 'lightOn') {
				$eqLogic->setState(1);
			}
			if ($this->getLogicalId() == 'lightOff') {
				$eqLogic->setState(0);
			}
			if ($this->getLogicalId() == 'setBrightness') {
				$eqLogic->setBrightness($_options['slider']);
			}
			if ($this->getLogicalId() == 'setColorTemp') {
				$eqLogic->setTempColor($_options['slider']);
			}
			if ($this->getLogicalId() == 'color') {
				$eqLogic->setColor($_options['color']);
			}


    }

    /*     * **********************Getteur Setteur*************************** */
}
