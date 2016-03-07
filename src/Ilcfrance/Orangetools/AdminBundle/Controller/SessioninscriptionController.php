<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\NewTForm as SessioninscriptionNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\UpdateSessionformationTForm as SessioninscriptionUpdateSessionformationTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\UpdateTraineeTForm as SessioninscriptionUpdateTraineeTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\UpdateConvocationTForm as SessioninscriptionUpdateConvocationTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\SendConvocationTForm as SessioninscriptionSendConvocationTForm;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class SessioninscriptionController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_sessioninscriptions');

	}

	public function listAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessioninscriptions_list');

		$em = $this->getEntityManager();

		$sessioninscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->getAll();
		$this->addTwigVar('sessioninscriptions', $sessioninscriptions);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessioninscription.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessioninscription.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:list.html.twig', $this->getTwigVars());

	}

	public function listBySessionformationAction($id)
	{

		$this->addTwigVar('menu_active', 'admin_sessioninscriptions_list');

		$em = $this->getEntityManager();

		$sessionformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->find($id);

		if (null == $sessionformation) {
			$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.notfound'));
			$this->redirect($this->generateUrl('ilcfrance_orangetools_admin_sessioninscription_list'));
		}

		$sessioninscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->getAllBySessionformation($sessionformation);
		$this->addTwigVar('sessioninscriptions', $sessioninscriptions);
		$this->addTwigVar('sessionformation', $sessionformation);

		$this->setBrowserPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Sessioninscription.listBySessionformation.browserpagetitle',
				array(
					'%sessionformation%' => $sessionformation->getCode()
				)));
		$this->setPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Sessioninscription.listBySessionformation.pagetitle',
				array(
					'%sessionformation%' => $sessionformation->getCode()
				)));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:listBySessionformation.html.twig', $this->getTwigVars());

	}

	public function listByTraineeAction($id)
	{

		$this->addTwigVar('menu_active', 'admin_sessioninscriptions_list');

		$em = $this->getEntityManager();

		$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

		if (null == $trainee) {
			$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.notfound'));
			$this->redirect($this->generateUrl('ilcfrance_orangetools_admin_sessioninscription_list'));
		}

		$sessioninscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->getAllByUser($trainee);
		$this->addTwigVar('sessioninscriptions', $sessioninscriptions);
		$this->addTwigVar('trainee', $trainee);

		$this->setBrowserPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Sessioninscription.listByTrainee.browserpagetitle',
				array(
					'%user%' => $trainee->getFullName()
				)));
		$this->setPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Sessioninscription.listByTrainee.pagetitle',
				array(
					'%user%' => $trainee->getFullName()
				)));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:listByTrainee.html.twig', $this->getTwigVars());

	}

	public function addGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessioninscriptions_add');

		$sessioninscription = new Sessioninscription();
		$sessioninscriptionNewForm = $this->createForm(SessioninscriptionNewTForm::class, $sessioninscription);

		$this->addTwigVar('sessioninscription', $sessioninscription);
		$this->addTwigVar('SessioninscriptionNewForm', $sessioninscriptionNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessioninscription.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessioninscription.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessioninscriptions_add');

		$sessioninscription = new Sessioninscription();
		$sessioninscriptionNewForm = $this->createForm(SessioninscriptionNewTForm::class, $sessioninscription);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['SessioninscriptionNewForm'])) {
			$sessioninscriptionNewForm->handleRequest($request);
			if ($sessioninscriptionNewForm->isValid()) {
				$em = $this->getEntityManager();

				$em->persist($sessioninscription);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Sessioninscription.add.success',
						array(
							'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
							'%user%' => $sessioninscription->getUser()->getFullName()
						)));

				return $this->redirect(
					$this->generateUrl('ilcfrance_orangetools_admin_sessioninscription_editGet', array(
						'id' => $sessioninscription->getId()
					)));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.add.failure'));
			}
		}

		$this->addTwigVar('sessioninscription', $sessioninscription);
		$this->addTwigVar('SessioninscriptionNewForm', $sessioninscriptionNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessioninscription.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessioninscription.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:add.html.twig', $this->getTwigVars());

	}

	public function deleteAction($id)
	{

		/*
		 * if (! $this->hasRole('ROLE_SUPERADMIN')) {
		 * return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		 * }
		 */
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_sessionformation_list');
		}
		$em = $this->getEntityManager();
		try {
			$sessioninscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->find($id);

			if (null == $sessioninscription) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.delete.notfound'));
			} else {
				$em->remove($sessioninscription);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Sessioninscription.delete.success',
						array(
							'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
							'%user%' => $sessioninscription->getUser()->getFullName()
						)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.delete.failure'));
		}

		return $this->redirect($urlFrom);

	}

	public function editGetAction($id)
	{

		/*
		 * if (! $this->hasRole('ROLE_SUPERADMIN')) {
		 * return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		 * }
		 */
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_sessioninscription_list');
		}

		$em = $this->getEntityManager();
		try {
			$sessioninscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->find($id);

			if (null == $sessioninscription) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.edit.notfound'));
			} else {
				$sessioninscriptionUpdateSessionformationForm = $this->createForm(SessioninscriptionUpdateSessionformationTForm::class, $sessioninscription);
				$sessioninscriptionUpdateTraineeForm = $this->createForm(SessioninscriptionUpdateTraineeTForm::class, $sessioninscription);
				$sessioninscriptionUpdateConvocationForm = $this->createForm(SessioninscriptionUpdateConvocationTForm::class, $sessioninscription);
				$sessioninscriptionSendConvocationForm = $this->createForm(SessioninscriptionSendConvocationTForm::class, $sessioninscription);

				$modulepreinscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->findOneBy(array('moduleformation' => $sessioninscription->getSessionformation()->getModuleformation(), 'user' => $sessioninscription->getUser()));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$this->addTwigVar('sessioninscription', $sessioninscription);
				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('SessioninscriptionUpdateSessionformationForm', $sessioninscriptionUpdateSessionformationForm->createView());
				$this->addTwigVar('SessioninscriptionUpdateTraineeForm', $sessioninscriptionUpdateTraineeForm->createView());
				$this->addTwigVar('SessioninscriptionUpdateConvocationForm', $sessioninscriptionUpdateConvocationForm->createView());
				$this->addTwigVar('SessioninscriptionSendConvocationForm', $sessioninscriptionSendConvocationForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessioninscription.edit.browserpagetitle',
						array(
							'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
							'%user%' => $sessioninscription->getUser()->getFullName()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessioninscription.edit.pagetitle',
						array(
							'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
							'%user%' => $sessioninscription->getUser()->getFullName()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$sessioninscription->getId(),
					Trace::AE_Sessioninscription);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

	public function editPostAction($id)
	{

		/*
		 * if (! $this->hasRole('ROLE_SUPERADMIN')) {
		 * return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		 * }
		 */
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_sessioninscription_list');
		}

		$em = $this->getEntityManager();
		try {
			$sessioninscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->find($id);

			if (null == $sessioninscription) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.edit.notfound'));
			} else {
				$sessioninscriptionUpdateSessionformationForm = $this->createForm(SessioninscriptionUpdateSessionformationTForm::class, $sessioninscription);
				$sessioninscriptionUpdateTraineeForm = $this->createForm(SessioninscriptionUpdateTraineeTForm::class, $sessioninscription);
				$sessioninscriptionUpdateConvocationForm = $this->createForm(SessioninscriptionUpdateConvocationTForm::class, $sessioninscription);
				$sessioninscriptionSendConvocationForm = $this->createForm(SessioninscriptionSendConvocationTForm::class, $sessioninscription);

				$modulepreinscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->findOneBy(array('moduleformation' => $sessioninscription->getSessionformation()->getModuleformation(), 'user' => $sessioninscription->getUser()));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['SessioninscriptionUpdateSessionformationForm'])) {
					$sessioninscriptionUpdateSessionformationForm->handleRequest($request);
					if ($sessioninscriptionUpdateSessionformationForm->isValid()) {
						$em->persist($sessioninscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessioninscription.edit.success',
								array(
									'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
									'%user%' => $sessioninscription->getUser()->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessioninscription_editGet',
								array(
									'id' => $sessioninscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessioninscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.edit.failure'));
					}
				} elseif (isset($reqData['SessioninscriptionUpdateTraineeForm'])) {
					$sessioninscriptionUpdateTraineeForm->handleRequest($request);
					if ($sessioninscriptionUpdateTraineeForm->isValid()) {
						$em->persist($sessioninscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessioninscription.edit.success',
								array(
									'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
									'%user%' => $sessioninscription->getUser()->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessioninscription_editGet',
								array(
									'id' => $sessioninscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessioninscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.edit.failure'));
					}
				} elseif (isset($reqData['SessioninscriptionUpdateConvocationForm'])) {
					$sessioninscriptionUpdateConvocationForm->handleRequest($request);
					if ($sessioninscriptionUpdateConvocationForm->isValid()) {
						$em->persist($sessioninscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessioninscription.edit.success',
								array(
									'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
									'%user%' => $sessioninscription->getUser()->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessioninscription_editGet',
								array(
									'id' => $sessioninscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessioninscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.edit.failure'));
					}
				} elseif (isset($reqData['SessioninscriptionSendConvocationForm'])) {
					$sessioninscriptionSendConvocationForm->handleRequest($request);
					if ($sessioninscriptionSendConvocationForm->isValid()) {
						$countSessioninscriptions = 0;
						$mailsent = 0;

						$trainee = $sessioninscription->getUser();

						$countSessioninscriptions++;

						$mvars = array();
						$message = \Swift_Message::newInstance();
						$mvars['user'] = $trainee;
						$mvars['sessionformation'] = $sessioninscription->getSessionformation();
						$mvars ['plan'] = $message->embed (
							\Swift_Image::fromPath ($this->getParameter('kernel.root_dir') . '/../web/images/plan.jpg'));

						$from = $this->getParameter('mail_from');
						$fromName = $this->getParameter('mail_from_name');
						$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.admin.Sessioninscription.mail.convocation.subject');
						$message->setFrom($from, $fromName)
						->setTo($trainee->getEmail(), $trainee->getFullname())
						->setBcc($trainee->getEmail2(), $trainee->getFullName2())
						->setSubject($subject)
						->setBody($this->renderView('IlcfranceOrangetoolsAdminBundle:Sessioninscription:sendConvocation.mail.html.twig', $mvars), 'text/html');

						$this->sendmail($message);
						$sessioninscription->setConvocation(Sessioninscription::CONVOCATION_SENT);
						$em->persist($sessioninscription);

						$modulepreinscription->setLockout(Modulepreinscription::LOCKOUT_LOCKED);
						$em->persist($modulepreinscription);

						$mailsent++;

						$em->flush();

						$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.mail.convocation.sent', array('%countSessioninscriptions%' => $countSessioninscriptions, '%$mailsent%' => $mailsent)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessioninscription_editGet',
								array(
									'id' => $sessioninscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 1);
						$this->getSession()->set('tabActive', 1);

						$em->refresh($sessioninscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.edit.failure'));
					}
				}

				$this->addTwigVar('sessioninscription', $sessioninscription);
				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('SessioninscriptionUpdateSessionformationForm', $sessioninscriptionUpdateSessionformationForm->createView());
				$this->addTwigVar('SessioninscriptionUpdateTraineeForm', $sessioninscriptionUpdateTraineeForm->createView());
				$this->addTwigVar('SessioninscriptionUpdateConvocationForm', $sessioninscriptionUpdateConvocationForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessioninscription.edit.browserpagetitle',
						array(
							'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
							'%user%' => $sessioninscription->getUser()->getFullName()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessioninscription.edit.pagetitle',
						array(
							'%sessionformation%' => $sessioninscription->getSessionformation()->getCode(),
							'%user%' => $sessioninscription->getUser()->getFullName()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$sessioninscription->getId(),
					Trace::AE_Sessioninscription);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Sessioninscription:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

}