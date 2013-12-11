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

namespace Flake\Controller;

class Page extends \Flake\Core\Render\Container {
	
	protected $sTitle = "";
	protected $sMetaKeywords = "";
	protected $sMetaDescription = "";
	protected $sTemplatePath = "";
	
	public function __construct($sTemplatePath) {
		$this->sTemplatePath = $sTemplatePath;
	}
	
	public function setTitle($sTitle) {
		$this->sTitle = $sTitle;
	}
	
	public function setMetaKeywords($sKeywords) {
		$this->sMetaKeywords = $sKeywords;
	}
	
	public function setMetaDescription($sDescription) {
		$this->sMetaDescription = $sDescription;
	}
	
	public function getTitle() {
		return $this->sTitle;
	}
	
	public function getMetaKeywords() {
		$sString = str_replace(array("le", "la", "les", "de", "des", "un", "une"), " ", $this->sMetaKeywords);
		$sString = \Flake\Util\Tools::stringToUrlToken($sString);
		return implode(", ", explode("-", $sString));
	}
	
	public function getMetaDescription() {
		return $this->sMetaDescription;
	}
	
	public function setBaseUrl($sBaseUrl) {
		$this->sBaseUrl = $sBaseUrl;
	}
	
	public function getBaseUrl() {
		return $this->sBaseUrl;
	}
	
	public function injectHTTPHeaders() {
		header("Content-Type: text/html; charset=UTF-8");
	}
	
	public function render() {
		$this->execute();
		
		$aRenderedBlocks = $this->renderBlocks();
		$aRenderedBlocks["pagetitle"] = $this->getTitle();
		$aRenderedBlocks["pagemetakeywords"] = $this->getMetaKeywords();
		$aRenderedBlocks["pagemetadescription"] = $this->getMetaDescription();
		$aRenderedBlocks["baseurl"] = $this->getBaseUrl();
		
		$oTemplate = new \Flake\Core\Template($this->sTemplatePath);
		$sHtml = $oTemplate->parse(
			$aRenderedBlocks
		);

		return $sHtml;
	}
	
	public function addCss($sCssAbsPath) {
		
		if(\Flake\Util\Frameworks::enabled("LessPHP")) {
			$sCompiledPath = PATH_buildcss;
			$sFileName = basename($sCssAbsPath);

			$sCompiledFilePath = $sCompiledPath . \Flake\Util\Tools::shortMD5($sFileName) . "_" . $sFileName;

			if(substr(strtolower($sCompiledFilePath), -4) !== ".css") {
				$sCompiledFilePath .= ".css";
			}

			if(!file_exists($sCompiledPath)) {
				@mkdir($sCompiledPath);
				if(!file_exists($sCompiledPath)) {
					die("Page: Cannot create " . $sCompiledPath);
				}
			}

			\Frameworks\LessPHP\Delegate::compileCss($sCssAbsPath, $sCompiledFilePath);
			$sCssUrl = \Flake\Util\Tools::serverToRelativeWebPath($sCompiledFilePath);
		} else {
			$sCssUrl = \Flake\Util\Tools::serverToRelativeWebPath($sCssAbsPath);
		}
		
		$sHtml = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $sCssUrl . "\" media=\"all\"/>";
		$this->zone("head")->addBlock(new \Flake\Controller\HtmlBlock($sHtml));
	}
}