<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;

class DefaultController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin');

	}

	public function indexAction()
	{

		if ($this->isGranted('ROLE_TRAINEE')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}
		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAll();
		$this->addTwigVar('traces', \array_reverse($traces->toArray()));

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.homepage.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.homepage.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Default:index.html.twig', $this->getTwigVars());

	}

}
