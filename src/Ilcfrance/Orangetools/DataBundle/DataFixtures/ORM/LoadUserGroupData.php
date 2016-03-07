<?php

namespace Ilcfrance\Orangetools\DataBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\Entity\Groupmodule;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class LoadUserGroupData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

	/**
	 *
	 * @var ContainerInterface
	 */
	private $container;

	public function setContainer(ContainerInterface $container = null)
	{

		$this->container = $container;

	}

	public function load(ObjectManager $manager)
	{

		$roleUser = new Role();
		$roleUser->setName('ROLE_USER');
		$roleUser->setDescription('<p>Simple User</p>');
		$manager->persist($roleUser);

		$manager->flush();

		$roleAdmin = new Role();
		$roleAdmin->setName('ROLE_ADMIN');
		$roleAdmin->setDescription('<p>Generic Administrator</p>');
		$manager->persist($roleAdmin);
		$manager->flush();

		$roleAdmin->addParent($roleUser);
		$manager->persist($roleAdmin);
		$manager->flush();

		$roleTrainee = new Role();
		$roleTrainee->setName('ROLE_TRAINEE');
		$roleTrainee->setDescription('<p>Trainee</p>');
		$manager->persist($roleTrainee);
		$manager->flush();

		$roleTrainee->addParent($roleUser);
		$manager->persist($roleTrainee);
		$manager->flush();

		$sasedev = new User();
		$sasedev->setUsername('sasedev');
		$sasedev->setClearPassword('demodemo');
		$sasedev->setLockout(User::LOCKOUT_UNLOCKED);
		$sasedev->setEmail('seif.salah@gmail.com');
		$sasedev->setFirstName('Abdelkadeur Seifeddine');
		$sasedev->setLastName('Salah');
		$sasedev->addUserRole($roleAdmin);

		$manager->persist($sasedev);
		$manager->flush();

		$lea = new User();
		$lea->setUsername('lea');
		$lea->setClearPassword('demodemo');
		$lea->setLockout(User::LOCKOUT_UNLOCKED);
		$lea->setEmail('lea.paolini@gmail.com');
		$lea->setFirstName('Lea');
		$lea->setLastName('Paolini');
		$lea->addUserRole($roleAdmin);

		$manager->persist($lea);
		$manager->flush();

		/*
		$trainee1 = new User();
		$trainee1->setUsername('test');
		$trainee1->setClearPassword('demodemo');
		$trainee1->setLockout(User::LOCKOUT_UNLOCKED);
		$trainee1->setEmail('test@sasedev.net');
		$trainee1->setFirstName('Abdelkadeur Seifeddine');
		$trainee1->setLastName('Salah');
		$trainee1->addUserRole($roleTrainee);


		$manager->persist($trainee1);

		$gm1 = new Groupmodule();
		$gm1->setName('Parcours de Formation d’anglais Niveau B1-­ (2-­2.4)');

		$manager->persist($gm1);

		$gm2 = new Groupmodule();
		$gm2->setName('Parcours de Formation d’anglais Niveau B1+ (2.5-­2.9)');

		$manager->persist($gm2);

		$gm3 = new Groupmodule();
		$gm3->setName('Parcours de Formation d’anglais Niveau B2 C1 (3-­4)');

		$manager->persist($gm3);

		$mf1 = new Moduleformation();
		$mf1->setCode('TE');
		$mf1->setTitle("Techniques d'écoute - accents variés");
		$mf1->setDescription("Techniques d'écoute - accents variés");
		$mf1->setGroupmodule($gm1);

		$manager->persist($mf1);

		$mf2 = new Moduleformation();
		$mf2->setCode('VP');
		$mf2->setTitle("Valoriser une présentation");
		$mf2->setDescription("Valoriser une présentation");
		$mf2->setGroupmodule($gm1);

		$manager->persist($mf2);

		$mf3 = new Moduleformation();
		$mf3->setCode('PTC');
		$mf3->setTitle("Participer à la téléconférence");
		$mf3->setDescription("Participer à la téléconférence");
		$mf3->setGroupmodule($gm1);

		$manager->persist($mf3);

		$mf4 = new Moduleformation();
		$mf4->setCode('GP');
		$mf4->setTitle("Gérer un projet', 'Gérer un projet");
		$mf4->setDescription("Gérer un projet', 'Gérer un projet");
		$mf4->setGroupmodule($gm1);

		$manager->persist($mf4);

		$mf5 = new Moduleformation();
		$mf5->setCode('CSP');
		$mf5->setTitle("Communication socio-professionnelle");
		$mf5->setDescription("Communication socio-professionnelle");
		$mf5->setGroupmodule($gm1);

		$manager->persist($mf5);

		$mf6 = new Moduleformation();
		$mf6->setCode('RP');
		$mf6->setTitle("Rédaction professionnelle");
		$mf6->setDescription("Rédaction professionnelle");
		$mf6->setGroupmodule($gm1);

		$manager->persist($mf6);

		$mf7 = new Moduleformation();
		$mf7->setCode('LRS');
		$mf7->setTitle("Lecture et rédaction scientifique");
		$mf7->setDescription("Lecture et rédaction scientifique");
		$mf7->setGroupmodule($gm1);

		$manager->persist($mf7);

		$mf8 = new Moduleformation();
		$mf8->setCode('SE');
		$mf8->setTitle("Stratégies d'écoute - accents variés");
		$mf8->setDescription("Stratégies d'écoute - accents variés");
		$mf8->setGroupmodule($gm1);

		$manager->persist($mf8);

		$mf9 = new Moduleformation();
		$mf9->setCode('SEI');
		$mf9->setTitle("Stratégies d’Ecoute - ­Inde");
		$mf9->setDescription("Stratégies d’Ecoute - ­Inde");
		$mf9->setGroupmodule($gm2);

		$manager->persist($mf9);

		$mf10 = new Moduleformation();
		$mf10->setCode('SPC');
		$mf10->setTitle("Persuader et convaincre");
		$mf10->setDescription("Persuader et convaincre");
		$mf10->setGroupmodule($gm2);

		$manager->persist($mf10);

		$mf11 = new Moduleformation();
		$mf11->setCode('GTC');
		$mf11->setTitle("Gérer la téléconférence");
		$mf11->setDescription("Gérer la téléconférence");
		$mf11->setGroupmodule($gm2);

		$manager->persist($mf11);

		$mf12 = new Moduleformation();
		$mf12->setCode('MP');
		$mf12->setTitle("Mener un projet");
		$mf12->setDescription("Mener un projet");
		$mf12->setGroupmodule($gm2);

		$manager->persist($mf12);

		$mf13 = new Moduleformation();
		$mf13->setCode('NW');
		$mf13->setTitle("Networking");
		$mf13->setDescription("Networking");
		$mf13->setGroupmodule($gm2);

		$manager->persist($mf13);

		$mf14 = new Moduleformation();
		$mf14->setCode('RGD');
		$mf14->setTitle("Rédaction et Gestion de Documents");
		$mf14->setDescription("Rédaction et Gestion de Documents");
		$mf14->setGroupmodule($gm2);

		$manager->persist($mf14);

		$mf15 = new Moduleformation();
		$mf15->setCode('EIP');
		$mf15->setTitle("Effective International Communication");
		$mf15->setDescription("Effective International Communication");
		$mf15->setGroupmodule($gm3);

		$manager->persist($mf15);

		$mf16 = new Moduleformation();
		$mf16->setCode('IPS');
		$mf16->setTitle("International Public Speaking");
		$mf16->setDescription("International Public Speaking");
		$mf16->setGroupmodule($gm3);

		$manager->persist($mf16);

		$mf17 = new Moduleformation();
		$mf17->setCode('IPN');
		$mf17->setTitle("International Project Management");
		$mf17->setDescription("International Project Management");
		$mf17->setGroupmodule($gm3);

		$manager->persist($mf17);

		$mf18 = new Moduleformation();
		$mf18->setCode('ITW');
		$mf18->setTitle("International Teamwork");
		$mf18->setDescription("International Teamwork");
		$mf18->setGroupmodule($gm3);

		$manager->persist($mf18);

		$sf1 = new Sessionformation();
		$sf1->setCode('TE-001');
		$sf1->setTitle('Session du 15 mars 2016');
		$sf1->setDtStart(new \DateTime('2016-03-15 09:00:00'));
		$sf1->setLocation("Centre de Paris");
		$sf1->setPhoneContactCenter("+33.1123456789");
		$sf1->setConditionsReport("Contacter le support pour plus d'informations");
		$sf1->setDtInfo("durée 7h");
		$sf1->setOtherInfos("une seule journée");
		$sf1->setMaxParticipants(6);
		$sf1->setModuleformation($mf1);

		$manager->persist($sf1);

		$sf2 = new Sessionformation();
		$sf2->setCode('VP-001');
		$sf2->setTitle('Session du 16 mars 2016');
		$sf2->setDtStart(new \DateTime('2016-03-16 09:00:00'));
		$sf2->setLocation("Centre de Paris");
		$sf2->setPhoneContactCenter("+33.1123456789");
		$sf2->setConditionsReport("Contacter le support pour plus d'informations");
		$sf2->setDtInfo("durée 7h");
		$sf2->setOtherInfos("une seule journée");
		$sf2->setMaxParticipants(6);
		$sf2->setModuleformation($mf2);

		$manager->persist($sf2);

		$mpi1 = new Modulepreinscription();
		$mpi1->setModuleformation($mf1);
		$mpi1->setUser($trainee1);

		$manager->persist($mpi1);

		$si1 = new Sessioninscription();
		$si1->setConvocation(Sessioninscription::CONVOCATION_NOTSENT);
		$si1->setSessionformation($sf1);
		$si1->setUser($trainee1);

		$manager->persist($si1);

		$manager->flush(); //*/

	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Doctrine\Common\DataFixtures\OrderedFixtureInterface::getOrder()
	 */
	public function getOrder()
	{

		return 1;

	}

}
