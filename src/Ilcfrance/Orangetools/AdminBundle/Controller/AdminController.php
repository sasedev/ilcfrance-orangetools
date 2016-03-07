<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\AdminBundle\Form\Admin\NewTForm as AdminNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Admin\UpdateEmailTForm as AdminUpdateEmailTForm;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class AdminController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_admins');

	}

	public function listAction()
	{
		$this->addTwigVar('menu_active', 'admin_admins_list');

		$em = $this->getEntityManager();

		$roleAdmin = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(array('name' => 'ROLE_ADMIN'));
		if (null == $roleAdmin) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		}

		$admins = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->getAllByRole($roleAdmin);
		$this->addTwigVar('admins', $admins);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Admin:list.html.twig', $this->getTwigVars());

	}

	public function addGetAction()
	{
		$this->addTwigVar('menu_active', 'admin_admins_add');

		$admin = new User();
		$adminNewForm = $this->createForm(AdminNewTForm::class, $admin);


		$this->addTwigVar('admin', $admin);
		$this->addTwigVar('AdminNewForm', $adminNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Admin:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{
		$this->addTwigVar('menu_active', 'admin_admins_add');

		$admin = new User();
		$adminNewForm = $this->createForm(AdminNewTForm::class, $admin);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['AdminNewForm'])) {
			$adminNewForm->handleRequest($request);
			if ($adminNewForm->isValid()) {
				$em = $this->getEntityManager();

				$roleAdmin = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(array('name' => 'ROLE_ADMIN'));
				if (null == $roleAdmin) {
					return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
				}

				$admin->addUserRole($roleAdmin);

				$em->persist($admin);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate('ilcfrance.orangetools.admin.Admin.add.success', array('%user%' => $admin->getUsername()))
				);

				return $this->redirect(
					$this->generateUrl('ilcfrance_orangetools_admin_admin_editGet', array('id' => $admin->getId()))
				);
			} else {

				$this->addFlash(
					'error',
					$this->translate('ilcfrance.orangetools.admin.Admin.add.failure')
				);
			}
		}


		$this->addTwigVar('admin', $admin);
		$this->addTwigVar('AdminNewForm', $adminNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Admin:add.html.twig', $this->getTwigVars());

	}

	public function deleteAction($id)
	{
		/*if (! $this->hasRole('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		}*/
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_admin_list');
		}
		$em = $this->getEntityManager();
		try {
			$user = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

			if (null == $user) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Admin.delete.notfound'));
			} else {
				$em->remove($user);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate('ilcfrance.orangetools.admin.Admin.delete.success', array('%user%' => $user->getUsername()))
				);
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine().' '.$e->getMessage().' '.$e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Admin.delete.failure'));
		}

		return $this->redirect($urlFrom);

	}

	public function editGetAction($id)
	{
		/*if (! $this->hasRole('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		} */
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_admin_list');
		}

		$em = $this->getEntityManager();
		try {
			$admin = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

			if (null == $admin) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Admin.edit.notfound'));
			} else {
				$is_trainee = false;
				foreach ($admin->getUserRoles() as $role) {
					if ($role->getName() == 'ROLE_TRAINEE') {
						$is_trainee = true;
					}
				}

				if ($is_trainee) {
					return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array('id' => $id)));
				}


				$userUpdateEmailForm = $this->createForm(AdminUpdateEmailTForm::class, $admin);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');

				$this->addTwigVar('admin', $admin);
				$this->addTwigVar('AdminUpdateEmailForm', $userUpdateEmailForm->createView());

				$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.edit.browserpagetitle', array('%user%' => $admin->getUsername())));
				$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.edit.pagetitle', array('%user%' => $admin->getUsername())));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId($admin->getId(), Trace::AE_User);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Admin:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine().' '.$e->getMessage().' '.$e->getTraceAsString());
		}

		return $this->redirect($urlFrom);
	}

	public function editPostAction($id)
	{
		/*if (! $this->hasRole('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		} */
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_admin_list');
		}

		$em = $this->getEntityManager();
		try {
			$admin = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

			if (null == $admin) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Admin.edit.notfound'));
			} else {
				$userUpdateEmailForm = $this->createForm(AdminUpdateEmailTForm::class, $admin);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['AdminUpdateEmailForm'])) {
					$userUpdateEmailForm->handleRequest($request);
					if ($userUpdateEmailForm->isValid()) {
						$em->persist($admin);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate('ilcfrance.orangetools.admin.Admin.edit.success', array('%user%' => $admin->getUsername()))
						);

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_admin_editGet', array('id' => $admin->getId()))
						);
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($admin);

						$this->addFlash(
							'error',
							$this->translate('ilcfrance.orangetools.admin.Admin.edit.failure')
						);
					}
				}

				$this->addTwigVar('admin', $admin);
				$this->addTwigVar('AdminUpdateEmailForm', $userUpdateEmailForm->createView());



				$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.edit.browserpagetitle', array('%user%' => $admin->getUsername())));
				$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Admin.edit.pagetitle', array('%user%' => $admin->getUsername())));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId($admin->getId(), Trace::AE_User);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Admin:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine().' '.$e->getMessage().' '.$e->getTraceAsString());
		}

		return $this->redirect($urlFrom);
	}

}