<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;

/**
 *
 * @author sasedev <seif.salah@mfail.com>
 */
class UpdateDtStartTForm extends AbstractType
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
			'dtStart',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationUpdateDtStartForm.dtStart.label'
			));

		$builder->add(
			'dtEnd',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationUpdateDtStartForm.dtEnd.label'
			));

	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{

		return 'SessionformationUpdateDtStartForm';

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
				'dtStart'
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