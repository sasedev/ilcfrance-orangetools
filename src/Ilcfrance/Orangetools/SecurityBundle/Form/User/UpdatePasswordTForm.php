<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class UpdatePasswordTForm extends AbstractType
{

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('oldPassword', PasswordType::class, array('label' => 'ilcfrance.orangetools.security.UserUpdatePasswordForm.oldPassword.label', 'mapped' => false));

		$builder->add('clearPassword', RepeatedType::class,
			array('type' => PasswordType::class, 'invalid_message' => 'ilcfrance.orangetools.security.UserUpdatePasswordForm.clearPassword.repeat.notequal',
				'first_options' => array('label' => 'ilcfrance.orangetools.security.UserUpdatePasswordForm.clearPassword.first.label', 'attr' => array('label_col' => 4, 'widget_col' => 8, 'placeholder' => 'ilcfrance.orangetools.security.UserUpdatePasswordForm.clearPassword.first.placeholder', 'input_group' => array('append' => '<span class="fa fa-lock fa-fw"></span>'))),
				'second_options' => array('label' => 'ilcfrance.orangetools.security.UserUpdatePasswordForm.clearPassword.second.label', 'attr' => array('label_col' => 4, 'widget_col' => 8, 'placeholder' => 'ilcfrance.orangetools.security.UserUpdatePasswordForm.clearPassword.second.placeholder', 'input_group' => array('append' => '<span class="fa fa-lock fa-fw"></span>')))));
	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{
		return 'UserUpdatePasswordForm';
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
		return array('validation_groups' => array('clearPassword'));
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
