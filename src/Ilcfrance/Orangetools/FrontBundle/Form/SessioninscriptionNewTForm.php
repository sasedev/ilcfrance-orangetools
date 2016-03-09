<?php

namespace Ilcfrance\Orangetools\FrontBundle\Form;

use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\EntityRepository\SessionformationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class SessioninscriptionNewTForm extends AbstractType
{

	/**
	 *
	 * @var Sessionformation
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
				'sessionformation',
				EntityType::class,
				array(
					'label' => 'ilcfrance.orangetools.front.SessioninscriptionNewForm.sessionformation.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:Sessionformation',
					'query_builder' => function (SessionformationRepository $sfr) use ($moduleformation_id)
					{
						return $sfr->createQueryBuilder('sf')
							->join('sf.moduleformation', 'mf')
							->where('mf.id = :id')
							->setParameter('id', $moduleformation_id)
							->orderBy('sf.dtStart', 'ASC');
					},
					'choice_label' => 'choiceLabelFull',
					'multiple' => false,
					'by_reference' => true,
					'expanded' => true,
					'required' => true
				));
			$builder->add(
				'moduleformation',
				HiddenType::class,
				array(
					'label' => 'ilcfrance.orangetools.front.SessioninscriptionNewForm.moduleformation.label',
					'data' => $moduleformation_id,
					'mapped' => false
				));
		} else {
			$builder->add(
				'sessionformation',
				EntityType::class,
				array(
					'label' => 'ilcfrance.orangetools.front.SessioninscriptionNewForm.sessionformation.label',
					'class' => 'IlcfranceOrangetoolsDataBundle:Sessionformation',
					'query_builder' => function (SessionformationRepository $sfr)
					{
						return $sfr->createQueryBuilder('sf')
							->orderBy('sf.dtStart', 'ASC');
					},
					'choice_label' => 'title',
					'multiple' => false,
					'by_reference' => true,
					'expanded' => true,
					'required' => true
				));
			$builder->add(
				'moduleformation',
				HiddenType::class,
				array(
					'label' => 'ilcfrance.orangetools.front.SessioninscriptionNewForm.moduleformation.label',
					'data' => null,
					'mapped' => false
				));
		}

	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	public function finishView(FormView $view, FormInterface $form, array $options)
	{

		$this->moduleformation = $options['moduleformation'];
		$this->user = $options['user'];

		if (null != $this->moduleformation) {

			$now = new \DateTime('now');

			foreach ($this->moduleformation->getSessionformations() as $sessionformation) {
				foreach ($view->children['sessionformation']->children as $children) {
					if ($sessionformation->getId() == $children->vars['value']) {
						$dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', $sessionformation->getDtStart());
						if (($dtStart < $now) ||
							(\count($sessionformation->getSessioninscriptions()) >= $sessionformation->getMaxParticipants()) ||
							($sessionformation->getLockout() != Sessionformation::LOCKOUT_UNLOCKED)) {
							$children->vars['attr']['disabled'] = 'disabled';
						}
						foreach ($this->user->getSessioninscriptions() as $sessioninscription) {
							if ($sessioninscription->getSessionformation()->getId() == $children->vars['value']) {
								$children->vars['checked'] = true;
							}
						}
					}
				}
			}
		}

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
				'sessionformation'
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