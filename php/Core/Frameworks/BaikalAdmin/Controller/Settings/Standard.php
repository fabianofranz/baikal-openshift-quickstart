<?php
#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal-server.com
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace BaikalAdmin\Controller\Settings;

class Standard extends \Flake\Core\Controller {
		
	public function execute() {
		$this->oModel = new \Baikal\Model\Config\Standard(PROJECT_PATH_SPECIFIC . "config.php");
		
		# Assert that config file is writable
		if(!$this->oModel->writable()) {
			throw new \Exception("Config file is not writable;" . __FILE__ . " > " . __LINE__);
		}
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE
		));
		
		if($this->oForm->submitted()) {
			$this->oForm->execute();
		}
	}

	public function render() {
		
		$oView = new \BaikalAdmin\View\Settings\Standard();
		$oView->setData("form", $this->oForm->render());
		
		return $oView->render();
	}
}