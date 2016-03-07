<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class LoginTForm extends AbstractType
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
			'username',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.security.LoginForm.username.label',
				'constraints' => array(
					new NotBlank(),
					new Length(array(
						'min' => 3,
						'max' => 50
					))
				)
			));

		$builder->add(
			'password',
			PasswordType::class,
			array(
				'label' => 'ilcfrance.orangetools.security.LoginForm.password.label',
				'constraints' => array(
					new NotBlank(),
					new Length(array(
						'min' => 8
					))
				)
			));

		$builder->add(
			'remember_me',
			CheckboxType::class,
			array(
				'label' => 'ilcfrance.orangetools.security.LoginForm.rememberme.label',
				'required' => false
			));

		$builder->add('target_path', HiddenType::class, array(
			'required' => false
		));

	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 */
	public function getName()
	{

		return "LoginForm";

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
			'csrf_protection' => false
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
