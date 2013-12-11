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

namespace Flake\Core;

abstract class Route extends \Flake\Core\FLObject {
	
	# should be abstract, but is not, due to PHP strict standard
	public static function layout(\Flake\Core\Render\Container &$oRenderContainer) {
		
	}
	public static function parametersMap() {
		return array();
	}
	
	# converts raw url params "a/b/c/d"=[a, b, c, d] in route params [a=>b, c=>d]
	
	public static function getParams() {
		$aRouteParams = array();
		
		$aParametersMap = static::parametersMap();	# static to use method as defined in derived class
		$aURLParams = $GLOBALS["ROUTER"]::getURLParams();
		
		reset($aParametersMap);
		foreach($aParametersMap as $sParam => $aMap) {
			$sURLToken = $sParam;
			
			if(array_key_exists("urltoken", $aMap)) {
				$sURLToken = $aMap["urltoken"];
			}
			
			if(($iPos = array_search($sURLToken, $aURLParams)) !== FALSE) {
				$aRouteParams[$sParam] = $aURLParams[($iPos + 1)];	# the value corresponding to this param is the next one in the URL
			}
		}
		
		reset($aRouteParams);
		return $aRouteParams;
	}
}