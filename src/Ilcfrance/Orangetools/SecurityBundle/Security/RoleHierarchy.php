<?php

namespace Ilcfrance\Orangetools\SecurityBundle\Security;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

/**
 *
 * @author sasedev
 */
class RoleHierarchy extends BaseRoleHierarchy
{

	protected $rm;

	public function __construct(RoleManagerInterface $rm)
	{
		$this->rm = $rm;
		$map = $this->buildRolesTree();
		parent::__construct($map);
	}

	protected function buildRolesTree()
	{
		$hierarchy = [];
		$roles = $this->rm->getRoles();
		foreach ($roles as $role) {
			if (count($role->getParents()) > 0) {
				$roleParents = array();

				foreach ($role->getParents() as $parent) {
					$roleParents[] = $parent->getRole();
				}

				$hierarchy[$role->getRole()] = $roleParents;
			}
		}

		return $hierarchy;
	}
}