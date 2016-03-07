<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Controller;

use Ilcfrance\Orangetools\SecurityBundle\Form\User\UpdateEmailTForm as UserUpdateEmailTForm;
use Ilcfrance\Orangetools\SecurityBundle\Form\User\UpdatePasswordTForm as UserUpdatePasswordTForm;
use Ilcfrance\Orangetools\SecurityBundle\Form\User\UpdateInfosTForm as UserUpdateInfosTForm;
use Ilcfrance\Orangetools\SecurityBundle\Form\User\UpdatePhoneTForm as UserUpdatePhoneTForm;
use Ilcfrance\Orangetools\SecurityBundle\Form\User\UpdateMobileTForm as UserUpdateMobileTForm;
use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class ProfileController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'profile');

	}

	public function profileGetAction()
	{

		$sc = $this->getSecurityTokenStorage();
		$user = $sc->getToken()->getUser();

		$userUpdateEmailForm = $this->createForm(UserUpdateEmailTForm::class, $user);
		$userUpdatePasswordForm = $this->createForm(UserUpdatePasswordTForm::class, $user);
		$userUpdateInfosForm = $this->createForm(UserUpdateInfosTForm::class, $user);
		$userUpdatePhoneForm = $this->createForm(UserUpdatePhoneTForm::class, $user);
		$userUpdateMobileForm = $this->createForm(UserUpdateMobileTForm::class, $user);

		$this->addTwigVar('tabActive', $this->getSession()
			->get('tabActive', 1));
		$this->getSession()->remove('tabActive');

		$this->addTwigVar('user', $user);
		$this->addTwigVar('UserUpdateEmailForm', $userUpdateEmailForm->createView());
		$this->addTwigVar('UserUpdatePasswordForm', $userUpdatePasswordForm->createView());
		$this->addTwigVar('UserUpdateInfosForm', $userUpdateInfosForm->createView());
		$this->addTwigVar('UserUpdatePhoneForm', $userUpdatePhoneForm->createView());
		$this->addTwigVar('UserUpdateMobileForm', $userUpdateMobileForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.profile.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.profile.pagetitle'));

		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllRelatedToUser($user);
		$this->addTwigVar('traces', \array_reverse($traces->toArray()));

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Profile:index.html.twig', $this->getTwigVars());

	}

	public function profilePostAction()
	{

		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			// return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_profile'));
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_security_profile');
		}

		$sc = $this->getSecurityTokenStorage();
		$user = $sc->getToken()->getUser();
		$oldDbpass = $user->getPassword();

		$userUpdateEmailForm = $this->createForm(UserUpdateEmailTForm::class, $user);
		$userUpdatePasswordForm = $this->createForm(UserUpdatePasswordTForm::class, $user);
		$userUpdateInfosForm = $this->createForm(UserUpdateInfosTForm::class, $user);
		$userUpdatePhoneForm = $this->createForm(UserUpdatePhoneTForm::class, $user);
		$userUpdateMobileForm = $this->createForm(UserUpdateMobileTForm::class, $user);

		$this->addTwigVar('tabActive', $this->getSession()
			->get('tabActive', 2));
		$this->getSession()->remove('tabActive');

		$request = $this->getRequest();
		$reqData = $request->request->all();
		$em = $this->getEntityManager();

		if (isset($reqData['UserUpdateEmailForm'])) {
			$this->addTwigVar('tabActive', 2);
			$this->getSession()->set('tabActive', 2);
			$userUpdateEmailForm->handleRequest($request);
			if ($userUpdateEmailForm->isValid()) {
				$em->persist($user);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate('ilcfrance.orangetools.security.UserUpdateEmailForm.success'));

				return $this->redirect($urlFrom);
			} else {
				$em->refresh($user);

				$this->addFlash(
					'error',
					$this->translate(
						'ilcfrance.orangetools.security.UserUpdateEmailForm.error',
						array(
							'%email%' => $userUpdateEmailForm['email']->getData()
						)));
			}
		} elseif (isset($reqData['UserUpdatePasswordForm'])) {
			$this->addTwigVar('tabActive', 2);
			$this->getSession()->set('tabActive', 2);
			$userUpdatePasswordForm->handleRequest($request);
			if ($userUpdatePasswordForm->isValid()) {
				$oldPassword = $userUpdatePasswordForm['oldPassword']->getData();
				$encoder = new Pbkdf2PasswordEncoder('sha512', true, 1000);
				$oldpassEncoded = $encoder->encodePassword($oldPassword, $user->getSalt());
				if ($oldpassEncoded != $oldDbpass) {
					$formError = new FormError($this->translate('ilcfrance.orangetools.security.UserUpdatePasswordForm.oldPassword.incorrect', array(), 'validators'));
					$userUpdatePasswordForm['oldPassword']->addError($formError);
					$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.profile.UserUpdatePasswordForm.error'));
				} else {
					$em->persist($user);
					$em->flush();
					$this->addFlash('success', $this->translate('ilcfrance.orangetools.security.UserUpdatePasswordForm.success'));

					return $this->redirect($urlFrom);
				}
			} else {
				$em->refresh($user);

				$this->addFlash(
					'error',
					$this->translate(
						'ilcfrance.orangetools.security.UserUpdatePasswordForm.error'));
			}
		} elseif (isset($reqData['UserUpdateInfosForm'])) {
			$this->addTwigVar('tabActive', 2);
			$this->getSession()->set('tabActive', 2);
			$userUpdateInfosForm->handleRequest($request);
			if ($userUpdateInfosForm->isValid()) {
				$em->persist($user);
				$em->flush();
				$this->addFlash('success', $this->translate('ilcfrance.orangetools.security.UserUpdateInfosForm.success'));

				return $this->redirect($urlFrom);
			} else {
				$em->refresh($user);

				$this->addFlash(
					'error',
					$this->translate('ilcfrance.orangetools.security.UserUpdateInfosForm.error')
				);
			}
		} elseif (isset($reqData['UserUpdatePhoneForm'])) {
			$this->addTwigVar('tabActive', 2);
			$this->getSession()->set('tabActive', 2);
			$userUpdatePhoneForm->handleRequest($request);
			if ($userUpdatePhoneForm->isValid()) {
				$em->persist($user);
				$em->flush();
				$this->addFlash('success', $this->translate('ilcfrance.orangetools.security.UserUpdatePhoneForm.success'));

				return $this->redirect($urlFrom);
			} else {
				$em->refresh($user);

				$this->addFlash(
					'error',
					$this->translate('ilcfrance.orangetools.security.UserUpdateInfosForm.error')
				);
			}
		} elseif (isset($reqData['UserUpdateMobileForm'])) {
			$this->addTwigVar('tabActive', 2);
			$this->getSession()->set('tabActive', 2);
			$userUpdateMobileForm->handleRequest($request);
			if ($userUpdateMobileForm->isValid()) {
				$em->persist($user);
				$em->flush();
				$this->addFlash('success', $this->translate('ilcfrance.orangetools.security.UserUpdateInfosForm.success'));

				return $this->redirect($urlFrom);
			} else {
				$em->refresh($user);

				$this->addFlash(
					'error',
					$this->translate('ilcfrance.orangetools.security.UserUpdateMobileForm.error')
				);
			}
		}

		$this->addTwigVar('user', $user);
		$this->addTwigVar('UserUpdateEmailForm', $userUpdateEmailForm->createView());
		$this->addTwigVar('UserUpdatePasswordForm', $userUpdatePasswordForm->createView());
		$this->addTwigVar('UserUpdateInfosForm', $userUpdateInfosForm->createView());
		$this->addTwigVar('UserUpdatePhoneForm', $userUpdatePhoneForm->createView());
		$this->addTwigVar('UserUpdateMobileForm', $userUpdateMobileForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.profile.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.profile.pagetitle'));

		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllRelatedToUser($user);
		$this->addTwigVar('traces', \array_reverse($traces->toArray()));

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Profile:index.html.twig', $this->getTwigVars());

	}

}