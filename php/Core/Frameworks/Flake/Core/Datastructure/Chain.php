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

namespace Flake\Core\Datastructure;

class Chain extends \SplDoublyLinkedList {
	
	public function push(\Flake\Core\Datastructure\Chainable $value) {
		$value->chain($this, $this->count());
		parent::push($value);
	}
	
	public function offsetUnset($offset) {
		throw new \Exception("Cannot delete Chainable in Chain");
	}
	
	public function &first() {
		$oRes = $this->bottom();
		return $oRes;
	}
	
	public function &last() {
		$oRes = $this->top();
		return $oRes;
	}
	
	public function reset() {
		reset($this);
	}
	
	public function __toString() {
		ob_start();
		var_dump($this);
		$sDump = ob_get_contents();
		ob_end_clean();
		
		return "<pre>" . htmlspecialchars($sDump) . "</pre>";
	}
}