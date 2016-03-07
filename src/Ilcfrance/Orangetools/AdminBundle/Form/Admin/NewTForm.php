<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Admin;

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

		$builder->add('firstName', TextType::class, array('label' => 'ilcfrance.orangetools.admin.AdminNewForm.firstName.label'));

		$builder->add('lastName', TextType::class, array('label' => 'ilcfrance.orangetools.admin.AdminNewForm.lastName.label'));

		$builder->add('username', TextType::class, array('label' => 'ilcfrance.orangetools.admin.AdminNewForm.username.label'));

		$builder->add('email', EmailType::class, array('label' => 'ilcfrance.orangetools.admin.AdminNewForm.email.label'));

		$builder->add('phone', TextType::class, array('label' => 'ilcfrance.orangetools.admin.AdminNewForm.phone.label', 'required' => false));

		$builder->add('mobile', TextType::class, array('label' => 'ilcfrance.orangetools.admin.AdminNewForm.mobile.label', 'required' => false));
	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{
		return 'AdminNewForm';
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
		return array('validation_groups' => array('firstName', 'lastName', 'username', 'email', 'phone', 'mobile'));
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