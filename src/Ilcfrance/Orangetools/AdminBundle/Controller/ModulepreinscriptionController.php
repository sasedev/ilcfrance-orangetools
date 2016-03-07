<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;
use Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription\NewTForm as ModulepreinscriptionNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription\UpdateTraineeTForm as ModulepreinscriptionUpdateTraineeTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription\UpdateModuleformationTForm as ModulepreinscriptionUpdateModuleformationTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription\UpdateLockoutTForm as ModulepreinscriptionUpdateLockoutTForm;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class ModulepreinscriptionController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_modulepreinscriptions');

	}

	public function listAction()
	{

		$this->addTwigVar('menu_active', 'admin_modulepreinscriptions_list');

		$em = $this->getEntityManager();

		$modulepreinscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->getAll();
		$this->addTwigVar('modulepreinscriptions', $modulepreinscriptions);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Modulepreinscription.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Modulepreinscription.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:list.html.twig', $this->getTwigVars());

	}

	public function listByModuleformationAction($id)
	{

		$this->addTwigVar('menu_active', 'admin_modulepreinscriptions_list');

		$em = $this->getEntityManager();

		$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->find($id);

		if (null == $moduleformation) {
			$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.notfound'));
			$this->redirect($this->generateUrl('ilcfrance_orangetools_admin_modulepreinscription_list'));
		}

		$modulepreinscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->getAllByModuleformation($moduleformation);
		$this->addTwigVar('modulepreinscriptions', $modulepreinscriptions);
		$this->addTwigVar('moduleformation', $moduleformation);

		$this->setBrowserPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Modulepreinscription.listByModuleformation.browserpagetitle',
				array(
					'%moduleformation%' => $moduleformation->getCode()
				)));
		$this->setPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Modulepreinscription.listByModuleformation.pagetitle',
				array(
					'%moduleformation%' => $moduleformation->getCode()
				)));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:listByModuleformation.html.twig', $this->getTwigVars());

	}

	public function listByTraineeAction($id)
	{

		$this->addTwigVar('menu_active', 'admin_modulepreinscriptions_list');

		$em = $this->getEntityManager();

		$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

		if (null == $trainee) {
			$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.notfound'));
			$this->redirect($this->generateUrl('ilcfrance_orangetools_admin_modulepreinscription_list'));
		}

		$modulepreinscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->getAllByUser($trainee);
		$this->addTwigVar('modulepreinscriptions', $modulepreinscriptions);
		$this->addTwigVar('trainee', $trainee);

		$this->setBrowserPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Modulepreinscription.listByTrainee.browserpagetitle',
				array(
					'%user%' => $trainee->getFullName()
				)));
		$this->setPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Modulepreinscription.listByTrainee.pagetitle',
				array(
					'%user%' => $trainee->getFullName()
				)));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:listByTrainee.html.twig', $this->getTwigVars());

	}

	public function addGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_modulepreinscriptions_add');

		$modulepreinscription = new Modulepreinscription();
		$modulepreinscriptionNewForm = $this->createForm(ModulepreinscriptionNewTForm::class, $modulepreinscription);

		$this->addTwigVar('modulepreinscription', $modulepreinscription);
		$this->addTwigVar('ModulepreinscriptionNewForm', $modulepreinscriptionNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_modulepreinscriptions_add');

		$modulepreinscription = new Modulepreinscription();
		$modulepreinscriptionNewForm = $this->createForm(ModulepreinscriptionNewTForm::class, $modulepreinscription);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['ModulepreinscriptionNewForm'])) {
			$modulepreinscriptionNewForm->handleRequest($request);
			if ($modulepreinscriptionNewForm->isValid()) {
				$em = $this->getEntityManager();

				$em->persist($modulepreinscription);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Modulepreinscription.add.success',
						array(
							'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
							'%user%' => $modulepreinscription->getUser()->getFullName()
						)));

				return $this->redirect(
					$this->generateUrl('ilcfrance_orangetools_admin_modulepreinscription_editGet', array(
						'id' => $modulepreinscription->getId()
					)));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.failure'));
			}
		}

		$this->addTwigVar('modulepreinscription', $modulepreinscription);
		$this->addTwigVar('ModulepreinscriptionNewForm', $modulepreinscriptionNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:add.html.twig', $this->getTwigVars());

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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_modulepreinscription_list');
		}
		$em = $this->getEntityManager();
		try {
			$modulepreinscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->find($id);

			if (null == $modulepreinscription) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.delete.notfound'));
			} else {
				$em->remove($modulepreinscription);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Modulepreinscription.delete.success',
						array(
							'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
							'%user%' => $modulepreinscription->getUser()->getFullName()
						)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.delete.failure'));
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_modulepreinscription_list');
		}

		$em = $this->getEntityManager();
		try {
			$modulepreinscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->find($id);

			if (null == $modulepreinscription) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.edit.notfound'));
			} else {
				$modulepreinscriptionUpdateModuleformationForm = $this->createForm(ModulepreinscriptionUpdateModuleformationTForm::class, $modulepreinscription);
				$modulepreinscriptionUpdateTraineeForm = $this->createForm(ModulepreinscriptionUpdateTraineeTForm::class, $modulepreinscription);
				$modulepreinscriptionUpdateLockoutForm = $this->createForm(ModulepreinscriptionUpdateLockoutTForm::class, $modulepreinscription);

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$sessioninscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->getAllByUserModuleformation($modulepreinscription->getUser(), $modulepreinscription->getModuleformation());

				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('sessionsinscriptions', $sessioninscriptions);
				$this->addTwigVar('ModulepreinscriptionUpdateModuleformationForm', $modulepreinscriptionUpdateModuleformationForm->createView());
				$this->addTwigVar('ModulepreinscriptionUpdateTraineeForm', $modulepreinscriptionUpdateTraineeForm->createView());
				$this->addTwigVar('ModulepreinscriptionUpdateLockoutForm', $modulepreinscriptionUpdateLockoutForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Modulepreinscription.edit.browserpagetitle',
						array(
							'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
							'%user%' => $modulepreinscription->getUser()->getFullName()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Modulepreinscription.edit.pagetitle',
						array(
							'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
							'%user%' => $modulepreinscription->getUser()->getFullName()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$modulepreinscription->getId(),
					Trace::AE_Modulepreinscription);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:edit.html.twig', $this->getTwigVars());
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_modulepreinscription_list');
		}

		$em = $this->getEntityManager();
		try {
			$modulepreinscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->find($id);

			if (null == $modulepreinscription) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.edit.notfound'));
			} else {
				$modulepreinscriptionUpdateModuleformationForm = $this->createForm(ModulepreinscriptionUpdateModuleformationTForm::class, $modulepreinscription);
				$modulepreinscriptionUpdateTraineeForm = $this->createForm(ModulepreinscriptionUpdateTraineeTForm::class, $modulepreinscription);
				$modulepreinscriptionUpdateLockoutForm = $this->createForm(ModulepreinscriptionUpdateLockoutTForm::class, $modulepreinscription);

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['ModulepreinscriptionUpdateModuleformationForm'])) {
					$modulepreinscriptionUpdateModuleformationForm->handleRequest($request);
					if ($modulepreinscriptionUpdateModuleformationForm->isValid()) {
						$em->persist($modulepreinscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Modulepreinscription.edit.success',
								array(
									'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
									'%user%' => $modulepreinscription->getUser()->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_modulepreinscription_editGet',
								array(
									'id' => $modulepreinscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($modulepreinscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.edit.failure'));
					}
				} elseif (isset($reqData['ModulepreinscriptionUpdateTraineeForm'])) {
					$modulepreinscriptionUpdateTraineeForm->handleRequest($request);
					if ($modulepreinscriptionUpdateTraineeForm->isValid()) {
						$em->persist($modulepreinscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Modulepreinscription.edit.success',
								array(
									'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
									'%user%' => $modulepreinscription->getUser()->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_modulepreinscription_editGet',
								array(
									'id' => $modulepreinscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($modulepreinscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.edit.failure'));
					}
				} elseif (isset($reqData['ModulepreinscriptionUpdateLockoutForm'])) {
					$modulepreinscriptionUpdateLockoutForm->handleRequest($request);
					if ($modulepreinscriptionUpdateLockoutForm->isValid()) {
						$em->persist($modulepreinscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Modulepreinscription.edit.success',
								array(
									'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
									'%user%' => $modulepreinscription->getUser()->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_modulepreinscription_editGet',
								array(
									'id' => $modulepreinscription->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($modulepreinscription);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.edit.failure'));
					}
				}

				$sessioninscriptions = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->getAllByUserModuleformation($modulepreinscription->getUser(), $modulepreinscription->getModuleformation());

				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('sessionsinscriptions', $sessioninscriptions);
				$this->addTwigVar('ModulepreinscriptionUpdateModuleformationForm', $modulepreinscriptionUpdateModuleformationForm->createView());
				$this->addTwigVar('ModulepreinscriptionUpdateTraineeForm', $modulepreinscriptionUpdateTraineeForm->createView());
				$this->addTwigVar('ModulepreinscriptionUpdateLockoutForm', $modulepreinscriptionUpdateLockoutForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Modulepreinscription.edit.browserpagetitle',
						array(
							'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
							'%user%' => $modulepreinscription->getUser()->getFullName()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Modulepreinscription.edit.pagetitle',
						array(
							'%moduleformation%' => $modulepreinscription->getModuleformation()->getCode(),
							'%user%' => $modulepreinscription->getUser()->getFullName()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$modulepreinscription->getId(),
					Trace::AE_Modulepreinscription);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Modulepreinscription:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

}