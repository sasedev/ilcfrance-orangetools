<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription;

use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\ModuleformationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class UpdateModuleformationTForm extends AbstractType
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
			'moduleformation',
			EntityType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionUpdateModuleformationForm.moduleformation.label',
				'class' => 'IlcfranceOrangetoolsDataBundle:Moduleformation',
				'query_builder' => function (ModuleformationRepository $sfr)
				{
					return $sfr->createQueryBuilder('mf')
						->orderBy('mf.code', 'ASC');
				},
				'choice_label' => 'choiceLabel',
				'multiple' => false,
				'by_reference' => true,
				'required' => true
			));

	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{

		return 'ModulepreinscriptionUpdateModuleformationForm';

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
				'moduleformation'
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