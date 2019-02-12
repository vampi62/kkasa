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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init();

  	if (init('action') == 'syncWithKasa') {
      $nb_devices = kkasa::syncWithKasa();
      if ($nb_devices>0)
      {
        ajax::success($nb_devices);
      }
      else {
        ajax::error(__("Synchronisation terminée mais aucun équipement joignable trouvé. Vérifiez l'application mobile Kasa",__FILE__));
      }
  	} elseif(init('action') == 'debugInfo') {
      kkasa::getDebugInfo();
      ajax::success();
    } elseif(init('action') == 'createCmd') {
      $id = init('id');
      $device = eqLogic::byId($id);
      switch(init('cmdType'))
      {
        case 'basic':
          $device->addBasicCmd(init('createcommand'));
          break;
        case 'power':
          $device->addPowerCmd(init('createcommand'));
          break;
        case 'led':
          $device->addLedCmd(init('createcommand'));
          break;
        case 'plug':
          $device->addPlugCmd(init('createcommand'));
          break;
        default:
          ajax::error(__('Type de commandes inconnu',__FILE__));
      }

      ajax::success();
    }



    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
