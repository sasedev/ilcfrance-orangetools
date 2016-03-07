<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class ImportTForm extends AbstractType
{

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder->add(
			'excel',
			FileType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationImportForm.excel.label',
				'constraints' => array(
					new File(
						array(
							'mimeTypes' => array(
								'application/vnd.ms-excel',
								'application/msexcel',
								'application/x-msexcel',
								'application/x-ms-excel',
								'application/x-excel',
								'application/x-dos_ms_excel',
								'application/xls',
								'application/x-xls',
								'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
								'application/vnd.ms-office',
								'application/vnd.ms-excel'
							),
							'maxSize' => "20480k"
						))
				),
				'mapped' => false
			));

	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{

		return 'SessionformationImportForm';

	}

	/**
	 *
	 * {@inheritDoc} @see AbstractType::getBlockPrefix()
	 */
	public function getBlockPrefix()
	{

		return $this->getName();

	}

	/**
	 * get the default options
	 *
	 * @return multitype:string multitype:string
	 */
	public function getDefaultOptions()
	{

		return array(
			'validation_groups' => array(
				'excel'
			)
		);

	}

	/**
	 *
	 * {@inheritDoc} @see AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver)
	{

		$resolver->setDefaults($this->getDefaultOptions());

	}

}