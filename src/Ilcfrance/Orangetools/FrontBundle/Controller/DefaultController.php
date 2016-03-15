<?php

namespace Ilcfrance\Orangetools\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\FrontBundle\Form\SessioninscriptionNewTForm;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;

class DefaultController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'front');

	}

	public function indexAction()
	{

		if ($this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}

		$sc = $this->getSecurityTokenStorage();
		$user = $sc->getToken()->getUser();

		$em = $this->getEntityManager();
		$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($user->getId());

		$tempSessioninscriptionNewForms = array();

		foreach ($trainee->getModulepreinscriptions() as $modulepreinscription) {
			$moduleformation = $modulepreinscription->getModuleformation();
			if (\count($moduleformation->getSessionformations()) > 0 && $modulepreinscription->getLockout() == Modulepreinscription::LOCKOUT_UNLOCKED) {
				$sessioninscription = new Sessioninscription();
				$sessioninscription->setUser($trainee);

				$sessioninscriptionNewForm = $this->createForm(SessioninscriptionNewTForm::class, $sessioninscription, array('moduleformation' => $moduleformation, 'user' => $trainee));

				$tempSessioninscriptionNewForms[$moduleformation->getId()] = $sessioninscriptionNewForm;
			}
		}

		$sessioninscriptionNewForms = array();

		foreach ($tempSessioninscriptionNewForms as $key => $sessioninscriptionNewForm) {
			$sessioninscriptionNewForms[$key] = $sessioninscriptionNewForm->createView();
		}

		$this->addTwigVar('trainee', $trainee);

		$this->addTwigVar('SessioninscriptionNewForms', $sessioninscriptionNewForms);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.homepage.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.homepage.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:index.html.twig', $this->getTwigVars());

	}

	public function indexPostAction()
	{

		if ($this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}

		$sc = $this->getSecurityTokenStorage();
		$user = $sc->getToken()->getUser();

		$em = $this->getEntityManager();
		$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($user->getId());

		$tempSessioninscriptionNewForms = array();

		foreach ($trainee->getModulepreinscriptions() as $modulepreinscription) {
			$moduleformation = $modulepreinscription->getModuleformation();
			if (\count($moduleformation->getSessionformations()) > 0 && $modulepreinscription->getLockout() == Modulepreinscription::LOCKOUT_UNLOCKED) {
				$sessioninscription = new Sessioninscription();
				$sessioninscription->setUser($trainee);

				$sessioninscriptionNewForm = $this->createForm(SessioninscriptionNewTForm::class, $sessioninscription, array('moduleformation' => $moduleformation, 'user' => $trainee));

				$tempSessioninscriptionNewForms[$moduleformation->getId()] = $sessioninscriptionNewForm;
			}
		}

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['SessioninscriptionNewForm'])) {
			if (isset($reqData['SessioninscriptionNewForm']['moduleformation'])) {
				$moduleformation_id = $reqData['SessioninscriptionNewForm']['moduleformation'];
				foreach ($tempSessioninscriptionNewForms as $key => $sessioninscriptionNewForm) {
					if ($moduleformation_id == $key) {
						$sessioninscriptionNewForm->handleRequest($request);
						if ($sessioninscriptionNewForm->isValid()) {
							$sessioninscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->getAllByUserModuleformationId($trainee, $moduleformation_id);
							foreach ($sessioninscriptions as $delSessioninscription) {
								$em->remove($delSessioninscription);
							}
							$sessioninscription = new Sessioninscription();
							$sessioninscription->setUser($trainee);
							$sessioninscription->setSessionformation($sessioninscriptionNewForm['sessionformation']->getData());

							$em->persist($sessioninscription);
							$em->flush();

							$mvars = array();
							$mvars['sessioninscription'] = $sessioninscription;

							try {

								$from = $this->getParameter('mail_from');
								$fromName = $this->getParameter('mail_from_name');
								$replyTo = $this->getParameter('mail_replay');
								$replyToName = $this->getParameter('mail_replay_name');
								$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.front.Sessioninscription.mail.params.subject');
								$message = \Swift_Message::newInstance()->setFrom($from, $fromName)
								->setReplyTo($replyTo, $replyToName)
								->setTo($trainee->getEmail(), $trainee->getFullname())
								->setSubject($subject)
								->setBody($this->renderView('IlcfranceOrangetoolsFrontBundle:Default:sendConfirmation.mail.html.twig', $mvars), 'text/html');

								$this->sendmail($message);
							} catch (\Exception $e) {
								$logger = $this->getLogger();
								$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
							}

							$this->addFlash(
								'success',
								$this->translate('ilcfrance.orangetools.trainee.Sessioninscription.add.success', array('%sessionformation%' => $sessioninscription->getSessionformation()->getChoiceLabelFull()))
								);

							return $this->redirect($this->generateUrl('ilcfrance_orangetools_front_homepage'));
						} else {

							$this->addFlash(
								'error',
								$this->translate('ilcfrance.orangetools.trainee.Sessioninscription.add.failure')
								);
						}

					}
				}
			}
		}

		$sessioninscriptionNewForms = array();

		foreach ($tempSessioninscriptionNewForms as $key => $sessioninscriptionNewForm) {
			$sessioninscriptionNewForms[$key] = $sessioninscriptionNewForm->createView();
		}

		$this->addTwigVar('trainee', $trainee);

		$this->addTwigVar('SessioninscriptionNewForms', $sessioninscriptionNewForms);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.homepage.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.homepage.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:index.html.twig', $this->getTwigVars());

	}

	public function bookingAction()
	{
		$this->addTwigVar('menu_active', 'booking');

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.booking.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.booking.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:booking.html.twig', $this->getTwigVars());

	}

	public function contactAction()
	{
		$this->addTwigVar('menu_active', 'contact');

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.contact.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.contact.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:contact.html.twig', $this->getTwigVars());

	}

	public function conditionsAction()
	{
		$this->addTwigVar('menu_active', 'conditions');

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.conditions.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.conditions.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:conditions.html.twig', $this->getTwigVars());

	}

	public function faqAction()
	{
		$this->addTwigVar('menu_active', 'faq');

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.faq.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.faq.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:faq.html.twig', $this->getTwigVars());

	}

	public function glossaryAction()
	{
		$this->addTwigVar('menu_active', 'glossary');

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.glossary.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.glossary.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:glossary.html.twig', $this->getTwigVars());

	}

	public function preambleAction()
	{
		$this->addTwigVar('menu_active', 'preamble');

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.front.preamble.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.front.preamble.pagetitle'));

		return $this->render('IlcfranceOrangetoolsFrontBundle:Default:preamble.html.twig', $this->getTwigVars());

	}

}
