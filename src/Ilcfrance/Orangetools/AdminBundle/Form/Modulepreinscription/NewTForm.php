<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Modulepreinscription;

use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\ModuleformationRepository;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\UserRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class NewTForm extends AbstractType
{

	/**
	 *
	 * @var Moduleformation
	 */
	private $moduleformation;

	/**
	 *
	 * @var User
	 */
	private $user;

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->moduleformation = $options['moduleformation'];
		$this->user = $options['user'];

		if (null != $this->moduleformation) {
			$moduleformation_id = $this->moduleformation->getId();
			$builder->add(
				'moduleformation',
				EntityidType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionNewForm.moduleformation.label',
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
					'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionNewForm.moduleformation.label',
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

		if (null != $this->user) {
			$user_id = $this->user->getId();
			$builder->add(
				'user',
				EntityidType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionNewForm.user.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:User',
					'query_builder' => function (UserRepository $ur) use ($user_id)
					{
						return $ur->createQueryBuilder('u')
							->where('u.id = :id')
							->setParameter('id', $user_id);
					},
					'choice_label' => 'id',
					'multiple' => false,
					'by_reference' => true,
					'required' => true
				));
		} else {
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

		$builder->add(
			'lockout',
			ChoiceType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.ModulepreinscriptionNewForm.lockout.label',
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

		return 'ModulepreinscriptionNewForm';

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
				'user',
				'lockout'
			),
			'moduleformation' => null,
			'user' => null
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