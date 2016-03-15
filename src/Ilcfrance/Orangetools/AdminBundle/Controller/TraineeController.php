<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\NewTForm as TraineeNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\ImportTForm as TraineeImportTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\UpdateInfosTForm as TraineeUpdateInfosTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\UpdateManagerTForm as TraineeUpdateManagerTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\UpdateEmailTForm as TraineeUpdateEmailTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\UpdateClearPasswordTForm as TraineeUpdateClearPasswordTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\UpdateLockoutTForm as TraineeUpdateLockoutTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\UpdateValidUntilTForm as TraineeUpdateValidUntilTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Trainee\SendParamsTForm as TraineeSendParamsTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription\NewTForm as ModulepreinscriptionNewTForm;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\NewTForm as SessioninscriptionNewTForm;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TraineeController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_trainees');

	}

	public function listAction()
	{

		$this->addTwigVar('menu_active', 'admin_trainees_list');

		$em = $this->getEntityManager();

		$roleTrainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(array(
			'name' => 'ROLE_TRAINEE'
		));
		if (null == $roleTrainee) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		}

		$trainees = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->getAllByRole($roleTrainee);
		$this->addTwigVar('trainees', $trainees);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:list.html.twig', $this->getTwigVars());

	}

	public function sendInfosAction()
	{

		$em = $this->getEntityManager();

		$roleTrainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(array(
			'name' => 'ROLE_TRAINEE'
		));
		if (null == $roleTrainee) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		}
		$countTrainees = 0;
		$mailsent = 0;

		$trainees = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->getAllByRole($roleTrainee);
		foreach ($trainees as $trainee) {
			$countTrainees++;
			if ($trainee->getInfoSent() == User::INFOSENT_NO) {

				try {
					$mvars = array();
					$mvars['user'] = $trainee;
					$mvars['url'] = $this->generateUrl('ilcfrance_orangetools_front_homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);

					$from = $this->getParameter('mail_from');
					$fromName = $this->getParameter('mail_from_name');
					$replyTo = $this->getParameter('mail_replay');
					$replyToName = $this->getParameter('mail_replay_name');
					$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.admin.Trainee.mail.params.subject', array('%year%' => date('Y')));
					$message = \Swift_Message::newInstance()->setFrom($from, $fromName)
						->setTo($trainee->getEmail(), $trainee->getFullname())
						->setReplyTo($replyTo, $replyToName)
						->setSubject($subject)
						->setBody($this->renderView('IlcfranceOrangetoolsAdminBundle:Trainee:sendInfos.mail.html.twig', $mvars), 'text/html');

					$this->sendmail($message);

					$trainee->setInfoSent(User::INFOSENT_YES);
					$em->persist($trainee);
					$mailsent++;
				} catch (\Exception $e) {
					$logger = $this->getLogger();
					$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
				}
			}
		}
		$em->flush();

		$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Trainee.mail.params.sent', array('%countTrainees%' => $countTrainees, '%$mailsent%' => $mailsent)));

		return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_trainee_list'));

	}

	public function exportAction()
	{

		$em = $this->getEntityManager();

		$roleTrainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(array(
			'name' => 'ROLE_TRAINEE'
		));
		if (null == $roleTrainee) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		}

		$trainees = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->getAllByRole($roleTrainee);

		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()
			->setCreator("Salah Abdelkader Seif Eddine")
			->setLastModifiedBy($this->getSecurityTokenStorage()
			->getToken()
			->getUser()
			->getFullname())
			->setTitle($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle'))
			->setSubject($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle'))
			->setDescription($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle'))
			->setKeywords($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle'))
			->setCategory("Trainee");

		$phpExcelObject->setActiveSheetIndex(0);

		$workSheet = $phpExcelObject->getActiveSheet();
		$workSheet->setTitle($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle'));

		$headerStyle = array(
			'fill' => array(
				'type' => \PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => 'FF6600'
				)
			)
		);

		$style1 = array(
			'fill' => array(
				'type' => \PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => 'FFF71C'
				)
			)
		);

		$style2 = array(
			'fill' => array(
				'type' => \PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			)
		);

		$col = 0;
		$row = 1;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.username'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.lastName'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.firstName'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.phone'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.mobile'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.email'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.job'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.lastName2'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.firstName2'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.email2'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$workSheet->setCellValueByColumnAndRow($col, $row, $this->translate('User.level'));
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->getFont()
			->setBold(true);
		$workSheet->getCellByColumnAndRow($col, $row)
			->getStyle()
			->applyFromArray($headerStyle);
		$col ++;

		$maxcol = $col;

		$row ++;

		foreach ($trainees as $trainee) {
			$col = 0;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getUsername());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getLastName());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getFirstName());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getPhone());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getMobile());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getEmail());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getJob());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getLastName2());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getFirstName2());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getEmail2());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			$workSheet->setCellValueByColumnAndRow($col, $row, $trainee->getLevel());
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->getFont()
				->setBold(true);
			$workSheet->getCellByColumnAndRow($col, $row)
				->getStyle()
				->applyFromArray($row % 2 == 1 ? $style1 : $style2);
			$col ++;

			foreach ($trainee->getModulepreinscriptions() as $modulepreinscription) {

				$workSheet->setCellValueByColumnAndRow($col, $row, $modulepreinscription->getModuleformation()
					->getCode());
				$workSheet->getCellByColumnAndRow($col, $row)
					->getStyle()
					->getFont()
					->setBold(true);
				$workSheet->getCellByColumnAndRow($col, $row)
					->getStyle()
					->applyFromArray($row % 2 == 1 ? $style2 : $style1);
				$col ++;
			}

			foreach ($trainee->getSessioninscriptions() as $sessioninscription) {

				$workSheet->setCellValueByColumnAndRow($col, $row, $sessioninscription->getSessionformation()
					->getCode());
				$workSheet->getCellByColumnAndRow($col, $row)
					->getStyle()
					->getFont()
					->setBold(true);
				$workSheet->getCellByColumnAndRow($col, $row)
					->getStyle()
					->applyFromArray($row % 2 == 1 ? $style1 : $style2);
				$col ++;
			}

			if ($maxcol < $col) {
				$maxcol = $col;
			}

			$row ++;
		}

		for ($i = 0; $i <= $maxcol; $i ++) {
			$colStrName = \PHPExcel_Cell::stringFromColumnIndex($i);
			$workSheet->getColumnDimension($colStrName)->setAutoSize(true);
		}

		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$response = $this->get('phpexcel')->createStreamedResponse($writer);

		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');

		$now = new \DateTime('now');
		$filename = $this->normalize(
			$this->getParameter('sitename') . '_' . $this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle').'_'.$now->format('Y-m-d_H.i.s'));
		$filename = str_ireplace('"', '|', $filename);
		$filename = str_ireplace(' ', '_', $filename);

		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename . '.xlsx');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');

		return $response;

	}

	public function addGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_trainees_add');

		$trainee = new User();
		$traineeNewForm = $this->createForm(TraineeNewTForm::class, $trainee);

		$this->addTwigVar('trainee', $trainee);
		$this->addTwigVar('TraineeNewForm', $traineeNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_trainees_add');

		$trainee = new User();
		$traineeNewForm = $this->createForm(TraineeNewTForm::class, $trainee);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['TraineeNewForm'])) {
			$traineeNewForm->handleRequest($request);
			if ($traineeNewForm->isValid()) {
				$em = $this->getEntityManager();

				$roleTrainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(
					array(
						'name' => 'ROLE_TRAINEE'
					));
				if (null == $roleTrainee) {
					return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
				}

				$trainee->addUserRole($roleTrainee);
				$validUntil = new \DateTime();
				$validUntil->modify("+12 day");
				$trainee->setValidUntil($validUntil);

				$em->persist($trainee);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Trainee.add.success',
						array(
							'%user%' => $trainee->getUsername()
						)));

				return $this->redirect(
					$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
						'id' => $trainee->getId()
					)));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.add.failure'));
			}
		}

		$this->addTwigVar('trainee', $trainee);
		$this->addTwigVar('TraineeNewForm', $traineeNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:add.html.twig', $this->getTwigVars());

	}

	public function importGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_trainees_import');

		$traineeImportForm = $this->createForm(TraineeImportTForm::class);

		$this->addTwigVar('TraineeImportForm', $traineeImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:import.html.twig', $this->getTwigVars());

	}

	public function importPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_trainees_import');

		$traineeImportForm = $this->createForm(TraineeImportTForm::class);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['TraineeImportForm'])) {
			$traineeImportForm->handleRequest($request);
			if ($traineeImportForm->isValid()) {
				ini_set('memory_limit', '4096M');
				ini_set('max_execution_time', '0');

				$em = $this->getEntityManager();

				$roleTrainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:Role')->findOneBy(
					array(
						'name' => 'ROLE_TRAINEE'
					));
				if (null == $roleTrainee) {
					return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
				}

				$extension = $traineeImportForm['excel']->getData()->guessExtension();
				if ($extension == 'zip') {
					$extension = 'xlsx';
				}

				$now = new \DateTime('now');
				$filename = $this->normalize($this->translate('ilcfrance.orangetools.admin.Trainee.list.browserpagetitle')).'_'.$now->format('Y-m-d_H.i.s').'_'.uniqid() . '.' . $extension;
				$traineeImportForm['excel']->getData()->move($this->getParameter('adapter_files'), $filename);
				$fullfilename = $this->getParameter('adapter_files');
				$fullfilename .= '/' . $filename;

				$excelObj = $this->get('phpexcel')->createPHPExcelObject($fullfilename);

				$log = "";

				$activeSheetIndex = 0;

				$excelObj->setActiveSheetIndex($activeSheetIndex);
				$worksheet = $excelObj->getActiveSheet();
				$highestRow = $worksheet->getHighestRow();
				$highestColName = $worksheet->getHighestColumn();
				$highestCol = \PHPExcel_Cell::columnIndexFromString($highestColName);

				$lineRead = 0;
				$traineeNew = 0;
				$lineUnprocessed = 0;
				$lineError = 0;

				$moduleformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->getAll();
				$sessionformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->getAll();

				for ($row = 2; $row <= $highestRow; $row ++) {
					$lineRead ++;
					$col = 0;

					$xlsUsername = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsLastName = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsFirstName = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsPhone = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsMobile = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsEmail = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsJob = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsLastName2 = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsFirstName2 = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsEmail2 = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$xlsLevel = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
					$col ++;
					$beginCol = $col;

					if ($xlsUsername != "") {
						$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->findOneBy(
							array(
								'username' => $xlsUsername
							));
						if (null == $trainee) {

							$trainee = new User();
							$trainee->setUsername($xlsUsername);
							$trainee->setLastName($xlsLastName);
							$trainee->setFirstName($xlsFirstName);
							$trainee->setPhone($xlsPhone);
							$trainee->setMobile($xlsMobile);
							$trainee->setEmail($xlsEmail);
							$trainee->setJob($xlsJob);
							$trainee->setLastName2($xlsLastName2);
							$trainee->setFirstName2($xlsFirstName2);
							$trainee->setEmail2($xlsEmail2);
							$trainee->setLevel($xlsLevel);
							$validUntil = new \DateTime();
							$validUntil->modify("+12 day");
							$trainee->setValidUntil($validUntil);

							$trainee->addUserRole($roleTrainee);

							$em->persist($trainee);
							$traineeNew ++;

							for ($i = $beginCol; $i < $highestCol; $i ++) {
								$cellValue = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
								$col ++;
								$i ++;
								if ($cellValue != '') {
									$isModuleformation = false;
									$isSessionformation = false;
									foreach ($moduleformations as $moduleformation) {
										if ($moduleformation->getCode() == $cellValue) {
											$modulepreinscription = new Modulepreinscription();
											$modulepreinscription->setUser($trainee);
											$modulepreinscription->setModuleformation($moduleformation);
											$em->persist($modulepreinscription);

											$isModuleformation = true;
										}
									}
									foreach ($sessionformations as $sessionformation) {
										if ($sessionformation->getCode() == $cellValue) {
											$sessioninscription = new Sessioninscription();
											$sessioninscription->setUser($trainee);
											$sessioninscription->setSessionformation($sessionformation);
											$em->persist($sessioninscription);

											$isSessionformation = true;
										}
									}
									if (!$isModuleformation && !$isSessionformation) {
										$log .= "le Code  <b>" . $cellValue . "</b> à la ligne " . $row . " n'existe pas<br>";
									}
								}
							}
						} else {
							$log .= "l'Identifiant  <b>" . $xlsUsername . "</b> à la ligne " . $row . " existe déjà<br>";

							for ($i = $beginCol; $i < $highestCol; $i ++) {
								$cellValue = \trim(\strval($worksheet->getCellByColumnAndRow($col, $row)->getValue()));
								$col ++;
								$i ++;
								if ($cellValue != '') {
									$isModuleformation = false;
									$isModulepreinscriptionExist = false;
									$isSessionformation = false;
									$isSessioninscriptionExist = false;
									foreach ($moduleformations as $moduleformation) {
										if ($cellValue != '' && $moduleformation->getCode() == $cellValue) {

											$modulepreinscription = $em->getRepository(
												'IlcfranceOrangetoolsDataBundle:Modulepreinscription')->findOneBy(
												array(
													'user' => $trainee,
													'moduleformation' => $moduleformation
												));

											if (null == $modulepreinscription) {
												$modulepreinscription = new Modulepreinscription();
												$modulepreinscription->setUser($trainee);
												$modulepreinscription->setModuleformation($moduleformation);
												$em->persist($modulepreinscription);
											} else {
												$isModulepreinscriptionExist = true;
											}
											$isModuleformation = true;
										}
									}
									foreach ($sessionformations as $sessionformation) {
										if ($cellValue != '' && $sessionformation->getCode() == $cellValue) {

											$sessioninscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessioninscription')->findOneBy(
												array(
													'user' => $trainee,
													'sessionformation' => $sessionformation
												));

											if (null == $sessioninscription) {
												$sessioninscription = new Sessioninscription();
												$sessioninscription->setUser($trainee);
												$sessioninscription->setSessionformation($sessionformation);
												$em->persist($sessioninscription);
											} else {
												$isSessioninscriptionExist = true;
											}
											$isSessionformation = true;
										}
									}
									if (!$isModuleformation && !$isSessionformation) {
										$log .= "le Code  <b>" . $cellValue . "</b> à la ligne " . $row . " n'existe pas<br>";
									} elseif ($isModuleformation && $isModulepreinscriptionExist) {
										$log .= "le Stagiaire  <b>" . $xlsUsername . "</b> est déja inscrit au module <b>" . $cellValue . "</b> à la ligne " . $row . "<br>";
									} elseif ($isSessionformation && $isSessioninscriptionExist) {
										$log .= "le Stagiaire  <b>" . $xlsUsername . "</b> est déja inscrit à la session <b>" . $cellValue . "</b> à la ligne " . $row . "<br>";
									}
								}
							}

							$lineUnprocessed ++;
						}
					} else {
						$lineError ++;
						$log .= "la ligne " . $row . " ne contient pas de Stagiaire valide<br>";
					}
				}
				$em->flush();
				$log .= $lineRead . " lignes lues<br>";
				$log .= $traineeNew . " nouveaux Stagiaires<br>";
				$log .= $lineUnprocessed . " Stagiaires déjà dans la base<br>";
				$log .= $lineError . " lignes contenant des erreurs<br>";
				$this->addFlash('log', $log);
				$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Trainee.import.success'));
				return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_trainee_importGet'));
			} else {
				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.import.failure'));
			}
		}

		$this->addTwigVar('TraineeImportForm', $traineeImportForm->createView());
		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Trainee.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:import.html.twig', $this->getTwigVars());

	}

	public function deleteAction($id)
	{

		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_trainee_list');
		}
		$em = $this->getEntityManager();
		try {
			$user = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

			if (null == $user) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Trainee.delete.notfound'));
			} else {
				$em->remove($user);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate('ilcfrance.orangetools.admin.Trainee.delete.success', array(
						'%user%' => $user->getUsername()
					)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.delete.failure'));
		}

		return $this->redirect($urlFrom);

	}

	public function editGetAction($id)
	{

		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_trainee_list');
		}

		$em = $this->getEntityManager();
		try {
			$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

			if (null == $trainee) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.notfound'));
			} else {
				$userUpdateInfosForm = $this->createForm(TraineeUpdateInfosTForm::class, $trainee);
				$userUpdateManagerForm = $this->createForm(TraineeUpdateManagerTForm::class, $trainee);
				$userUpdateEmailForm = $this->createForm(TraineeUpdateEmailTForm::class, $trainee);
				$userUpdateClearPasswordForm = $this->createForm(TraineeUpdateClearPasswordTForm::class, $trainee);
				$userUpdateLockoutForm = $this->createForm(TraineeUpdateLockoutTForm::class, $trainee);
				$userUpdateValidUntilForm = $this->createForm(TraineeUpdateValidUntilTForm::class, $trainee);
				$userSendParamsForm = $this->createForm(TraineeSendParamsTForm::class, $trainee);

				$modulepreinscription = new Modulepreinscription();
				$modulepreinscription->setUser($trainee);
				$modulepreinscriptionNewForm = $this->createForm(
					ModulepreinscriptionNewTForm::class,
					$modulepreinscription,
					array(
						'user' => $trainee
					));

				$sessioninscription = new Sessioninscription();
				$sessioninscription->setUser($trainee);
				$sessioninscriptionNewForm = $this->createForm(
					SessioninscriptionNewTForm::class,
					$sessioninscription,
					array(
						'user' => $trainee
					));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$this->addTwigVar('trainee', $trainee);
				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('sessioninscription', $sessioninscription);
				$this->addTwigVar('TraineeUpdateInfosForm', $userUpdateInfosForm->createView());
				$this->addTwigVar('TraineeUpdateManagerForm', $userUpdateManagerForm->createView());
				$this->addTwigVar('TraineeUpdateEmailForm', $userUpdateEmailForm->createView());
				$this->addTwigVar('TraineeUpdateClearPasswordForm', $userUpdateClearPasswordForm->createView());
				$this->addTwigVar('TraineeUpdateLockoutForm', $userUpdateLockoutForm->createView());
				$this->addTwigVar('TraineeUpdateValidUntilForm', $userUpdateValidUntilForm->createView());
				$this->addTwigVar('TraineeSendParamsForm', $userSendParamsForm->createView());
				$this->addTwigVar('ModulepreinscriptionNewForm', $modulepreinscriptionNewForm->createView());
				$this->addTwigVar('SessioninscriptionNewForm', $sessioninscriptionNewForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Trainee.edit.browserpagetitle',
						array(
							'%user%' => $trainee->getUsername()
						)));
				$this->setPageTitle(
					$this->translate('ilcfrance.orangetools.admin.Trainee.edit.pagetitle', array(
						'%user%' => $trainee->getUsername()
					)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId($trainee->getId(), Trace::AE_User);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

	public function editPostAction($id)
	{
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_trainee_list');
		}

		$em = $this->getEntityManager();
		try {
			$trainee = $em->getRepository('IlcfranceOrangetoolsDataBundle:User')->find($id);

			if (null == $trainee) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.notfound'));
			} else {
				$userUpdateInfosForm = $this->createForm(TraineeUpdateInfosTForm::class, $trainee);
				$userUpdateManagerForm = $this->createForm(TraineeUpdateManagerTForm::class, $trainee);
				$userUpdateEmailForm = $this->createForm(TraineeUpdateEmailTForm::class, $trainee);
				$userUpdateClearPasswordForm = $this->createForm(TraineeUpdateClearPasswordTForm::class, $trainee);
				$userUpdateLockoutForm = $this->createForm(TraineeUpdateLockoutTForm::class, $trainee);
				$userUpdateValidUntilForm = $this->createForm(TraineeUpdateValidUntilTForm::class, $trainee);
				$userSendParamsForm = $this->createForm(TraineeSendParamsTForm::class, $trainee);

				$modulepreinscription = new Modulepreinscription();
				$modulepreinscription->setUser($trainee);
				$modulepreinscriptionNewForm = $this->createForm(
					ModulepreinscriptionNewTForm::class,
					$modulepreinscription,
					array(
						'user' => $trainee
					));

				$sessioninscription = new Sessioninscription();
				$sessioninscription->setUser($trainee);
				$sessioninscriptionNewForm = $this->createForm(
					SessioninscriptionNewTForm::class,
					$sessioninscription,
					array(
						'user' => $trainee
					));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['TraineeUpdateInfosForm'])) {
					$userUpdateInfosForm->handleRequest($request);
					if ($userUpdateInfosForm->isValid()) {
						$em->persist($trainee);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Trainee.edit.success',
								array(
									'%user%' => $trainee->getUsername()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['TraineeUpdateManagerForm'])) {
					$userUpdateManagerForm->handleRequest($request);
					if ($userUpdateManagerForm->isValid()) {
						$em->persist($trainee);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Trainee.edit.success',
								array(
									'%user%' => $trainee->getUsername()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['TraineeUpdateEmailForm'])) {
					$userUpdateEmailForm->handleRequest($request);
					if ($userUpdateEmailForm->isValid()) {
						$em->persist($trainee);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Trainee.edit.success',
								array(
									'%user%' => $trainee->getUsername()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['TraineeUpdateClearPasswordForm'])) {
					$userUpdateClearPasswordForm->handleRequest($request);
					if ($userUpdateClearPasswordForm->isValid()) {
						$em->persist($trainee);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Trainee.edit.success',
								array(
									'%user%' => $trainee->getUsername()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['TraineeUpdateLockoutForm'])) {
					$userUpdateLockoutForm->handleRequest($request);
					if ($userUpdateLockoutForm->isValid()) {
						$em->persist($trainee);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Trainee.edit.success',
								array(
									'%user%' => $trainee->getUsername()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['TraineeUpdateValidUntilForm'])) {
					$userUpdateValidUntilForm->handleRequest($request);
					if ($userUpdateValidUntilForm->isValid()) {
						$em->persist($trainee);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Trainee.edit.success',
								array(
									'%user%' => $trainee->getUsername()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['TraineeSendParamsForm'])) {
					$userSendParamsForm->handleRequest($request);
					if ($userSendParamsForm->isValid()) {
						$countTrainees = 0;
						$mailsent = 0;


						$countTrainees++;

						try {

							$mvars = array();
							$mvars['user'] = $trainee;
							$mvars['url'] = $this->generateUrl('ilcfrance_orangetools_front_homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);

							$from = $this->getParameter('mail_from');
							$fromName = $this->getParameter('mail_from_name');
							$subject = '[' . $this->getParameter('sitename') . '] ' . $this->translate('ilcfrance.orangetools.admin.Trainee.mail.params.subject', array('%year%' => date('Y')));
							$message = \Swift_Message::newInstance()->setFrom($from, $fromName)
							->setTo($trainee->getEmail(), $trainee->getFullname())
							->setSubject($subject)
							->setBody($this->renderView('IlcfranceOrangetoolsAdminBundle:Trainee:sendInfos.mail.html.twig', $mvars), 'text/html');

							$this->sendmail($message);

							$trainee->setInfoSent(User::INFOSENT_YES);
							$em->persist($trainee);
							$mailsent++;
							$em->flush();
						} catch (\Exception $e) {
							$logger = $this->getLogger();
							$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
						}

						$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Trainee.mail.params.sent', array('%countTrainees%' => $countTrainees, '%$mailsent%' => $mailsent)));


						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 1);
						$this->getSession()->set('tabActive', 1);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Trainee.edit.failure'));
					}
				} elseif (isset($reqData['ModulepreinscriptionNewForm'])) {
					$modulepreinscriptionNewForm->handleRequest($request);
					if ($modulepreinscriptionNewForm->isValid()) {
						$em->persist($modulepreinscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Modulepreinscription.add.success',
								array(
									'%moduleformation%' => $modulepreinscription->getModuleformation()
										->getCode(),
									'%user%' => $trainee->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 3);
						$this->getSession()->set('tabActive', 3);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.failure'));
					}
				} elseif (isset($reqData['SessioninscriptionNewForm'])) {
					$sessioninscriptionNewForm->handleRequest($request);
					if ($sessioninscriptionNewForm->isValid()) {
						$em->persist($sessioninscription);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessioninscription.add.success',
								array(
									'%sessionformation%' => $sessioninscription->getSessionformation()
										->getCode(),
									'%user%' => $trainee->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_trainee_editGet', array(
								'id' => $trainee->getId()
							)));
					} else {
						$this->addTwigVar('tabActive', 3);
						$this->getSession()->set('tabActive', 3);

						$em->refresh($trainee);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.failure'));
					}
				}

				$this->addTwigVar('trainee', $trainee);
				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('sessioninscription', $sessioninscription);
				$this->addTwigVar('TraineeUpdateEmailForm', $userUpdateEmailForm->createView());
				$this->addTwigVar('ModulepreinscriptionNewForm', $modulepreinscriptionNewForm->createView());
				$this->addTwigVar('SessioninscriptionNewForm', $sessioninscriptionNewForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Trainee.edit.browserpagetitle',
						array(
							'%user%' => $trainee->getUsername()
						)));
				$this->setPageTitle(
					$this->translate('ilcfrance.orangetools.admin.Trainee.edit.pagetitle', array(
						'%user%' => $trainee->getUsername()
					)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId($trainee->getId(), Trace::AE_User);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Trainee:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

}