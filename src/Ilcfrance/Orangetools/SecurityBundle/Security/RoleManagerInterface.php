<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 *
 * @author sasedev
 */
interface RoleManagerInterface
{

	/**
	 *
	 * @return array
	 */
	public function getRoles();

	/**
	 *
	 * @return string
	 */
	public function getClass();

	/**
	 *
	 * @return ObjectManager
	 */
	public function getEntityManager();

	/**
	 *
	 * @return ObjectRepository
	 */
	public function getEntityRepository();

	/**
	 *
	 * @return RoleInterface
	 */
	public function createRole();

	/**
	 *
	 * @return self
	 */
	public function saveRole(RoleInterface $role);
}
