<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\NewTForm as SessionformationNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\ImportTForm as SessionformationImportTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateCodeTForm as SessionformationUpdateCodeTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateModuleformationTForm as SessionformationUpdateModuleformationTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateTitleTForm as SessionformationUpdateTitleTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateConditionsReportTForm as SessionformationUpdateConditionsReportTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateDtInfoTForm as SessionformationUpdateDtInfoTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateDtStartTForm as SessionformationUpdateDtStartTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateLocationTForm as SessionformationUpdateLocationTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateLockoutTForm as SessionformationUpdateLockoutTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateMaxParticipantsTForm as SessionformationUpdateMaxParticipantsTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdateOtherInfosTForm as SessionformationUpdateOtherInfosTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\UpdatePhoneContactCenterTForm as SessionformationUpdatePhoneContactCenterTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\SendConvocationTForm as SessioninscriptionSendConvocationTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription\NewTForm as SessioninscriptionNewTForm;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class SessionformationController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_sessionformations');

	}

	public function listAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessionformations_list');

		$em = $this->getEntityManager();

		$sessionformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->getAll();
		$this->addTwigVar('sessionformations', $sessionformations);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:list.html.twig', $this->getTwigVars());

	}

	public function listByModuleformationAction($id)
	{

		$this->addTwigVar('menu_active', 'admin_sessionformations_list');

		$em = $this->getEntityManager();

		$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->find($id);

		if (null == $moduleformation) {
			$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.notfound'));
			$this->redirect($this->generateUrl('ilcfrance_orangetools_admin_sessionformation_list'));
		}

		$sessionformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->getAllByModuleformation(
			$moduleformation);
		$this->addTwigVar('sessionformations', $sessionformations);
		$this->addTwigVar('moduleformation', $moduleformation);

		$this->setBrowserPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Sessionformation.listByModuleformation.browserpagetitle',
				array(
					'%moduleformation%' => $moduleformation->getCode()
				)));
		$this->setPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Sessionformation.listByModuleformation.pagetitle',
				array(
					'%moduleformation%' => $moduleformation->getCode()
				)));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:listByModuleformation.html.twig', $this->getTwigVars());

	}

	public function exportAction()
	{

		$em = $this->getEntityManager();

		$sessionformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->getAll();

		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()
			->setCreator("Salah Abdelkader Seif Eddine")
			->setLastModifiedBy($this->getSecurityTokenStorage()
			->getToken()
			->getUser()
			->getFullname())
			->setTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle'))
			->setSubject($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle'))
			->setDescription($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle'))
			->setKeywords($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle'))
			->setCategory("Sessionformation");

		$phpExcelObject->setActiveSheetIndex(0);

		$workSheet = $phpExcelObject->getActiveSheet();
		$workSheet->setTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle'));

		$workSheet->setCellValue('A1', $this->translate('Sessionformation.moduleformation'));
		$workSheet->getStyle('A1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('B1', $this->translate('Sessionformation.code'));
		$workSheet->getStyle('B1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('C1', $this->translate('Sessionformation.title'));
		$workSheet->getStyle('C1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('D1', $this->translate('Sessionformation.dtStart'));
		$workSheet->getStyle('D1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('E1', $this->translate('Sessionformation.location'));
		$workSheet->getStyle('E1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('F1', $this->translate('Sessionformation.phoneContactCenter'));
		$workSheet->getStyle('F1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('G1', $this->translate('Sessionformation.conditionsReport'));
		$workSheet->getStyle('G1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('H1', $this->translate('Sessionformation.dtInfo'));
		$workSheet->getStyle('H1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('I1', $this->translate('Sessionformation.otherInfos'));
		$workSheet->getStyle('I1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('J1', $this->translate('Sessionformation.maxParticipants'));
		$workSheet->getStyle('J1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('K1', $this->translate('Sessionformation.lockout'));
		$workSheet->getStyle('K1')
			->getFont()
			->setBold(true);

		$workSheet->getStyle('A1:K1')->applyFromArray(
			array(
				'fill' => array(
					'type' => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array(
						'rgb' => 'FF6600'
					)
				)
			));

		$i = 1;

		foreach ($sessionformations as $sessionformation) {
			$i ++;
			$workSheet->setCellValue('A' . $i, $sessionformation->getModuleformation()
				->getCode(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('B' . $i, $sessionformation->getCode(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('C' . $i, $sessionformation->getTitle(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			//$dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', $sessionformation->getDtStart(), new \DateTimeZone('Europe/Paris'));
			//$workSheet->setCellValue(
			//	'D' . $i,
			//	\PHPExcel_Shared_Date::PHPToExcel($dtStart),
			//	\PHPExcel_Cell_DataType::TYPE_NUMERIC);
			// $workSheet->setCellValue('D' . $i, \PHPExcel_Shared_Date::PHPToExcel( $sessionformation->getDtStart(), true, 'Europe/Paris'), \PHPExcel_Cell_DataType::TYPE_NUMERIC);
			//$workSheet->getStyle('D' . $i)
			//	->getNumberFormat()
			//	->setFormatCode('yyyy-mm-dd h:mm:ss');
			$workSheet->setCellValue('D' . $i, $sessionformation->getDtStart(), \PHPExcel_Cell_DataType::TYPE_STRING2);

			$workSheet->setCellValue('E' . $i, $sessionformation->getLocation(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('F' . $i, $sessionformation->getPhoneContactCenter(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('G' . $i, $sessionformation->getConditionsReport(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('H' . $i, $sessionformation->getDtInfo(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue(
				'I' . $i,
				\html_entity_decode(\strip_tags($sessionformation->getOtherInfos())),
				\PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('J' . $i, $sessionformation->getMaxParticipants(), \PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$workSheet->setCellValue(
				'K' . $i,
				$this->translate('Sessionformation.lockout.' . $sessionformation->getLockout()),
				\PHPExcel_Cell_DataType::TYPE_STRING2);

			if ($i % 2 == 1) {
				$workSheet->getStyle('A' . $i . ':K' . $i)->applyFromArray(
					array(
						'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array(
								'rgb' => 'FFF71C'
							)
						)
					));
			} else {
				$workSheet->getStyle('A' . $i . ':K' . $i)->applyFromArray(
					array(
						'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array(
								'rgb' => 'FFFFFF'
							)
						)
					));
			}
		}

		$workSheet->getColumnDimension('A')->setAutoSize(true);
		$workSheet->getColumnDimension('B')->setAutoSize(true);
		$workSheet->getColumnDimension('C')->setAutoSize(true);
		$workSheet->getColumnDimension('D')->setAutoSize(true);
		$workSheet->getColumnDimension('E')->setAutoSize(true);
		$workSheet->getColumnDimension('F')->setAutoSize(true);
		$workSheet->getColumnDimension('G')->setAutoSize(true);
		$workSheet->getColumnDimension('H')->setAutoSize(true);
		$workSheet->getColumnDimension('I')->setAutoSize(true);
		$workSheet->getColumnDimension('J')->setAutoSize(true);
		$workSheet->getColumnDimension('K')->setAutoSize(true);

		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$response = $this->get('phpexcel')->createStreamedResponse($writer);

		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');

		$now = new \DateTime('now');
		$filename = $this->normalize(
			$this->getParameter('sitename') . '_' . $this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle').'_'.$now->format('Y-m-d_H.i.s'));
		$filename = str_ireplace('"', '|', $filename);
		$filename = str_ireplace(' ', '_', $filename);

		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename . '.xlsx');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');

		return $response;

	}

	public function addGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessionformations_add');

		$sessionformation = new Sessionformation();
		$sessionformationNewForm = $this->createForm(SessionformationNewTForm::class, $sessionformation);

		$this->addTwigVar('sessionformation', $sessionformation);
		$this->addTwigVar('SessionformationNewForm', $sessionformationNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessionformations_add');

		$sessionformation = new Sessionformation();
		$sessionformationNewForm = $this->createForm(SessionformationNewTForm::class, $sessionformation);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['SessionformationNewForm'])) {
			$sessionformationNewForm->handleRequest($request);
			if ($sessionformationNewForm->isValid()) {
				$em = $this->getEntityManager();

				$em->persist($sessionformation);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Sessionformation.add.success',
						array(
							'%sessionformation%' => $sessionformation->getCode()
						)));

				return $this->redirect(
					$this->generateUrl(
						'ilcfrance_orangetools_admin_sessionformation_editGet',
						array(
							'id' => $sessionformation->getId()
						)));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.add.failure'));
			}
		}

		$this->addTwigVar('sessionformation', $sessionformation);
		$this->addTwigVar('SessionformationNewForm', $sessionformationNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:add.html.twig', $this->getTwigVars());

	}

	public function importGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessionformations_import');

		$sessionformationImportForm = $this->createForm(SessionformationImportTForm::class);

		$this->addTwigVar('SessionformationImportForm', $sessionformationImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:import.html.twig', $this->getTwigVars());

	}

	public function importPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_sessionformations_import');

		$sessionformationImportForm = $this->createForm(SessionformationImportTForm::class);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['SessionformationImportForm'])) {
			$sessionformationImportForm->handleRequest($request);
			if ($sessionformationImportForm->isValid()) {
				ini_set('memory_limit', '4096M');
				ini_set('max_execution_time', '0');

				$em = $this->getEntityManager();

				$extension = $sessionformationImportForm['excel']->getData()->guessExtension();
				if ($extension == 'zip') {
					$extension = 'xlsx';
				}

				$now = new \DateTime('now');
				$filename = $this->normalize($this->translate('ilcfrance.orangetools.admin.Sessionformation.list.browserpagetitle')).'_'.$now->format('Y-m-d_H.i.s').'_'.uniqid() . '.' . $extension;
				$sessionformationImportForm['excel']->getData()->move($this->getParameter('adapter_files'), $filename);
				$fullfilename = $this->getParameter('adapter_files');
				$fullfilename .= '/' . $filename;

				$excelObj = $this->get('phpexcel')->createPHPExcelObject($fullfilename);

				$log = "";

				$activeSheetIndex = 0;

				$excelObj->setActiveSheetIndex($activeSheetIndex);
				$worksheet = $excelObj->getActiveSheet();
				$highestRow = $worksheet->getHighestRow();
				$lineRead = 0;
				$sessionformationNew = 0;
				$lineUnprocessed = 0;
				$lineError = 0;
				for ($row = 2; $row <= $highestRow; $row ++) {
					$lineRead ++;

					$xlsModuleformation = \trim(\strval($worksheet->getCellByColumnAndRow(0, $row)->getValue()));
					$xlsCode = \trim(\strval($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
					$xlsTitle = \trim(\strval($worksheet->getCellByColumnAndRow(2, $row)->getValue()));
					//$xlsDtStart = \PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow(3, $row)->getValue());
					//$xlsDtStart = \PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow(3, $row)->getValue(), true, \date_default_timezone_get());
					$xlsDtStart = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$xlsLocation = \trim(\strval($worksheet->getCellByColumnAndRow(4, $row)->getValue()));
					$xlsPhoneContactCenter = \trim(\strval($worksheet->getCellByColumnAndRow(5, $row)->getValue()));
					$xlsConditionsReport = \trim(\strval($worksheet->getCellByColumnAndRow(6, $row)->getValue()));
					$xlsDtInfo = \trim(\strval($worksheet->getCellByColumnAndRow(7, $row)->getValue()));
					$xlsOtherInfos = \nl2br(\trim(\strval($worksheet->getCellByColumnAndRow(8, $row)->getValue())));
					$xlsMaxParticipants = $worksheet->getCellByColumnAndRow(9, $row)->getValue();

					if ($xlsModuleformation != "" && $xlsCode != "" && $xlsTitle != "" && $xlsDtStart != "" && $xlsLocation != "") {
						$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->findOneBy(
							array(
								'code' => $xlsModuleformation
							));
						if (null != $moduleformation) {
							$sessionformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->findOneBy(
								array(
									'code' => $xlsCode
								));
							if (null == $sessionformation) {
								$sessionformation = new Sessionformation();
								$sessionformation->setModuleformation($moduleformation);
								$sessionformation->setCode($xlsCode);
								$sessionformation->setTitle($xlsTitle);
								$sessionformation->setDtStart($xlsDtStart);
								$sessionformation->setLocation($xlsLocation);
								$sessionformation->setPhoneContactCenter($xlsPhoneContactCenter);
								$sessionformation->setConditionsReport($xlsConditionsReport);
								$sessionformation->setDtInfo($xlsDtInfo);
								$sessionformation->setOtherInfos($xlsOtherInfos);
								$sessionformation->setMaxParticipants($xlsMaxParticipants);

								$em->persist($sessionformation);
								$sessionformationNew ++;
							} else {
								$lineUnprocessed ++;
								$log .= "le Code  <b>" . $xlsCode . "</b> à la ligne " . $row . " existe déjà<br>";
							}
						} else {
							$lineError ++;
							$log .= "le Module de formation <b>" . $xlsModuleformation . "</b> à la ligne " . $row . " n'existe pas<br>";
						}
					} else {
						$lineError ++;
						$log .= "la ligne " . $row . " ne contient pas de Session de formation valide<br>";
					}
				}
				$em->flush();

				$log .= $lineRead . " lignes lues<br>";
				$log .= $sessionformationNew . " nouvelles Sessions de formations<br>";
				$log .= $lineUnprocessed . " Sessions de formations déjà dans la base<br>";
				$log .= $lineError . " lignes contenant des erreurs<br>";

				$this->addFlash('log', $log);

				$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Sessionformation.import.success'));

				return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_sessionformation_importGet'));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.import.failure'));
			}
		}

		$this->addTwigVar('SessionformationImportForm', $sessionformationImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Sessionformation.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:import.html.twig', $this->getTwigVars());

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
			$sessionformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->find($id);

			if (null == $sessionformation) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessionformation.delete.notfound'));
			} else {
				$em->remove($sessionformation);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Sessionformation.delete.success',
						array(
							'%sessionformation%' => $sessionformation->getCode()
						)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.delete.failure'));
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_sessionformation_list');
		}

		$em = $this->getEntityManager();
		try {
			$sessionformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->find($id);

			if (null == $sessionformation) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.notfound'));
			} else {
				$sessionformationUpdateModuleformationForm = $this->createForm(
					SessionformationUpdateModuleformationTForm::class,
					$sessionformation);
				$sessionformationUpdateCodeForm = $this->createForm(SessionformationUpdateCodeTForm::class, $sessionformation);
				$sessionformationUpdateTitleForm = $this->createForm(SessionformationUpdateTitleTForm::class, $sessionformation);
				$sessionformationUpdateDtStartForm = $this->createForm(SessionformationUpdateDtStartTForm::class, $sessionformation);
				$sessionformationUpdateLocationForm = $this->createForm(SessionformationUpdateLocationTForm::class, $sessionformation);
				$sessionformationUpdatePhoneContactCenterForm = $this->createForm(
					SessionformationUpdatePhoneContactCenterTForm::class,
					$sessionformation);
				$sessionformationUpdateConditionsReportForm = $this->createForm(
					SessionformationUpdateConditionsReportTForm::class,
					$sessionformation);
				$sessionformationUpdateDtInfoForm = $this->createForm(SessionformationUpdateDtInfoTForm::class, $sessionformation);
				$sessionformationUpdateOtherInfosForm = $this->createForm(SessionformationUpdateOtherInfosTForm::class, $sessionformation);
				$sessionformationUpdateMaxParticipantsForm = $this->createForm(
					SessionformationUpdateMaxParticipantsTForm::class,
					$sessionformation);
				$sessionformationUpdateLockoutForm = $this->createForm(SessionformationUpdateLockoutTForm::class, $sessionformation);
				$sessioninscriptionSendConvocationForm = $this->createForm(SessioninscriptionSendConvocationTForm::class, $sessionformation);

				$sessioninscription = new Sessioninscription();
				$sessioninscription->setSessionformation($sessionformation);
				$sessioninscriptionNewForm = $this->createForm(
					SessioninscriptionNewTForm::class,
					$sessioninscription,
					array(
						'sessionformation' => $sessionformation
					));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$this->addTwigVar('sessionformation', $sessionformation);
				$this->addTwigVar('sessioninscription', $sessioninscription);
				$this->addTwigVar('SessionformationUpdateModuleformationForm', $sessionformationUpdateModuleformationForm->createView());
				$this->addTwigVar('SessionformationUpdateCodeForm', $sessionformationUpdateCodeForm->createView());
				$this->addTwigVar('SessionformationUpdateTitleForm', $sessionformationUpdateTitleForm->createView());
				$this->addTwigVar('SessionformationUpdateDtStartForm', $sessionformationUpdateDtStartForm->createView());
				$this->addTwigVar('SessionformationUpdateLocationForm', $sessionformationUpdateLocationForm->createView());
				$this->addTwigVar(
					'SessionformationUpdatePhoneContactCenterForm',
					$sessionformationUpdatePhoneContactCenterForm->createView());
				$this->addTwigVar('SessionformationUpdateConditionsReportForm', $sessionformationUpdateConditionsReportForm->createView());
				$this->addTwigVar('SessionformationUpdateDtInfoForm', $sessionformationUpdateDtInfoForm->createView());
				$this->addTwigVar('SessionformationUpdateOtherInfosForm', $sessionformationUpdateOtherInfosForm->createView());
				$this->addTwigVar('SessionformationUpdateMaxParticipantsForm', $sessionformationUpdateMaxParticipantsForm->createView());
				$this->addTwigVar('SessionformationUpdateLockoutForm', $sessionformationUpdateLockoutForm->createView());
				$this->addTwigVar('SessioninscriptionSendConvocationForm', $sessioninscriptionSendConvocationForm->createView());
				$this->addTwigVar('SessioninscriptionNewForm', $sessioninscriptionNewForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessionformation.edit.browserpagetitle',
						array(
							'%sessionformation%' => $sessionformation->getCode()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessionformation.edit.pagetitle',
						array(
							'%sessionformation%' => $sessionformation->getCode()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$sessionformation->getId(),
					Trace::AE_Sessionformation);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:edit.html.twig', $this->getTwigVars());
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_sessionformation_list');
		}

		$em = $this->getEntityManager();
		try {
			$sessionformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Sessionformation')->find($id);

			if (null == $sessionformation) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.notfound'));
			} else {
				$sessionformationUpdateModuleformationForm = $this->createForm(
					SessionformationUpdateModuleformationTForm::class,
					$sessionformation);
				$sessionformationUpdateCodeForm = $this->createForm(SessionformationUpdateCodeTForm::class, $sessionformation);
				$sessionformationUpdateTitleForm = $this->createForm(SessionformationUpdateTitleTForm::class, $sessionformation);
				$sessionformationUpdateDtStartForm = $this->createForm(SessionformationUpdateDtStartTForm::class, $sessionformation);
				$sessionformationUpdateLocationForm = $this->createForm(SessionformationUpdateLocationTForm::class, $sessionformation);
				$sessionformationUpdatePhoneContactCenterForm = $this->createForm(
					SessionformationUpdatePhoneContactCenterTForm::class,
					$sessionformation);
				$sessionformationUpdateConditionsReportForm = $this->createForm(
					SessionformationUpdateConditionsReportTForm::class,
					$sessionformation);
				$sessionformationUpdateDtInfoForm = $this->createForm(SessionformationUpdateDtInfoTForm::class, $sessionformation);
				$sessionformationUpdateOtherInfosForm = $this->createForm(SessionformationUpdateOtherInfosTForm::class, $sessionformation);
				$sessionformationUpdateMaxParticipantsForm = $this->createForm(
					SessionformationUpdateMaxParticipantsTForm::class,
					$sessionformation);
				$sessionformationUpdateLockoutForm = $this->createForm(SessionformationUpdateLockoutTForm::class, $sessionformation);
				$sessioninscriptionSendConvocationForm = $this->createForm(SessioninscriptionSendConvocationTForm::class, $sessionformation);

				$sessioninscription = new Sessioninscription();
				$sessioninscription->setSessionformation($sessionformation);
				$sessioninscriptionNewForm = $this->createForm(
					SessioninscriptionNewTForm::class,
					$sessioninscription,
					array(
						'sessionformation' => $sessionformation
					));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['SessionformationUpdateModuleformationForm'])) {
					$sessionformationUpdateModuleformationForm->handleRequest($request);
					if ($sessionformationUpdateModuleformationForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateCodeForm'])) {
					$sessionformationUpdateCodeForm->handleRequest($request);
					if ($sessionformationUpdateCodeForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateTitleForm'])) {
					$sessionformationUpdateTitleForm->handleRequest($request);
					if ($sessionformationUpdateTitleForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateDtStartForm'])) {
					$sessionformationUpdateDtStartForm->handleRequest($request);
					if ($sessionformationUpdateDtStartForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateLocationForm'])) {
					$sessionformationUpdateLocationForm->handleRequest($request);
					if ($sessionformationUpdateLocationForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdatePhoneContactCenterForm'])) {
					$sessionformationUpdatePhoneContactCenterForm->handleRequest($request);
					if ($sessionformationUpdatePhoneContactCenterForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateConditionsReportForm'])) {
					$sessionformationUpdateConditionsReportForm->handleRequest($request);
					if ($sessionformationUpdateConditionsReportForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateDtInfoForm'])) {
					$sessionformationUpdateDtInfoForm->handleRequest($request);
					if ($sessionformationUpdateDtInfoForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateOtherInfosForm'])) {
					$sessionformationUpdateOtherInfosForm->handleRequest($request);
					if ($sessionformationUpdateOtherInfosForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateMaxParticipantsForm'])) {
					$sessionformationUpdateMaxParticipantsForm->handleRequest($request);
					if ($sessionformationUpdateMaxParticipantsForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationUpdateLockoutForm'])) {
					$sessionformationUpdateLockoutForm->handleRequest($request);
					if ($sessionformationUpdateLockoutForm->isValid()) {
						$em->persist($sessionformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Sessionformation.edit.success',
								array(
									'%sessionformation%' => $sessionformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
					}
				} elseif (isset($reqData['SessioninscriptionSendConvocationForm'])) {
					$sessioninscriptionSendConvocationForm->handleRequest($request);
					if ($sessioninscriptionSendConvocationForm->isValid()) {
						$countSessioninscriptions = 0;
						$mailsent = 0;

						foreach ($sessionformation->getSessionInscriptions() as $sessioninscription) {
							$trainee = $sessioninscription->getUser();


							$countSessioninscriptions++;

							if ($sessioninscription->getConvocation() == Sessioninscription::CONVOCATION_NOTSENT) {

								$mvars = array();
								$message = \Swift_Message::newInstance();
								$mvars['user'] = $trainee;
								$mvars['sessionformation'] = $sessionformation;
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

								$modulepreinscription = $em->getRepository('IlcfranceOrangetoolsDataBundle:Modulepreinscription')->findOneBy(array('moduleformation' => $sessioninscription->getSessionformation()->getModuleformation(), 'user' => $sessioninscription->getUser()));

								$modulepreinscription->setLockout(Modulepreinscription::LOCKOUT_LOCKED);
								$em->persist($modulepreinscription);
								$mailsent++;
							}
						}


						$em->flush();

						$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.mail.convocation.sent', array('%countSessioninscriptions%' => $countSessioninscriptions, '%$mailsent%' => $mailsent)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 1);
						$this->getSession()->set('tabActive', 1);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.edit.failure'));
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
									'%user%' => $sessioninscription->getUser()
										->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_sessionformation_editGet',
								array(
									'id' => $sessionformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 3);
						$this->getSession()->set('tabActive', 3);

						$em->refresh($sessionformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessioninscription.add.failure'));
					}
				}

				$this->addTwigVar('sessionformation', $sessionformation);
				$this->addTwigVar('sessioninscription', $sessioninscription);
				$this->addTwigVar('SessionformationUpdateModuleformationForm', $sessionformationUpdateModuleformationForm->createView());
				$this->addTwigVar('SessionformationUpdateCodeForm', $sessionformationUpdateCodeForm->createView());
				$this->addTwigVar('SessionformationUpdateTitleForm', $sessionformationUpdateTitleForm->createView());
				$this->addTwigVar('SessionformationUpdateDtStartForm', $sessionformationUpdateDtStartForm->createView());
				$this->addTwigVar('SessionformationUpdateLocationForm', $sessionformationUpdateLocationForm->createView());
				$this->addTwigVar(
					'SessionformationUpdatePhoneContactCenterForm',
					$sessionformationUpdatePhoneContactCenterForm->createView());
				$this->addTwigVar('SessionformationUpdateConditionsReportForm', $sessionformationUpdateConditionsReportForm->createView());
				$this->addTwigVar('SessionformationUpdateDtInfoForm', $sessionformationUpdateDtInfoForm->createView());
				$this->addTwigVar('SessionformationUpdateOtherInfosForm', $sessionformationUpdateOtherInfosForm->createView());
				$this->addTwigVar('SessionformationUpdateMaxParticipantsForm', $sessionformationUpdateMaxParticipantsForm->createView());
				$this->addTwigVar('SessionformationUpdateLockoutForm', $sessionformationUpdateLockoutForm->createView());
				$this->addTwigVar('SessioninscriptionSendConvocationForm', $sessioninscriptionSendConvocationForm->createView());
				$this->addTwigVar('SessioninscriptionNewForm', $sessioninscriptionNewForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessionformation.edit.browserpagetitle',
						array(
							'%sessionformation%' => $sessionformation->getCode()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Sessionformation.edit.pagetitle',
						array(
							'%sessionformation%' => $sessionformation->getCode()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$sessionformation->getId(),
					Trace::AE_Sessionformation);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Sessionformation:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

}