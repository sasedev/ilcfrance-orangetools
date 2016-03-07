<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription;

use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\SessionformationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class UpdateSessionformationTForm extends AbstractType
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
			'sessionformation',
			EntityType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessioninscriptionNewForm.sessionformation.label',
				'class' => 'IlcfranceOrangetoolsDataBundle:Sessionformation',
				'query_builder' => function (SessionformationRepository $sfr)
				{
					return $sfr->createQueryBuilder('sf')
						->leftJoin('sf.sessioninscriptions', 'si')
						->where('sf.lockout = :sfLockout')
						->andWhere('sf.dtStart > :now')
						->groupBy('si.id')
						->having('COUNT(si) < sf.maxParticipants')
						->setParameter('sfLockout', Sessionformation::LOCKOUT_UNLOCKED)
						->setParameter('now', new \DateTime('now'))
						->orderBy('sf.code', 'ASC');
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

		return 'SessioninscriptionUpdateSessionformationForm';

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
				'sessionformation'
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