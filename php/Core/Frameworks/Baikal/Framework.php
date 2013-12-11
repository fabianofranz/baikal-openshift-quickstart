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

namespace Baikal;

class Framework extends \Flake\Core\Framework {
	
	public static function installTool() {	
		if(defined("BAIKAL_CONTEXT_INSTALL") && BAIKAL_CONTEXT_INSTALL === TRUE) {
			# Install tool has been launched and we're already on the install page
			return;
		} else {
			# Install tool has been launched; redirecting user
			$sInstallToolUrl = PROJECT_URI . "admin/install/";
			header("Location: " . $sInstallToolUrl);
			exit(0);
		}
	}
	
	public static function bootstrap() {
		
		# Registering Baikal classloader
		define("BAIKAL_PATH_FRAMEWORKROOT", dirname(__FILE__) . "/");

		\Baikal\Core\Tools::assertEnvironmentIsOk();
		\Baikal\Core\Tools::configureEnvironment();

		# Check that a config file exists
		if(
			!file_exists(PROJECT_PATH_SPECIFIC . "config.php") ||
			!file_exists(PROJECT_PATH_SPECIFIC . "config.system.php")
		) {
			self::installTool();
		} else {
			require_once(PROJECT_PATH_SPECIFIC . "config.php");
			require_once(PROJECT_PATH_SPECIFIC . "config.system.php");
			date_default_timezone_set(PROJECT_TIMEZONE);

			# Check that Baïkal is already configured
			if(!defined("BAIKAL_CONFIGURED_VERSION")) {
				self::installTool();

			} else {

				# Check that running version matches configured version
				if(version_compare(BAIKAL_VERSION, BAIKAL_CONFIGURED_VERSION) > 0) {
					self::installTool();

				} else {

					# Check that admin password is set
					if(!defined("BAIKAL_ADMIN_PASSWORDHASH")) {
						self::installTool();
					}

					\Baikal\Core\Tools::assertBaikalIsOk();

					set_error_handler("\Baikal\Framework::exception_error_handler");
					
				}
			}
		}
		
	}
	
	# Mapping PHP errors to exceptions; needed by SabreDAV
	public static function exception_error_handler($errno, $errstr, $errfile, $errline) {
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
}
