<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;

class DefaultController extends SasedevController
{

	public function indexAction()
	{
		if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			if ($this->isGranted('ROLE_ADMIN')) {
				return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
			} elseif ($this->isGranted('ROLE_TRAINEE')) {
				return $this->redirect($this->generateUrl('ilcfrance_orangetools_front_homepage'));
			} else {
				return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_profile'));
			}
		} else {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_profile'));
		}
	}

}
