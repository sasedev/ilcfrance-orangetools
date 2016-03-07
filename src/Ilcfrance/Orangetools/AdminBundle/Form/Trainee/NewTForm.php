<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Trainee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class NewTForm extends AbstractType
{

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder->add('firstName', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.firstName.label'));

		$builder->add('lastName', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.lastName.label'));

		$builder->add('username', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.username.label'));

		$builder->add('email', EmailType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.email.label'));

		$builder->add('phone', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.phone.label', 'required' => false));

		$builder->add('mobile', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.mobile.label', 'required' => false));

		$builder->add('job', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.job.label', 'required' => false));

		$builder->add('level', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.level.label', 'required' => false));

		$builder->add('firstName2', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.firstName2.label'));

		$builder->add('lastName2', TextType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.lastName2.label'));

		$builder->add('email2', EmailType::class, array('label' => 'ilcfrance.orangetools.admin.TraineeNewForm.email2.label'));
	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{
		return 'TraineeNewForm';
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
		return array('validation_groups' => array('firstName', 'lastName', 'username', 'email', 'phone', 'mobile', 'job', 'level', 'firstName2', 'lastName2', 'email2'));
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