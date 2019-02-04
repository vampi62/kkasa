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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../core/php/kkasa.inc.php';

function kkasa_install() {
  config::save('cron_freq', '15','kkasa');
  config::save('cloud', '1','kkasa');
  config::save('version',KKASA_VERSION,'kkasa');
}

function kkasa_update() {
  if (config::byKey('cron_freq', 'kkasa','-1')=='-1')
  {
    config::save('cron_freq', '15','kkasa');
  }
  if (config::byKey('cloud', 'kkasa','-1')=='-1')
  {
    config::save('cloud', '1','kkasa');
  }
  $kkasa_version = config::byKey('version','kkasa','1.0');
  log::add('kkasa', 'debug', "Update kkasa from ".$kkasa_version . " to ".KKASA_VERSION);

  if (version_compare($kkasa_version,'1.1','<'))
  {
    foreach (eqLogic::byType('kkasa') as $eqLogic) {
      addCmd($eqLogic,'rssi','info','numeric',__('Force signal',__FILE__),0,1,'dBm');
    }
  }

  config::save('version',KKASA_VERSION,'kkasa');
}


function kkasa_remove() {

}

function addCmd($eqLogic,$id,$type,$subtype=NULL,$name = NULL,$isVisible=NULL,$isHistorized=NULL,$unit=NULL,$generic_type=NULL)
{
  if (!isset($name)) $name = ucfirst($id);
  $cmd = $eqLogic->getCmd(null, $id);
  if (!is_object($cmd)) {
    $cmd = new kkasaCmd();
    $cmd->setName(__($name, __FILE__));
    if (isset($isVisible)) $cmd->setIsVisible($isVisible);
    if (isset($isHistorized)) $cmd->setIsHistorized($isHistorized);
    if (isset($generic_type)) $cmd->setDisplay('generic_type', $generic_type);
    if (isset($unit)) $cmd->setUnite($unit);
  }
  $cmd->setEqLogic_id($eqLogic->getId());
  $cmd->setLogicalId($id);
  $cmd->setType($type);
  if (isset($subtype)) $cmd->setSubType($subtype);
  $cmd->save();
}

?>
