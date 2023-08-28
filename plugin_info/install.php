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

function hydrao_install() {
}

function hydrao_update() {
    config::remove('autorefresh', 'hydrao');
    try {
        $crons = cron::searchClassAndFunction('hydrao', 'hourlyRefresh');
        if (is_array($crons)) {
            foreach ($crons as $cron) {
                $cron->remove();
            }
        }
    } catch (Exception $e) {
    }

    /** @var hydrao */
    foreach (eqLogic::byType('hydrao', true) as $eqLogic) {
        try {
            if ($eqLogic->getConfiguration('autorefresh') == '') {
                if ($eqLogic->getConfiguration('type') == 'showerHead')
                    $eqLogic->setConfiguration('autorefresh', rand(0, 59) . ' */2 * * *');
                if ($eqLogic->getConfiguration('type') == 'user')
                    $eqLogic->setConfiguration('autorefresh', rand(0, 59) . ' 4 * * *');
                $eqLogic->save(true);
            }
        } catch (Exception $e) {
        }
    }
}

function hydrao_remove() {
    config::remove('syncLimit', 'hydrao');
    config::remove('username', 'hydrao');
    config::remove('password', 'hydrao');
    config::remove('apikey', 'hydrao');
}
