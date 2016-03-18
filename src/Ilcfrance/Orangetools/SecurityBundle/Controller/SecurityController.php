<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Controller;

use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\SecurityBundle\Form\LoginTForm;
use Ilcfrance\Orangetools\SecurityBundle\Form\LostIdTForm;
use Ilcfrance\Orangetools\SecurityBundle\Form\LostPasswordTForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class SecurityController extends SasedevController
{

	/**
	 * login Action
	 *
	 * @return RedirectResponse|Response
	 */
	public function loginAction()
	{

		$this->addTwigVar('menu_active', 'profile');
		// si l'utilisateur est déja connecté on le redirige vers sa page de
		// profile
		if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}
		$session = $this->getSession();
		$request = $this->getRequest();
		if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
			$request->attributes->remove(Security::AUTHENTICATION_ERROR);
			$this->addFlash('error', $this->translate($error->getMessage()));
		} elseif ($session->has(Security::AUTHENTICATION_ERROR)) {
			$error = $session->get(Security::AUTHENTICATION_ERROR);
			$session->remove(Security::AUTHENTICATION_ERROR);
			$this->addFlash('error', $this->translate($error->getMessage()));
		}

		$lastUsername = $session->get('_security.last_username');
		$referer = $this->getReferer();

		$loginForm = $this->createForm(LoginTForm::class);

		$loginForm->get('username')->setData($lastUsername);
		$loginForm->get('target_path')->setData($referer);
		$loginForm->get('remember_me')->setData(true);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.login.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.login.pagetitle'));
		$this->addTwigVar('LoginForm', $loginForm->createView());

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Security:login.html.twig', $this->getTwigVars());

	}

	/**
	 * loginCheck [Get] Action
	 *
	 * @return RedirectResponse|Response
	 */
	public function loginCheckAction()
	{

		return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_login', array(), UrlGeneratorInterface::ABSOLUTE_URL));

	}

	/**
	 * lostPassword Action
	 *
	 * @return RedirectResponse|Response
	 */
	public function lostidAction()
	{

		if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}
		$lostIdForm = $this->createForm(LostIdTForm::class);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.lostid.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.lostid.pagetitle'));
		$this->addTwigVar('LostIdForm', $lostIdForm->createView());

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Security:lostid.html.twig', $this->getTwigVars());

	}

	/**
	 * lostPassword Action
	 *
	 * @return RedirectResponse|Response
	 */
	public function lostidPostAction()
	{

		if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}
		$lostIdForm = $this->createForm(LostIdTForm::class);
		$request = $this->getRequest();
		$lostIdForm->handleRequest($request);
		if ($lostIdForm->isValid()) {
			$email = $lostIdForm['email']->getData();
			$em = $this->getEntityManager();
			$user = null;
			$user = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->findOneBy(array(
				'email' => $email
			));

			if (null != $user) {

				$mvars = array();
				$mvars['user'] = $user;
				$mvars['url'] = $this->generateUrl('ilcfrance_orangetools_security_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);

				$from = $this->getParameter('mail_from');
				$fromName = $this->getParameter('mail_from_name');
						$replyTo = $this->getParameter('mail_replay');
						$replyToName = $this->getParameter('mail_replay_name');
				$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.security.lostid.mail.subject');
				$message = \Swift_Message::newInstance()->setFrom($from, $fromName)
							->setReplyTo($replyTo, $replyToName)
					->setTo($user->getEmail(), $user->getFullname())
					->setSubject($subject)
					->setBody($this->renderView('IlcfranceOrangetoolsSecurityBundle:Mail:lostid.html.twig', $mvars), 'text/html');

				$this->sendmail($message);

				$this->addFlash('success', $this->translate('ilcfrance.orangetools.security.lostid.mailSent', array(
					'%email%' => $user->getEmail()
				)));
				return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_login'));
			} else {
				$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.lostid.notfound', array(
					'%email%' => $email
				)));
			}
		}

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.lostid.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.lostid.pagetitle'));
		$this->addTwigVar('LostIdForm', $lostIdForm->createView());

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Security:lostid.html.twig', $this->getTwigVars());

	}

	/**
	 * lostpassword Action
	 *
	 * @return RedirectResponse|Response
	 */
	public function lostpasswordAction()
	{

		if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}
		$lostPasswordForm = $this->createForm(LostPasswordTForm::class);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.lostpassword.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.lostpassword.pagetitle'));
		$this->addTwigVar('LostPasswordForm', $lostPasswordForm->createView());

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Security:lostpassword.html.twig', $this->getTwigVars());

	}

	/**
	 * lostpassword Action
	 *
	 * @return RedirectResponse|Response
	 */
	public function lostpasswordPostAction()
	{

		if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_homepage'));
		}
		$lostPasswordForm = $this->createForm(LostPasswordTForm::class);
		$request = $this->getRequest();
		$lostPasswordForm->handleRequest($request);
		if ($lostPasswordForm->isValid()) {
			$username = $lostPasswordForm['username']->getData();
			$em = $this->getEntityManager();
			$user = null;
			$user = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->findOneBy(array(
				'username' => $username
			));

			if (null != $user) {
				$now = new \DateTime('now');
				$nexthour = new \DateTime();
				$nexthour->setTimestamp(strtotime('+1 hour'));
				if (null == $user->getRecoveryExpiration() || $user->getRecoveryExpiration() < $now) {
					$user->setRecoveryExpiration($nexthour);
					$user->setRecoveryCode(User::generateRandomChar(20));
					$em->persist($user);
					$em->flush();

					$mvars = array();
					$mvars['user'] = $user;
					$mvars['url'] = $this->generateUrl(
						'ilcfrance_orangetools_security_resetpass',
						array(
							'id' => $user->getId(),
							'code' => $user->getRecoveryCode()
						),
						UrlGeneratorInterface::ABSOLUTE_URL);

					$from = $this->getParameter('mail_from');
					$fromName = $this->getParameter('mail_from_name');
						$replyTo = $this->getParameter('mail_replay');
						$replyToName = $this->getParameter('mail_replay_name');
					$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.security.lostpassword.mail.subject');
					$message = \Swift_Message::newInstance()->setFrom($from, $fromName)
							->setReplyTo($replyTo, $replyToName)
						->setTo($user->getEmail(), $user->getFullname())
						->setSubject($subject)
						->setBody($this->renderView('IlcfranceOrangetoolsSecurityBundle:Mail:lostpassword.html.twig', $mvars), 'text/html');

					$this->sendmail($message);

					$this->addFlash(
						'success',
						$this->translate('ilcfrance.orangetools.security.lostpassword.mailSent', array(
							'%email%' => $user->getEmail()
						)));
					return $this->redirect($this->generateUrl('ilcfrance_orangetools_security_login'));
				} else {
					$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.lostpassword.mailAlreadySent'));
				}
			} else {
				$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.lostpassword.userNotFound', array(
					'%username%' => $username
				)));
			}
		}

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.lostpassword.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.lostpassword.pagetitle'));
		$this->addTwigVar('LostPasswordForm', $lostPasswordForm->createView());

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Security:lostpassword.html.twig', $this->getTwigVars());

	}

	/**
	 * resetpass Action
	 *
	 * @param guid $id
	 * @param string $code
	 *
	 * @return RedirectResponse|Response
	 */
	public function resetpassAction($id, $code)
	{

		$em = $this->getEntityManager();
		$user = null;
		$user = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);
		if (null == $user) {
			$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.resetpass.userNotFound'));
		} else {
			$now = new \DateTime('now');
			if (null == $user->getRecoveryExpiration() || $user->getRecoveryExpiration() < $now) {
				$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.resetpass.codeExpired'));
			} elseif ($user->getRecoveryCode() != $code) {
				$this->addFlash('error', $this->translate('ilcfrance.orangetools.security.resetpass.codeInvalid'));
			} else {
				$user->setSalt(md5(uniqid(null, true)));
				$user->setClearPassword(User::generateRandomChar(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'));
				$user->setRecoveryExpiration(null);
				$user->setRecoveryCode(null);
				$em->persist($user);
				$em->flush();
				$mvars = array();
				$mvars['user'] = $user;
				$mvars['url'] = $this->generateUrl('ilcfrance_orangetools_security_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);
				$from = $this->getParameter('mail_from');
				$fromName = $this->getParameter('mail_from_name');
						$replyTo = $this->getParameter('mail_replay');
						$replyToName = $this->getParameter('mail_replay_name');
				$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.security.resetpass.mail.subject');

				$message = \Swift_Message::newInstance()->setFrom($from, $fromName)
							->setReplyTo($replyTo, $replyToName)
					->setTo($user->getEmail(), $user->getFullname())
					->setSubject($subject)
					->setBody($this->renderView('IlcfranceOrangetoolsSecurityBundle:Mail:resetpass.html.twig', $mvars), 'text/html');

				$this->sendmail($message);

				$this->addFlash('success', $this->translate('ilcfrance.orangetools.security.resetpass.mailSent', array(
					'%email%' => $user->getEmail()
				)));
			}
		}

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.security.resetpass.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.security.resetpass.pagetitle'));

		return $this->render('IlcfranceOrangetoolsSecurityBundle:Security:resetpass.html.twig', $this->getTwigVars());

	}

}