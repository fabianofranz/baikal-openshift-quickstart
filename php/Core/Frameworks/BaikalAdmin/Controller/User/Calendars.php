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

namespace BaikalAdmin\Controller\User;

class Calendars extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;	# \Baikal\Model\Calendar 
	protected $oUser;	# \Baikal\Model\User
	protected $oForm;	# \Formal\Form

	public function execute() {
		
		if(($iUser = $this->currentUserId()) === FALSE) {
			throw new \Exception("BaikalAdmin\Controller\User\Calendars::render(): User get-parameter not found.");
		}

		$this->oUser = new \Baikal\Model\User($iUser);

		if($this->actionNewRequested()) {
			$this->actionNew();
		} elseif($this->actionEditRequested()) {
			$this->actionEdit();
		} elseif($this->actionDeleteRequested()) {
			$this->actionDelete();
		}
	}

	public function render() {
		
		$oView = new \BaikalAdmin\View\User\Calendars();
		
		# User
		$oView->setData("user", $this->oUser);
		
		# List of calendars
		$oCalendars = $this->oUser->getCalendarsBaseRequester()->execute();
		$aCalendars = array();
		
		foreach($oCalendars as $calendar) {
			$aCalendars[] = array(
				"linkedit" => $this->linkEdit($calendar),
				"linkdelete" => $this->linkDelete($calendar),
				"icon" => $calendar->icon(),
				"label" => $calendar->label(),
				"description" => $calendar->get("description"),
			);
		}
		
		$oView->setData("calendars", $aCalendars);
		
		# Messages
		$sMessages = implode("\n", $this->aMessages);
		$oView->setData("messages", $sMessages);

		if($this->actionNewRequested() || $this->actionEditRequested()) {
			$sForm = $this->oForm->render();
		} else {
			$sForm = "";
		}
		
		$oView->setData("form", $sForm);
		$oView->setData("titleicon", \Baikal\Model\Calendar::bigicon());
		$oView->setData("modelicon", $this->oUser->mediumicon());
		$oView->setData("modellabel", $this->oUser->label());
		$oView->setData("linkback", \BaikalAdmin\Controller\Users::link());
		$oView->setData("linknew", $this->linkNew());
		$oView->setData("calendaricon", \Baikal\Model\Calendar::icon());
		
		return $oView->render();
	}
	
	protected function initForm() {
		if($this->actionEditRequested() || $this->actionNewRequested()) {
			$aOptions = array(
				"closeurl" => $this->linkHome()
			);
			
			$this->oForm = $this->oModel->formForThisModelInstance($aOptions);
		}
	}
	
	protected function currentUserId() {
		$aParams = $this->getParams();
		if(($iUser = intval($aParams["user"])) === 0) {
			return FALSE;
		}
		
		return $iUser;
	}
	
	# Action new 
	
	public function linkNew() {
		return self::buildRoute(array(
			"user" => $this->currentUserId(),
			"new" => 1
		)) . "#form";
	}
	
	protected function actionNewRequested() {
		$aParams = $this->getParams();
		if(array_key_exists("new", $aParams) && intval($aParams["new"]) === 1) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionNew() {

		# Building floating model object
		$this->oModel = new \Baikal\Model\Calendar();
		$this->oModel->set(
			"principaluri",
			$this->oUser->get("uri")
		);
		
		$this->oModel->set(
			"components",
			"VEVENT"
		);
		
		$this->oModel->set(
			"ctag",
			"1"
		);
		
		# Initialize corresponding form
		$this->initForm();
		
		# Process form
		if($this->oForm->submitted()) {
			$this->oForm->execute();
			
			if($this->oForm->persisted()) {
				$this->oForm->setOption(
					"action",
					$this->linkEdit(
						$this->oForm->modelInstance()
					)
				);
			}
		}
	}
	
	# Action edit
	
	public function linkEdit(\Baikal\Model\Calendar $oModel) {
		return self::buildRoute(array(
			"user" => $this->currentUserId(),
			"edit" => $oModel->get("id")
		)) . "#form";
	}
	
	protected function actionEditRequested() {
		$aParams = $this->getParams();
		if(array_key_exists("edit", $aParams) && intval($aParams["edit"]) > 0) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionEdit() {
		# Building anchored model object
		$aParams = $this->getParams();
		$this->oModel = new \Baikal\Model\Calendar(intval($aParams["edit"]));
		
		# Initialize corresponding form
		$this->initForm();
		
		# Process form
		if($this->oForm->submitted()) {
			$this->oForm->execute();
		}
	}
	
	# Action delete + confirm
	
	public function linkDelete(\Baikal\Model\Calendar $oModel) {
		return self::buildRoute(array(
			"user" => $this->currentUserId(),
			"delete" => $oModel->get("id")
		)) . "#message";
	}
	
	public function linkDeleteConfirm(\Baikal\Model\Calendar $oModel) {
		return self::buildRoute(array(
			"user" => $this->currentUserId(),
			"delete" => $oModel->get("id"),
			"confirm" => 1
		)) . "#message";
	}
	
	protected function actionDeleteRequested() {
		$aParams = $this->getParams();
		if(array_key_exists("delete", $aParams) && intval($aParams["delete"]) > 0) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionDeleteConfirmed() {
		if($this->actionDeleteRequested() === FALSE) {
			return FALSE;
		}
		
		$aParams = $this->getParams();
		if(array_key_exists("confirm", $aParams) && intval($aParams["confirm"]) === 1) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	protected function actionDelete() {
		
		$aParams = $this->getParams();
		$iCalendar = intval($aParams["delete"]);
		
		if($this->actionDeleteConfirmed() !== FALSE) {
			
			# catching Exception thrown when model already destroyed
				# happens when user refreshes page on delete-URL, for instance
			
			try {
				$oModel = new \Baikal\Model\Calendar($iCalendar);
				$oModel->destroy();				
			} catch(\Exception $e) {
				# already deleted; silently discarding
			}
			
			# Redirecting to admin home
			\Flake\Util\Tools::redirectUsingMeta($this->linkHome());
		} else {
			
			$oModel = new \Baikal\Model\Calendar($iCalendar);
			$this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
				"Check twice, you're about to delete " . $oModel->label() . "</strong> from the database !",
				"<p>You are about to delete a calendar and all it's scheduled events. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
				$this->linkDeleteConfirm($oModel),
				"Delete <strong><i class='" . $oModel->icon() . " icon-white'></i> " . $oModel->label() . "</strong>",
				$this->linkHome()
			);
		}
	}
	
	# Link to home
	
	public function linkHome() {
		return self::buildRoute(array(
			"user" => $this->currentUserId(),
		));
	}
}