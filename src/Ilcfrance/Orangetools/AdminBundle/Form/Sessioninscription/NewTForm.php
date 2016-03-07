<?php

namespace Ilcfrance\Orangetools\AdminBundle\Form\Sessioninscription;

use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\SessionformationRepository;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\UserRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
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
	 * @var Sessionformation
	 */
	private $sessionformation;

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
		$this->sessionformation = $options['sessionformation'];
		$this->user = $options['user'];

		if (null != $this->sessionformation) {
			$sessionformation_id = $this->sessionformation->getId();
			$builder->add(
				'sessionformation',
				EntityidType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.SessioninscriptionNewForm.sessionformation.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:Sessionformation',
					'query_builder' => function (SessionformationRepository $sfr) use ($sessionformation_id)
					{
						return $sfr->createQueryBuilder('sf')
							->where('sf.id = :id')
							->setParameter('id', $sessionformation_id);
					},
					'choice_label' => 'id',
					'multiple' => false,
					'by_reference' => true,
					'required' => true
				));
		} else {
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

		if (null != $this->user) {
			$user_id = $this->user->getId();
			$builder->add(
				'user',
				EntityidType::class,
				array(
					'label' => 'ilcfrance.orangetools.admin.SessioninscriptionNewForm.user.label',
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
					'label' => 'ilcfrance.orangetools.admin.SessioninscriptionNewForm.user.label',
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
			'convocation',
			ChoiceType::class,
			array(
				'label' => 'ilcfrance.orangetools.admin.SessioninscriptionNewForm.convocation.label',
				'choices_as_values' => true,
				'choices' => Sessioninscription::choiceConvocation(),
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

		return 'SessioninscriptionNewForm';

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
				'sessionformation',
				'user',
				'convocation'
			),
			'sessionformation' => null,
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