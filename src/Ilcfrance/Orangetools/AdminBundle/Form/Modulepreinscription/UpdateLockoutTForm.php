<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;

/**
 *
 * @author sasedev <seif.salah@mfail.com>
 */
class UpdateLockoutTForm extends AbstractType
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
			'lockout',
			ChoiceType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionUpdateLockoutForm.lockout.label',
				'choices_as_values' => true,
				'choices' => Modulepreinscription::choiceLockout(),
				'attr' => array(
					'choice_label_trans' => true
				)
			));

	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{

		return 'ModulepreinscriptionUpdateLockoutForm';

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
				'lockout'
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