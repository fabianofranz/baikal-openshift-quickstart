<?php
#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
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

namespace Flake\Util;

abstract class Router extends \Flake\Core\FLObject {
	
	static $sURIPath = "";
	
	/* ----------------------- COMMON METHODS ------------------------------*/
	
	private function __construct() {
		# private constructor for static class
	}
	
	public static function getRoutes() {
		reset($GLOBALS["ROUTES"]);
		return $GLOBALS["ROUTES"];
	}
	
	public static function getControllerForRoute($sRoute) {
		return str_replace("\\Route", "\\Controller", self::getRouteClassForRoute($sRoute));
	}
	
	public static function getRouteClassForRoute($sRoute) {
		$aRoutes = $GLOBALS["ROUTER"]::getRoutes();
		return $aRoutes[$sRoute];
	}
	
	public static function getRouteForController($sController) {
		
		if($sController{0} !== "\\") {
			$sController = "\\" . $sController;
		}
		
		$aRoutes = $GLOBALS["ROUTER"]::getRoutes();
		
		reset($aRoutes);
		while(list($sRoute,) = each($aRoutes)) {
			if(str_replace("\\Route", "\\Controller", $aRoutes[$sRoute]) === $sController) {
				return $sRoute;
			}
		}
		
		return FALSE;
	}
	
	public static function route(\Flake\Core\Render\Container &$oRenderContainer) {
		$sRouteClass = $GLOBALS["ROUTER"]::getRouteClassForRoute(
			$GLOBALS["ROUTER"]::getCurrentRoute()
		);
		
		$sRouteClass::layout($oRenderContainer);
	}
	
	public static function buildRouteForController($sController, $aParams = array()) {

		#$aParams = func_get_args();
		#array_shift($aParams);	# stripping $sController
		if(($sRouteForController = $GLOBALS["ROUTER"]::getRouteForController($sController)) === FALSE) {
			throw new \Exception("buildRouteForController '" . htmlspecialchars($sController) . "': no route available.");
		}

		$aRewrittenParams = array();

		$sRouteClass = self::getRouteClassForRoute($sRouteForController);
		$aParametersMap = $sRouteClass::parametersMap();
		reset($aParametersMap);
		foreach($aParametersMap as $sParam => $aMap) {
			if(!array_key_exists($sParam, $aParams)) {
				# if parameter not in parameters map, skip !
				continue;
			}

			$sUrlToken = $sParam;
			if(array_key_exists("urltoken", $aMap)) {
				$sUrlToken = $aMap["urltoken"];
			}

			$aRewrittenParams[$sUrlToken] = $aParams[$sParam];
		}
		
		#array_unshift($aParams, $sRouteForController);	# Injecting route as first param
		#return call_user_func_array($GLOBALS["ROUTER"] . "::buildRoute", $aParams);
		return $GLOBALS["ROUTER"]::buildRoute($sRouteForController, $aRewrittenParams);
	}
	
	public static function buildCurrentRoute(/*[$sParam, $sParam2, ...]*/) {
		$aParams = func_get_args();
		$sCurrentRoute = $GLOBALS["ROUTER"]::getCurrentRoute();
		
		array_unshift($aParams, $sCurrentRoute);	# Injecting route as first param
		return call_user_func_array($GLOBALS["ROUTER"] . "::buildRoute", $aParams);
	}
	
	public static function setURIPath($sURIPath) {
		static::$sURIPath = $sURIPath;
	}
	
	public static function getUriPath() {
		return FLAKE_URIPATH . static::$sURIPath;
	}
	
	/* ----------------------- CHANGING METHODS ----------------------------*/
	
	# this method is likely to change with every Router implementation
	# should be abstract, but is not, because of PHP's strict standards
	public static function buildRoute($sRoute, $aParams/* [, $sParam, $sParam2, ...] */) {

	}
	
	# should be abstract, but is not, because of PHP's strict standards
	public static function getCurrentRoute() {
		
	}
	
	# should be abstract, but is not, because of PHP's strict standards
	public static function getURLParams() {
		
	}
}