<?php

namespace Ilcfrance\Orangetools\AdminBundle\Controller;

use Ilcfrance\Orangetools\ResBundle\Controller\SasedevController;
use Ilcfrance\Orangetools\DataBundle\Entity\Groupmodule;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\AdminBundle\Form\Groupmodule\NewTForm as GroupmoduleNewTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Groupmodule\ImportTForm as GroupmoduleImportTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Groupmodule\UpdateNameTForm as GroupmoduleUpdateNameTForm;
use Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation\NewTForm as ModuleformationNewTForm;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class GroupmoduleController extends SasedevController
{

	public function __construct()
	{

		parent::__construct();
		$this->addTwigVar('menu_active', 'admin_groupmodules');

	}

	public function listAction()
	{
		$this->addTwigVar('menu_active', 'admin_groupmodules_list');

		$em = $this->getEntityManager();

		$groupmodules = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->getAll();
		$this->addTwigVar('groupmodules', $groupmodules);

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:list.html.twig', $this->getTwigVars());

	}

	public function exportAction()
	{

		$em = $this->getEntityManager();

		$groupmodules = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->getAll();

		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()
			->setCreator("Salah Abdelkader Seif Eddine")
			->setLastModifiedBy($this->getSecurityTokenStorage()
			->getToken()
			->getUser()
			->getFullname())
			->setTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'))
			->setSubject($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'))
			->setDescription($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'))
			->setKeywords($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'))
			->setCategory("Groupmodule");

		$phpExcelObject->setActiveSheetIndex(0);

		$workSheet = $phpExcelObject->getActiveSheet();
		$workSheet->setTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'));

		$workSheet->setCellValue('A1', $this->translate('Groupmodule.name'));
		$workSheet->getStyle('A1')
			->getFont()
			->setBold(true);

		$workSheet->getStyle('A1')->applyFromArray(
			array(
				'fill' => array(
					'type' => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array(
						'rgb' => 'FF6600'
					)
				)
			));

		$i = 1;

		foreach ($groupmodules as $groupmodule) {
			$i ++;
			$workSheet->setCellValue('A' . $i, $groupmodule->getName(), \PHPExcel_Cell_DataType::TYPE_STRING2);

			if ($i % 2 == 1) {
				$workSheet->getStyle('A' . $i)->applyFromArray(
					array(
						'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array(
								'rgb' => 'FFF71C'
							)
						)
					));
			} else {
				$workSheet->getStyle('A' . $i)->applyFromArray(
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

		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$response = $this->get('phpexcel')->createStreamedResponse($writer);

		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');

		$filename = $this->normalize(
			$this->getParameter('sitename') . '_' . $this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle'));
		$filename = str_ireplace('"', '|', $filename);
		$filename = str_ireplace(' ', '_', $filename);

		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename . '.xlsx');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');

		return $response;

	}

	public function addGetAction()
	{
		$this->addTwigVar('menu_active', 'admin_groupmodules_add');

		$groupmodule = new Groupmodule();
		$groupmoduleNewForm = $this->createForm(GroupmoduleNewTForm::class, $groupmodule);


		$this->addTwigVar('groupmodule', $groupmodule);
		$this->addTwigVar('GroupmoduleNewForm', $groupmoduleNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:add.html.twig', $this->getTwigVars());

	}

	public function addPostAction()
	{
		$this->addTwigVar('menu_active', 'admin_groupmodules_add');

		$groupmodule = new Groupmodule();
		$groupmoduleNewForm = $this->createForm(GroupmoduleNewTForm::class, $groupmodule);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['GroupmoduleNewForm'])) {
			$groupmoduleNewForm->handleRequest($request);
			if ($groupmoduleNewForm->isValid()) {
				$em = $this->getEntityManager();

				$em->persist($groupmodule);
				$em->flush();
				$this->addFlash(
					'success',
					$this->translate('ilcfrance.orangetools.admin.Groupmodule.add.success', array('%groupmodule%' => $groupmodule->getName()))
				);

				return $this->redirect(
					$this->generateUrl('ilcfrance_orangetools_admin_groupmodule_editGet', array('id' => $groupmodule->getId()))
				);
			} else {

				$this->addFlash(
					'error',
					$this->translate('ilcfrance.orangetools.admin.Groupmodule.add.failure')
				);
			}
		}


		$this->addTwigVar('groupmodule', $groupmodule);
		$this->addTwigVar('GroupmoduleNewForm', $groupmoduleNewForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.add.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.add.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:add.html.twig', $this->getTwigVars());

	}

	public function importGetAction()
	{

		$this->addTwigVar('menu_active', 'admin_groupmodules_import');

		$groupmoduleImportForm = $this->createForm(GroupmoduleImportTForm::class);

		$this->addTwigVar('GroupmoduleImportForm', $groupmoduleImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:import.html.twig', $this->getTwigVars());

	}

	public function importPostAction()
	{

		$this->addTwigVar('menu_active', 'admin_groupmodules_import');

		$groupmoduleImportForm = $this->createForm(GroupmoduleImportTForm::class);

		$request = $this->getRequest();
		$reqData = $request->request->all();

		if (isset($reqData['GroupmoduleImportForm'])) {
			$groupmoduleImportForm->handleRequest($request);
			if ($groupmoduleImportForm->isValid()) {
				ini_set('memory_limit', '4096M');
				ini_set('max_execution_time', '0');

				$em = $this->getEntityManager();

				$extension = $groupmoduleImportForm['excel']->getData()->guessExtension();
				if ($extension == 'zip') {
					$extension = 'xlsx';
				}

				$now = new \DateTime('now');
				$filename = $this->normalize($this->translate('ilcfrance.orangetools.admin.Groupmodule.list.browserpagetitle')).'_'.$now->format('Y-m-d_H.i.s').'_'.uniqid() . '.' . $extension;
				$groupmoduleImportForm['excel']->getData()->move($this->getParameter('adapter_files'), $filename);
				$fullfilename = $this->getParameter('adapter_files');
				$fullfilename .= '/' . $filename;

				$excelObj = $this->get('phpexcel')->createPHPExcelObject($fullfilename);

				$log = "";

				$activeSheetIndex = 0;

				$excelObj->setActiveSheetIndex($activeSheetIndex);
				$worksheet = $excelObj->getActiveSheet();
				$highestRow = $worksheet->getHighestRow();
				$lineRead = 0;
				$groupmoduleNew = 0;
				$lineUnprocessed = 0;
				$lineError = 0;
				for ($row = 2; $row <= $highestRow; $row ++) {
					$lineRead ++;

					$xlsName = \trim(\strval($worksheet->getCellByColumnAndRow(0, $row)->getValue()));

					if ($xlsName != "") {
						$groupmodule = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->findOneBy(
							array(
								'name' => $xlsName
							));
						if (null == $groupmodule) {
							$groupmodule = new Groupmodule();
							$groupmodule->setName($xlsName);

							$em->persist($groupmodule);
							$groupmoduleNew ++;
						} else {
							$lineUnprocessed ++;
							$log .= "le Groupe de Modules  <b>" . $xlsName . "</b> à la ligne " . $row . " existe déjà<br>";
						}
					} else {
						$lineError ++;
						$log .= "la ligne " . $row . " ne contient pas de Groupe de Modules valide<br>";
					}
				}
				$em->flush();

				$log .= $lineRead . " lignes lues<br>";
				$log .= $groupmoduleNew . " nouveaux Groupes de Modules<br>";
				$log .= $lineUnprocessed . " Groupes de Modules déjà dans la base<br>";
				$log .= $lineError . " lignes contenant des erreurs<br>";

				$this->addFlash('log', $log);

				$this->addFlash('success', $this->translate('ilcfrance.orangetools.admin.Groupmodule.import.success'));

				return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_groupmodule_importGet'));
			} else {

				$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Groupmodule.import.failure'));
			}
		}

		$this->addTwigVar('GroupmoduleImportForm', $groupmoduleImportForm->createView());

		$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.import.browserpagetitle'));
		$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.import.pagetitle'));

		return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:import.html.twig', $this->getTwigVars());

	}

	public function deleteAction($id)
	{
		/*if (! $this->hasRole('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_orangetools_admin_homepage'));
		}*/
		$urlFrom = $this->getReferer();
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_groupmodule_list');
		}
		$em = $this->getEntityManager();
		try {
			$groupmodule = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->find($id);

			if (null == $groupmodule) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Groupmodule.delete.notfound'));
			} else {
				$em->remove($groupmodule);
				$em->flush();

				$this->addFlash(
					'success',
					$this->translate('ilcfrance.orangetools.admin.Groupmodule.delete.success', array('%groupmodule%' => $groupmodule->getName()))
				);
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine().' '.$e->getMessage().' '.$e->getTraceAsString());

			$this->addFlash('error', $this->translate('ilcfrance.orangetools.admin.Groupmodule.delete.failure'));
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_groupmodule_list');
		}

		$em = $this->getEntityManager();
		try {
			$groupmodule = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->find($id);

			if (null == $groupmodule) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.notfound'));
			} else {
				$groupmoduleUpdateNameForm = $this->createForm(GroupmoduleUpdateNameTForm::class, $groupmodule);

				$moduleformation = new Moduleformation();
				$moduleformation->setGroupmodule($groupmodule);
				$moduleformationNewForm = $this->createForm(ModuleformationNewTForm::class, $moduleformation, array('groupmodule' => $groupmodule));

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$this->addTwigVar('groupmodule', $groupmodule);
				$this->addTwigVar('GroupmoduleUpdateNameForm', $groupmoduleUpdateNameForm->createView());

				$this->addTwigVar('moduleformation', $moduleformation);
				$this->addTwigVar('ModuleformationNewForm', $moduleformationNewForm->createView());

				$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.browserpagetitle', array('%groupmodule%' => $groupmodule->getName())));
				$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.pagetitle', array('%groupmodule%' => $groupmodule->getName())));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId($groupmodule->getId(), Trace::AE_Groupmodule);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:edit.html.twig', $this->getTwigVars());
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
			$urlFrom = $this->generateUrl('ilcfrance_orangetools_admin_groupmodule_list');
		}

		$em = $this->getEntityManager();
		try {
			$groupmodule = $em->getRepository('IlcfranceOrangetoolsDataBundle:Groupmodule')->find($id);

			if (null == $groupmodule) {
				$this->addFlash('warning', $this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.notfound'));
			} else {
				$groupmoduleUpdateNameForm = $this->createForm(GroupmoduleUpdateNameTForm::class, $groupmodule);

				$moduleformation = new Moduleformation();
				$moduleformation->setGroupmodule($groupmodule);
				$moduleformationNewForm = $this->createForm(ModuleformationNewTForm::class, $moduleformation, array('groupmodule' => $groupmodule));

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$this->addTwigVar('stabActive', $this->getSession()->get('stabActive', 1));
				$this->getSession()->remove('stabActive');

				$request = $this->getRequest();
				$reqData = $request->request->all();

				if (isset($reqData['GroupmoduleUpdateNameForm'])) {
					$groupmoduleUpdateNameForm->handleRequest($request);
					if ($groupmoduleUpdateNameForm->isValid()) {
						$em->persist($groupmodule);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.success', array('%groupmodule%' => $groupmodule->getName()))
						);

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_groupmodule_editGet', array('id' => $groupmodule->getId()))
						);
					} else {
						$this->addTwigVar('tabActive', 2);
						$this->getSession()->set('tabActive', 2);

						$em->refresh($groupmodule);

						$this->addFlash(
							'error',
							$this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.failure')
						);
					}
				} elseif (isset($reqData['ModuleformationNewForm'])) {
					$moduleformationNewForm->handleRequest($request);
					if ($moduleformationNewForm->isValid()) {
						$em->persist($moduleformation);
						$em->flush();
						$this->addFlash(
							'success',
							$this->translate('ilcfrance.orangetools.admin.Moduleformation.add.success', array('%moduleformation%' => $moduleformation->getCode()))
						);

						return $this->redirect(
							$this->generateUrl('ilcfrance_orangetools_admin_groupmodule_editGet', array('id' => $groupmodule->getId()))
						);
					} else {
						$this->addTwigVar('tabActive', 3);
						$this->getSession()->set('tabActive', 3);

						$em->refresh($groupmodule);

						$this->addFlash(
							'error',
							$this->translate('ilcfrance.orangetools.admin.Moduleformation.add.error')
						);
					}
				}

				$this->addTwigVar('groupmodule', $groupmodule);
				$this->addTwigVar('GroupmoduleUpdateNameForm', $groupmoduleUpdateNameForm->createView());

				$this->addTwigVar('moduleformation', $moduleformation);
				$this->addTwigVar('ModuleformationNewForm', $moduleformationNewForm->createView());



				$this->setBrowserPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.browserpagetitle', array('%groupmodule%' => $groupmodule->getName())));
				$this->setPageTitle($this->translate('ilcfrance.orangetools.admin.Groupmodule.edit.pagetitle', array('%groupmodule%' => $groupmodule->getName())));

				$dm = $this->container->get('doctrine_mongodb')->getManager();
				$traces = $dm->getRepository('IlcfranceOrangetoolsDataBundle:Trace')->getAllByEntityId($groupmodule->getId(), Trace::AE_Groupmodule);
				$this->addTwigVar('traces', \array_reverse($traces->toArray()));

				return $this->render('IlcfranceOrangetoolsAdminBundle:Groupmodule:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine().' '.$e->getMessage().' '.$e->getTraceAsString());
		}

		return $this->redirect($urlFrom);
	}

}