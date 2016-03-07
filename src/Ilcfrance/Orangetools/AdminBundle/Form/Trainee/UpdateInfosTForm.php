<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Trainee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class UpdateInfosTForm extends AbstractType
{

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder->add('firstName', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeUpdateInfosForm.firstName.label'));

		$builder->add('lastName', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeUpdateInfosForm.lastName.label'));

		$builder->add('phone', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeUpdateInfosForm.phone.label', 'required' => false));

		$builder->add('mobile', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeUpdateInfosForm.mobile.label', 'required' => false));

		$builder->add('job', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeUpdateInfosForm.job.label', 'required' => false));

		$builder->add('level', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeUpdateInfosForm.level.label', 'required' => false));
	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{
		return 'TraineeUpdateInfosForm';
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
		return array('validation_groups' => array('firstName', 'lastName', 'phone', 'mobile', 'job', 'level'));
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