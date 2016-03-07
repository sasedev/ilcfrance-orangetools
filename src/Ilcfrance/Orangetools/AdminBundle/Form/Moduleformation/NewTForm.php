<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Moduleformation;

use Ilcfrance\Orangetools\DataBundle\Entity\Groupmodule;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\GroupmoduleRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
	 *
	 * @var Groupmodule
	 */
	private $groupmodule;

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->groupmodule = $options['groupmodule'];

		if (null != $this->groupmodule) {
			$groupmodule_id = $this->groupmodule->getId();
			$builder->add(
				'groupmodule',
				EntityidType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.ModuleformationNewForm.groupmodule.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:Groupmodule',
					'query_builder' => function (GroupmoduleRepository $gmr) use ($groupmodule_id)
					{
						return $gmr->createQueryBuilder('gm')
							->where('gm.id = :id')
							->setParameter('id', $groupmodule_id);
					},
					'choice_label' => 'id',
					'multiple' => false,
					'by_reference' => true,
					'required' => true
				));
		} else {
			$builder->add(
				'groupmodule',
				EntityType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.ModuleformationNewForm.groupmodule.label',
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

		$builder->add('code', TextType::class, array(
			'label' => 'ilcfrance.orangetools.admin.ModuleformationNewForm.code.label'
		));

		$builder->add('title', TextType::class, array(
			'label' => 'ilcfrance.orangetools.admin.ModuleformationNewForm.title.label'
		));

		$builder->add(
			'description',
			TextareaType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.ModuleformationNewForm.description.label',
				'required' => false
			));

	}

	/**
	 *
	 * {@inheritDoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{

		return 'ModuleformationNewForm';

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
				'groupmodule',
				'code',
				'title',
				'description'
			),
			'groupmodule' => null
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