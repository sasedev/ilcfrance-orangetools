<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\NewTForm as ModuleformationNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\ImportTForm as ModuleformationImportTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\UpdateCodeTForm as ModuleformationUpdateCodeTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\UpdateGroupmoduleTForm as ModuleformationUpdateGroupmoduleTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\UpdateTitleTForm as ModuleformationUpdateTitleTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\UpdateDescriptionTForm as ModuleformationUpdateDescriptionTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation\NewTForm as SessionformationNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription\NewTForm as ModulepreinscriptionNewTForm;
use Ilcfrance\Orangetools\DataBundle\Entity\Groupmodule;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class ModuleformationController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_moduleformations');

	}

	public function listAction()
	{

		$this->addTwigVar('menu_active', 'admin_moduleformations_list');

		$em = $this->getEntityManager();

		$moduleformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->getAll();
		$this->addTwigVar('moduleformations', $moduleformations);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:list.html.twig', $this->getTwigVars());

	}

	public function listByGroupmoduleAction($id)
	{

		$this->addTwigVar('menu_active', 'admin_moduleformations_list');

		$em = $this->getEntityManager();

		$groupmodule = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->find($id);

		if (null == $groupmodule) {
			$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.notfound'));
			$this->redirect($this->generateUrl('ilcfrance_orangetools_admin_moduleformation_list'));
		}

		$moduleformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->getAllByGroupmodule($groupmodule);
		$this->addTwigVar('moduleformations', $moduleformations);
		$this->addTwigVar('groupmodule', $groupmodule);

		$this->setBrowserPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Moduleformation.listByGroupmodule.browserpagetitle',
				array(
					'%groupmodule%' => $groupmodule->getName()
				)));
		$this->setPageTitle(
			$this->translate(
				'ilcfrance.orangetools.admin.Moduleformation.listByGroupmodule.pagetitle',
				array(
					'%groupmodule%' => $groupmodule->getName()
				)));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:listByGroupmodule.html.twig', $this->getTwigVars());

	}

	public function exportAction()
	{

		$em = $this->getEntityManager();

		$moduleformations = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->getAll();

		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()
			->setCreator("Salah Abdelkader Seif Eddine")
			->setLastModifiedBy($this->getSecurityTokenStorage()
			->getToken()
			->getUser()
			->getFullname())
			->setTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle'))
			->setSubject($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle'))
			->setDescription($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle'))
			->setKeywords($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle'))
			->setCategory("Moduleformation");

		$phpExcelObject->setActiveSheetIndex(0);

		$workSheet = $phpExcelObject->getActiveSheet();
		$workSheet->setTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle'));

		$workSheet->setCellValue('A1', $this->translate('Moduleformation.groupmodule'));
		$workSheet->getStyle('A1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('B1', $this->translate('Moduleformation.code'));
		$workSheet->getStyle('B1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('C1', $this->translate('Moduleformation.title'));
		$workSheet->getStyle('C1')
			->getFont()
			->setBold(true);
		$workSheet->setCellValue('D1', $this->translate('Moduleformation.description'));
		$workSheet->getStyle('D1')
			->getFont()
			->setBold(true);

		$workSheet->getStyle('A1:D1')->applyFromArray(
			array(
				'fill' => array(
					'type' => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array(
						'rgb' => 'FF6600'
					)
				)
			));

		$i = 1;

		foreach ($moduleformations as $moduleformation) {
			$i ++;
			$workSheet->setCellValue('A' . $i, $moduleformation->getGroupmodule()
				->getName(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('B' . $i, $moduleformation->getCode(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('C' . $i, $moduleformation->getTitle(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('D' . $i, \strip_tags($moduleformation->getDescription()), \PHPExcel_Cell_DataType::TYPE_STRING2);

			if ($i % 2 == 1) {
				$workSheet->getStyle('A' . $i . ':D' . $i)->applyFromArray(
					array(
						'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array(
								'rgb' => 'FFF71C'
							)
						)
					));
			} else {
				$workSheet->getStyle('A' . $i . ':D' . $i)->applyFromArray(
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

		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$response = $this->get('phpexcel')->createStreamedResponse($writer);

		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');

		$now = new \DateTime('now');
		$filename = $this->normalize(
			$this->getParameter('sitename') . '_' . $this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle').'_'.$now->format('Y-m-d_H.i.s'));
		$filename = str_ireplace('"', '|', $filename);
		$filename = str_ireplace(' ', '_', $filename);

		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename . '.xlsx');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');

		return $response;

	}

	public function addGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_moduleformations_add');

		$moduleformation = new Moduleformation();
		$moduleformationNewForm = $this->createForm(ModuleformationNewTForm::class, $moduleformation);

		$this->addTwigVar('moduleformation', $moduleformation);
		$this->addTwigVar('ModuleformationNewForm', $moduleformationNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_moduleformations_add');

		$moduleformation = new Moduleformation();
		$moduleformationNewForm = $this->createForm(ModuleformationNewTForm::class, $moduleformation);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['ModuleformationNewForm'])) {
			$moduleformationNewForm->handleRequest($request);
			if ($moduleformationNewForm->isValid()) {
				$em = $this->getEntityManager();

				$em->persist($moduleformation);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Moduleformation.add.success',
						array(
							'%moduleformation%' => $moduleformation->getCode()
						)));

				return $this->redirect(
					$this->generateUrl(
						'ilcfrance_orangetools_admin_moduleformation_editGet',
						array(
							'id' => $moduleformation->getId()
						)));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.add.failure'));
			}
		}

		$this->addTwigVar('moduleformation', $moduleformation);
		$this->addTwigVar('ModuleformationNewForm', $moduleformationNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:add.html.twig', $this->getTwigVars());

	}

	public function importGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_moduleformations_import');

		$moduleformationImportForm = $this->createForm(ModuleformationImportTForm::class);

		$this->addTwigVar('ModuleformationImportForm', $moduleformationImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:import.html.twig', $this->getTwigVars());

	}

	public function importPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_moduleformations_import');

		$moduleformationImportForm = $this->createForm(ModuleformationImportTForm::class);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['ModuleformationImportForm'])) {
			$moduleformationImportForm->handleRequest($request);
			if ($moduleformationImportForm->isValid()) {
				ini_set('memory_limit', '4096M');
				ini_set('max_execution_time', '0');

				$em = $this->getEntityManager();

				$extension = $moduleformationImportForm['excel']->getData()->guessExtension();
				if ($extension == 'zip') {
					$extension = 'xlsx';
				}

				$now = new \DateTime('now');
				$filename = $this->normalize($this->translate('ilcfrance.orangetools.admin.Moduleformation.list.browserpagetitle')).'_'.$now->format('Y-m-d_H.i.s').'_'.uniqid() . '.' . $extension;
				$moduleformationImportForm['excel']->getData()->move($this->getParameter('adapter_files'), $filename);
				$fullfilename = $this->getParameter('adapter_files');
				$fullfilename .= '/' . $filename;

				$excelObj = $this->get('phpexcel')->createPHPExcelObject($fullfilename);

				$log = "";

				$activeSheetIndex = 0;

				$excelObj->setActiveSheetIndex($activeSheetIndex);
				$worksheet = $excelObj->getActiveSheet();
				$highestRow = $worksheet->getHighestRow();
				$lineRead = 0;
				$moduleformationNew = 0;
				$lineUnprocessed = 0;
				$lineError = 0;
				for ($row = 2; $row <= $highestRow; $row ++) {
					$lineRead ++;

					$xlsGroupmodule = \trim(\strval($worksheet->getCellByColumnAndRow(0, $row)->getValue()));
					$xlsCode = \trim(\strval($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
					$xlsTitle = \trim(\strval($worksheet->getCellByColumnAndRow(2, $row)->getValue()));
					$xlsDescription = \nl2br(\trim(\strval($worksheet->getCellByColumnAndRow(3, $row)->getValue())));

					if ($xlsGroupmodule != "" && $xlsCode != "" && $xlsTitle != "") {
						$groupmodule = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->findOneBy(
							array(
								'name' => $xlsGroupmodule
							));
						if (null != $groupmodule) {
							$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->findOneBy(
								array(
									'code' => $xlsCode
								));
							if (null == $moduleformation) {
								$moduleformation = new Moduleformation();
								$moduleformation->setGroupmodule($groupmodule);
								$moduleformation->setCode($xlsCode);
								$moduleformation->setTitle($xlsTitle);
								$moduleformation->setDescription($xlsDescription);

								$em->persist($moduleformation);
								$moduleformationNew ++;
							} else {
								$lineUnprocessed ++;
								$log .= "le Code  <b>" . $xlsCode . "</b> à la ligne " . $row . " existe déjà<br>";
							}
						} else {
							$lineError ++;
							$log .= "le Groupe de Module <b>" . $xlsGroupmodule . "</b> à la ligne " . $row . " n'existe pas<br>";
						}
					} else {
						$lineError ++;
						$log .= "la ligne " . $row . " ne contient pas de Module de formation valide<br>";
					}
				}
				$em->flush();

				$log .= $lineRead . " lignes lues<br>";
				$log .= $moduleformationNew . " nouveaux Module de Formations<br>";
				$log .= $lineUnprocessed . " Module de Formations déjà dans la base<br>";
				$log .= $lineError . " lignes contenant des erreurs<br>";

				$this->addFlash('log', $log);

				$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Moduleformation.import.success'));

				return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_moduleformation_importGet'));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.import.failure'));
			}
		}

		$this->addTwigVar('ModuleformationImportForm', $moduleformationImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Moduleformation.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:import.html.twig', $this->getTwigVars());

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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_moduleformation_list');
		}
		$em = $this->getEntityManager();
		try {
			$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->find($id);

			if (null == $moduleformation) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Moduleformation.delete.notfound'));
			} else {
				$em->remove($moduleformation);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate(
						'ilcfrance.orangetools.admin.Moduleformation.delete.success',
						array(
							'%moduleformation%' => $moduleformation->getCode()
						)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.delete.failure'));
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_moduleformation_list');
		}

		$em = $this->getEntityManager();
		try {
			$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->find($id);

			if (null == $moduleformation) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.notfound'));
			} else {
				$moduleformationUpdateGroupmoduleForm = $this->createForm(ModuleformationUpdateGroupmoduleTForm::class, $moduleformation);
				$moduleformationUpdateCodeForm = $this->createForm(ModuleformationUpdateCodeTForm::class, $moduleformation);
				$moduleformationUpdateTitleForm = $this->createForm(ModuleformationUpdateTitleTForm::class, $moduleformation);
				$moduleformationUpdateDescriptionForm = $this->createForm(ModuleformationUpdateDescriptionTForm::class, $moduleformation);

				$sessionformation = new Sessionformation();
				$sessionformation->setModuleformation($moduleformation);
				$sessionformationNewForm = $this->createForm(
					SessionformationNewTForm::class,
					$sessionformation,
					array(
						'moduleformation' => $moduleformation
					));

				$modulepreinscription = new Modulepreinscription();
				$modulepreinscription->setModuleformation($moduleformation);
				$modulepreinscriptionNewForm = $this->createForm(
					ModulepreinscriptionNewTForm::class,
					$modulepreinscription,
					array(
						'moduleformation' => $moduleformation
					));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$this->addTwigVar('moduleformation', $moduleformation);
				$this->addTwigVar('sessionformation', $sessionformation);
				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('ModuleformationUpdateGroupmoduleForm', $moduleformationUpdateGroupmoduleForm->createView());
				$this->addTwigVar('ModuleformationUpdateCodeForm', $moduleformationUpdateCodeForm->createView());
				$this->addTwigVar('ModuleformationUpdateTitleForm', $moduleformationUpdateTitleForm->createView());
				$this->addTwigVar('ModuleformationUpdateDescriptionForm', $moduleformationUpdateDescriptionForm->createView());
				$this->addTwigVar('SessionformationNewForm', $sessionformationNewForm->createView());
				$this->addTwigVar('ModulepreinscriptionNewForm', $modulepreinscriptionNewForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Moduleformation.edit.browserpagetitle',
						array(
							'%moduleformation%' => $moduleformation->getCode()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Moduleformation.edit.pagetitle',
						array(
							'%moduleformation%' => $moduleformation->getCode()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$moduleformation->getId(),
					Trace::AE_Moduleformation);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:edit.html.twig', $this->getTwigVars());
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_moduleformation_list');
		}

		$em = $this->getEntityManager();
		try {
			$moduleformation = $em->getRepository('IlcfranceOrangetoolsDataBundle:Moduleformation')->find($id);

			if (null == $moduleformation) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.notfound'));
			} else {
				$moduleformationUpdateGroupmoduleForm = $this->createForm(ModuleformationUpdateGroupmoduleTForm::class, $moduleformation);
				$moduleformationUpdateCodeForm = $this->createForm(ModuleformationUpdateCodeTForm::class, $moduleformation);
				$moduleformationUpdateTitleForm = $this->createForm(ModuleformationUpdateTitleTForm::class, $moduleformation);
				$moduleformationUpdateDescriptionForm = $this->createForm(ModuleformationUpdateDescriptionTForm::class, $moduleformation);

				$sessionformation = new Sessionformation();
				$sessionformation->setModuleformation($moduleformation);
				$sessionformationNewForm = $this->createForm(
					SessionformationNewTForm::class,
					$sessionformation,
					array(
						'moduleformation' => $moduleformation
					));

				$modulepreinscription = new Modulepreinscription();
				$modulepreinscription->setModuleformation($moduleformation);
				$modulepreinscriptionNewForm = $this->createForm(
					ModulepreinscriptionNewTForm::class,
					$modulepreinscription,
					array(
						'moduleformation' => $moduleformation
					));

				$this->addTwigVar('tabActive', $this->getSession()
					->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()
					->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['ModuleformationUpdateGroupmoduleForm'])) {
					$moduleformationUpdateGroupmoduleForm->handleRequest($request);
					if ($moduleformationUpdateGroupmoduleForm->isValid()) {
						$em->persist($moduleformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Moduleformation.edit.success',
								array(
									'%moduleformation%' => $moduleformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_moduleformation_editGet',
								array(
									'id' => $moduleformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($moduleformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.failure'));
					}
				} elseif (isset($reqData['ModuleformationUpdateCodeForm'])) {
					$moduleformationUpdateCodeForm->handleRequest($request);
					if ($moduleformationUpdateCodeForm->isValid()) {
						$em->persist($moduleformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Moduleformation.edit.success',
								array(
									'%moduleformation%' => $moduleformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_moduleformation_editGet',
								array(
									'id' => $moduleformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($moduleformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.failure'));
					}
				} elseif (isset($reqData['ModuleformationUpdateTitleForm'])) {
					$moduleformationUpdateTitleForm->handleRequest($request);
					if ($moduleformationUpdateTitleForm->isValid()) {
						$em->persist($moduleformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Moduleformation.edit.success',
								array(
									'%moduleformation%' => $moduleformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_moduleformation_editGet',
								array(
									'id' => $moduleformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($moduleformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.failure'));
					}
				} elseif (isset($reqData['ModuleformationUpdateDescriptionForm'])) {
					$moduleformationUpdateDescriptionForm->handleRequest($request);
					if ($moduleformationUpdateDescriptionForm->isValid()) {
						$em->persist($moduleformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate(
								'ilcfrance.orangetools.admin.Moduleformation.edit.success',
								array(
									'%moduleformation%' => $moduleformation->getCode()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_moduleformation_editGet',
								array(
									'id' => $moduleformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($moduleformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Moduleformation.edit.failure'));
					}
				} elseif (isset($reqData['SessionformationNewForm'])) {
					$sessionformationNewForm->handleRequest($request);
					if ($sessionformationNewForm->isValid()) {
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
								'ilcfrance_orangetools_admin_moduleformation_editGet',
								array(
									'id' => $moduleformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 3);
						$this->getSession()->set('tabActive', 3);

						$em->refresh($moduleformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Sessionformation.add.failure'));
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
									'%moduleformation%' => $moduleformation->getCode(),
									'%user%' => $modulepreinscription->getUser()
										->getFullName()
								)));

						return $this->redirect(
							$this->generateUrl(
								'ilcfrance_orangetools_admin_moduleformation_editGet',
								array(
									'id' => $moduleformation->getId()
								)));
					} else {
						$this->addTwigVar('tabActive', 4);
						$this->getSession()->set('tabActive', 4);

						$em->refresh($moduleformation);

						$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Modulepreinscription.add.failure'));
					}
				}

				$this->addTwigVar('moduleformation', $moduleformation);
				$this->addTwigVar('sessionformation', $sessionformation);
				$this->addTwigVar('modulepreinscription', $modulepreinscription);
				$this->addTwigVar('ModuleformationUpdateGroupmoduleForm', $moduleformationUpdateGroupmoduleForm->createView());
				$this->addTwigVar('ModuleformationUpdateCodeForm', $moduleformationUpdateCodeForm->createView());
				$this->addTwigVar('ModuleformationUpdateTitleForm', $moduleformationUpdateTitleForm->createView());
				$this->addTwigVar('ModuleformationUpdateDescriptionForm', $moduleformationUpdateDescriptionForm->createView());
				$this->addTwigVar('SessionformationNewForm', $sessionformationNewForm->createView());
				$this->addTwigVar('ModulepreinscriptionNewForm', $modulepreinscriptionNewForm->createView());

				$this->setBrowserPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Moduleformation.edit.browserpagetitle',
						array(
							'%moduleformation%' => $moduleformation->getCode()
						)));
				$this->setPageTitle(
					$this->translate(
						'ilcfrance.orangetools.admin.Moduleformation.edit.pagetitle',
						array(
							'%moduleformation%' => $moduleformation->getCode()
						)));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId(
					$moduleformation->getId(),
					Trace::AE_Moduleformation);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Moduleformation:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);

	}

}