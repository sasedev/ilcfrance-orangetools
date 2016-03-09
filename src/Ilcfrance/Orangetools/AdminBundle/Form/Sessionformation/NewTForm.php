<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Sessionformation;

use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\ModuleformationRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;

/**
 *
 * @author sasedev <seif.salah@mfail.com>
 */
class NewTForm extends AbstractType
{

	/**
	 *
	 * @var Moduleformation
	 */
	private $moduleformation;

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$this->moduleformation = $options['moduleformation'];

		if (null != $this->moduleformation) {
			$moduleformation_id = $this->moduleformation->getId();
			$builder->add(
				'moduleformation',
				EntityidType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.moduleformation.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:Moduleformation',
					'query_builder' => function (ModuleformationRepository $mfr) use ($moduleformation_id)
					{
						return $mfr->createQueryBuilder('mf')
							->where('mf.id = :id')
							->setParameter('id', $moduleformation_id);
					},
					'choice_label' => 'id',
					'multiple' => false,
					'by_reference' => true,
					'required' => true
				));
		} else {
			$builder->add(
				'moduleformation',
				EntityType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.moduleformation.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:Moduleformation',
					'query_builder' => function (ModuleformationRepository $mfr)
					{
						return $mfr->createQueryBuilder('mf')
							->orderBy('mf.code', 'ASC');
					},
					'choice_label' => 'choiceLabel',
					'multiple' => false,
					'by_reference' => true,
					'required' => true
				));
		}

		$builder->add('code', TextType::class, array(
			'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.code.label'
		));

		$builder->add('title', TextType::class, array(
			'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.title.label'
		));

		$builder->add(
			'dtStart',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.dtStart.label'
			));

		$builder->add(
			'location',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.location.label'
			));

		$builder->add(
			'phoneContactCenter',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.phoneContactCenter.label',
				'required' => false
			));

		$builder->add(
			'conditionsReport',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.conditionsReport.label',
				'required' => false
			));

		$builder->add(
			'dtInfo',
			TextType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.dtInfo.label',
				'required' => false
			));

		$builder->add(
			'otherInfos',
			TextareaType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.otherInfos.label',
				'required' => false
			));

		$builder->add(
			'maxParticipants',
			IntegerType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.maxParticipants.label'
			));

		$builder->add(
			'lockout',
			ChoiceType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessionformationNewForm.lockout.label',
				'choices_as_values' => true,
				'choices' => Sessionformation::choiceLockout(),
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

		return 'SessionformationNewForm';

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
				'moduleformation',
				'code',
				'title',
				'dtStart',
				'location',
				'phoneContactCenter',
				'conditionsReport',
				'dtInfo',
				'otherInfos',
				'maxParticipants',
				'lockout'
			),
			'moduleformation' => null
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