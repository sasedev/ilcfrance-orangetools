<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Security;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 *
 * @author sasedev
 */
class RoleManager implements RoleManagerInterface
{

	protected $class;

	protected $entity_manager;

	protected $entity_repository;

	public function __construct(ManagerRegistry $manager_registry, $class)
	{
		$this->class = $class;
		$this->entity_manager = $manager_registry->getManagerForClass($class);
		$this->entity_repository = $this->entity_manager->getRepository($class);
	}

	/**
	 *
	 * {@inheritDoc} @see RoleManagerInterface::getEntityManager()
	 */
	public function getEntityManager()
	{
		return $this->entity_manager;
	}

	/**
	 *
	 * {@inheritDoc} @see RoleManagerInterface::getEntityRepository()
	 */
	public function getEntityRepository()
	{
		return $this->entity_repository;
	}

	/**
	 *
	 * {@inheritDoc} @see RoleManagerInterface::getRoles()
	 */
	public function getRoles()
	{
		return $this->getEntityRepository()->getAll();
	}

	/**
	 *
	 * {@inheritDoc} @see RoleManagerInterface::getClass()
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 *
	 * {@inheritDoc} @see RoleManagerInterface::createRole()
	 */
	public function createRole()
	{
		$class = $this->getClass();
		return new $class();
	}

	/**
	 *
	 * {@inheritDoc} @see RoleManagerInterface::saveRole()
	 */
	public function saveRole(RoleInterface $role)
	{
		$this->getEntityManager()->persist($role);
		$this->getEntityManager()->flush();
		return $this;
	}
}
