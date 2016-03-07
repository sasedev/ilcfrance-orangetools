<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription;

use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class UpdateTraineeTForm extends AbstractType
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
			'user',
			EntityType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionNewForm.user.label',
				'class' => 'IlcfranceOrangetoolsDataBundle:User',
				'query_builder' => function (UserRepository $ur)
				{
					return $ur->createQueryBuilder('u')
						->join('u.userRoles', 'r')
						->where('u.lockout = :uLockout')
						->andWhere('r.name = :role')
						->setParameter('uLockout', User::LOCKOUT_UNLOCKED)
						->setParameter('role', 'ROLE_TRAINEE')
						->orderBy('u.username', 'ASC');
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

		return 'ModulepreinscriptionUpdateTraineeForm';

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
				'user'
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