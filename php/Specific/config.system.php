<?php
##############################################################################
#
# Copyright notice
#
# (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
# All rights reserved
#
# http://baikal-server.com
#
# This script is part of the Baïkal Server project. The Baïkal
# Server project is free software; you can redistribute it
# and/or modify it under the terms of the GNU General Public
# License as published by the Free Software Foundation; either
# version 2 of the License, or (at your option) any later version.
#
# The GNU General Public License can be found at
# http://www.gnu.org/copyleft/gpl.html.
#
# This script is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# This copyright notice MUST APPEAR in all copies of the script!
#
##############################################################################

##############################################################################
# System configuration
# Should not be changed, unless YNWYD
#
# RULES
# 0. All folder pathes *must* be suffixed by "/"
# 1. All URIs *must* be suffixed by "/" if pointing to a folder
#

# PATH to SabreDAV
define("BAIKAL_PATH_SABREDAV", PROJECT_PATH_FRAMEWORKS . "SabreDAV/lib/Sabre/");

# If you change this value, you'll have to re-generate passwords for all your users
define("BAIKAL_AUTH_REALM", "BaikalDAV");

# Should begin and end with a "/"
define("BAIKAL_CARD_BASEURI", PROJECT_BASEURI . "card.php/");

# Should begin and end with a "/"
define("BAIKAL_CAL_BASEURI", PROJECT_BASEURI . "cal.php/");

# Define path to Baïkal Database SQLite file
define("PROJECT_SQLITE_FILE", PROJECT_PATH_SPECIFIC . "db/db.sqlite");

# MySQL > Use MySQL instead of SQLite ?
define("PROJECT_DB_MYSQL", TRUE);

# MySQL > Host, including ':portnumber' if port is not the default one (3306)
define("PROJECT_DB_MYSQL_HOST", getenv('OPENSHIFT_MYSQL_DB_HOST'));
;
# MySQL > Database name
define("PROJECT_DB_MYSQL_DBNAME", getenv('OPENSHIFT_APP_NAME'));

# MySQL > Username
define("PROJECT_DB_MYSQL_USERNAME", getenv('OPENSHIFT_MYSQL_DB_USERNAME'));

# MySQL > Password
define("PROJECT_DB_MYSQL_PASSWORD", getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));

# A random 32 bytes key that will be used to encrypt data
define("BAIKAL_ENCRYPTION_KEY", "08f5478aee01badf4f6df5c379e6170a");

# The currently configured Baïkal version
define("BAIKAL_CONFIGURED_VERSION", "0.2.6");
