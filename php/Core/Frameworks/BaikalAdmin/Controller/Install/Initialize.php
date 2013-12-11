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

namespace BaikalAdmin\Controller\Install;

class Initialize extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;
	protected $oForm;	# \Formal\Form
	
	public function execute() {
		# Assert that /Specific is writable
		if(!file_exists(PROJECT_PATH_SPECIFIC) || !is_dir(PROJECT_PATH_SPECIFIC) || !is_writable(PROJECT_PATH_SPECIFIC)) {
			throw new \Exception("Specific/ dir is readonly. Baïkal Admin requires write permissions on this dir.");
		}

		$this->createHtaccessFilesIfNeeded();
		
		$this->oModel = new \Baikal\Model\Config\Standard(PROJECT_PATH_SPECIFIC . "config.php");
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE
		));
		
		if($this->oForm->submitted()) {
			$this->oForm->execute();
			
			if($this->oForm->persisted()) {

				# Creating system config, and initializing BAIKAL_ENCRYPTION_KEY
				$oSystemConfig = new \Baikal\Model\Config\System(PROJECT_PATH_SPECIFIC . "config.system.php");
				$oSystemConfig->set("BAIKAL_ENCRYPTION_KEY",  md5(microtime() . rand()));

				# Default: PDO::SQLite or PDO::MySQL ?
				$aPDODrivers = \PDO::getAvailableDrivers();
				if(!in_array('sqlite', $aPDODrivers)) {	# PDO::MySQL is already asserted in \Baikal\Core\Tools::assertEnvironmentIsOk()
					$oSystemConfig->set("PROJECT_DB_MYSQL",  TRUE);
				}

				$oSystemConfig->persist();

				# Using default PROJECT_SQLITE_FILE
				$PROJECT_SQLITE_FILE = PROJECT_PATH_SPECIFIC . "db/db.sqlite";

				if(!file_exists($PROJECT_SQLITE_FILE)) {
					# Installing default sqlite database
					@copy(PROJECT_PATH_CORERESOURCES . "Db/SQLite/db.sqlite", $PROJECT_SQLITE_FILE);
				}
			}
		}
	}

	public function render() {
		$sBigIcon = "glyph2x-magic";
		$sBaikalVersion = BAIKAL_VERSION;
		
		$oView = new \BaikalAdmin\View\Install\Initialize();
		$oView->setData("baikalversion", BAIKAL_VERSION);
		
		if($this->oForm->persisted()) {
			$sLink = PROJECT_URI . "admin/install/?/database";
			\Flake\Util\Tools::redirect($sLink);
			exit(0);

			#$sMessage = "<p>Baïkal is now configured. You may <a class='btn btn-success' href='" . PROJECT_URI . "admin/'>Access the Baïkal admin</a></p>";
			#$sForm = "";
		} else {
			$sMessage = "";
			$sForm = $this->oForm->render();
		}
		
		$oView->setData("message", $sMessage);
		$oView->setData("form", $sForm);
		
		return $oView->render();
	}
	
	protected function createHtaccessFilesIfNeeded() {

		if(!file_exists(PROJECT_PATH_DOCUMENTROOT . ".htaccess")) {
			@copy(PROJECT_PATH_CORERESOURCES . "System/htaccess-documentroot", PROJECT_PATH_DOCUMENTROOT . ".htaccess");
		}
		
		if(!file_exists(PROJECT_PATH_DOCUMENTROOT . ".htaccess")) {
			throw new \Exception("Unable to create " . PROJECT_PATH_DOCUMENTROOT . ".htaccess; you may try to create it manually by copying " . PROJECT_PATH_CORERESOURCES . "System/htaccess-documentroot");
		}
		
		if(!file_exists(PROJECT_PATH_SPECIFIC . ".htaccess")) {
			@copy(PROJECT_PATH_CORERESOURCES . "System/htaccess-specific", PROJECT_PATH_SPECIFIC . ".htaccess");
		}
		
		if(!file_exists(PROJECT_PATH_SPECIFIC . ".htaccess")) {
			throw new \Exception("Unable to create " . PROJECT_PATH_SPECIFIC . ".htaccess; you may try to create it manually by copying " . PROJECT_PATH_CORERESOURCES . "System/htaccess-specific");
		}
	}
}