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

	if (init('action') == 'syncDevices') {
		if (hydrao::syncDevices()) {
			ajax::success();
		} else {
			ajax::error(__('Erreur lors de la synchronisation, vérifiez le log', __FILE__), 1);
		}
	} elseif (init('action') == 'createCommands') {
		/**
		 * @var hydrao
		 */
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('eqLogic non trouvé : ', __FILE__) . init('id'));
		}

		if ($eqLogic->createCommands(true)) {
			ajax::success();
		} else {
			ajax::error(__('Erreur lors de la création des commandes', __FILE__), 1);
		}
	} elseif (init('action') == 'getImage') {
		/**
		 * @var hydrao
		 */
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('eqLogic non trouvé : ', __FILE__) . init('id'));
		}
		ajax::success($eqLogic->getImage());
	}

	throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	log::add('hydrao', 'error', $e->getMessage());
	ajax::error(displayException($e), $e->getCode());
}
