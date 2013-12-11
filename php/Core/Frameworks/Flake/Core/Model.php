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

abstract class Model extends \Flake\Core\FLObject {
	protected $aData = array();
	
	protected function getData() {
		reset($this->aData);
		return $this->aData;
	}
	
	public function __get($sPropName) {
		return $this->get($sPropName);
	}
	
	public function __isset($name) {
		if(array_key_exists($name, $this->aData)) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function get($sPropName) {
		if(array_key_exists($sPropName, $this->aData)) {
			return $this->aData[$sPropName];
		}
		
		throw new \Exception("\Flake\Core\Model->get(): property " . htmlspecialchars($sPropName) . " does not exist on " . get_class($this));
	}
	
	public function set($sPropName, $sPropValue) {
		if(array_key_exists($sPropName, $this->aData)) {
			$this->aData[$sPropName] = $sPropValue;
			return $this;
		}
		
		throw new \Exception("\Flake\Core\Model->set(): property " . htmlspecialchars($sPropName) . " does not exist on " . get_class($this));
	}
	
	public function label() {
		return $this->get($this::LABELFIELD);
	}
	
	public static function icon() {
		return "icon-book";
	}
	
	public static function mediumicon() {
		return "glyph-book";
	}
	
	public static function bigicon() {
		return "glyph2x-book";
	}
	
	public static function humanName() {
		$aRes = explode("\\", get_called_class());
		return array_pop($aRes);
	}
	
	public function floating() {
		return TRUE;
	}
	
	public function formForThisModelInstance($options = array()) {
		$sClass = get_class($this);
		$oForm = new \Formal\Form($sClass, $options);
		$oForm->setModelInstance($this);
		
		return $oForm;
	}
	
	public function formMorphologyForThisModelInstance() {
		throw new \Exception(get_class($this) . ": No form morphology provided for Model.");
	}
	
	public abstract function persist();
	
	public abstract function destroy();
}