<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation;

use Ilcfrance\Orangetools\DataBundle\EntityRepository\GroupmoduleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class UpdateGroupmoduleTForm extends AbstractType
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
			'groupmodule',
			EntityType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.ModuleformationUpdateGroupmoduleForm.groupmodule.label',
				'class' => 'IlcfranceOrangetoolsDataBundle:Groupmodule',
				'query_builder' => function (GroupmoduleRepository $gmr)
				{
					return $gmr->createQueryBuilder('gm')
						->orderBy('gm.name', 'ASC');
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

		return 'ModuleformationUpdateGroupmoduleForm';

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
				'groupmodule'
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