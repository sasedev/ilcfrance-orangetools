<?php

namespace Ilcfrance\Orangetools\DataBundle\MongoRepository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TraceRepository extends DocumentRepository
{

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllQuery(\DateTime $minDtCrea = null)
	{

		if (null == $minDtCrea) {
			return $this->createQueryBuilder('t')
				->sort('dtCrea', 'ASC')
				->sort('actionEntity', 'ASC')
				->sort('actionType', 'ASC')
				->getQuery();
		} else {
			return $this->createQueryBuilder('t')
				->field('dtCrea')->gte($minDtCrea)
				->sort('dtCrea', 'ASC')
				->sort('actionEntity', 'ASC')
				->sort('actionType', 'ASC')
				->getQuery();
		}

	}

	/**
	 * Get All Entities
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed,
	 *         \Doctrine\ORM\Internal\Hydration\mixed,
	 *         \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getAll(\DateTime $minDtCrea = null)
	{

		return $this->getAllQuery($minDtCrea)->execute();

	}

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllRelatedToUserQuery(User $user, \DateTime $minDtCrea = null)
	{

		if (null == $minDtCrea) {
			$qb = $this->createQueryBuilder('t');
			$qb->addOr($qb->expr()
				->field('userId')
				->equals((string) $user->getId()));
			$qb->addOr(
				$qb->expr()
				->addAnd(
					$qb->expr()
					->field('actionId')
					->equals((string) $user->getId())
				)
				->addAnd(
					$qb->expr()
					->field('actionEntity')
					->equals(Trace::AE_User)
				)
			);
			$qb->sort('dtCrea', 'ASC');
			$qb->sort('actionEntity', 'ASC');
			$qb->sort('actionType', 'ASC');

			return $qb->getQuery();
		} else {
			$qb = $this->createQueryBuilder('t');
			$qb->addAnd($qb->expr()
				->field('dtCrea')
				->gte($minDtCrea)
			);
			$qb->addAnd(
				$qb->addOr($qb->expr()->field('userId')->equals((string) $user->getId()))
				->addOr(
					$qb->expr()->addAnd($qb->expr()->field('userId')->equals((string) $user->getId()))
					->addAnd($qb->expr()->field('actionEntity')->equals(Trace::AE_User))
				)
			);
			$qb->sort('dtCrea', 'ASC');
			$qb->sort('actionEntity', 'ASC');
			$qb->sort('actionType', 'ASC');

			return $qb->getQuery();
		}

	}

	/**
	 * Get All Entities
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed,
	 *         \Doctrine\ORM\Internal\Hydration\mixed,
	 *         \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getAllRelatedToUser(User $user, \DateTime $minDtCrea = null)
	{

		return $this->getAllRelatedToUserQuery($user, $minDtCrea)->execute();

	}

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllByEntityIdQuery($entity_id, $entity_type)
	{
		$qb = $this->createQueryBuilder('t');
		$qb->addAnd($qb->expr()
			->field('actionEntity')
			->equals((string) $entity_type));
		$qb->addAnd(
			$qb->expr()
			->field('actionId')
			->equals((string) $entity_id)
			);
		$qb->sort('dtCrea', 'ASC');

		return $qb->getQuery();

	}

	/**
	 * Get All Entities
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed,
	 *		 \Doctrine\ORM\Internal\Hydration\mixed,
	 *		 \Doctrine\DBAL\Driver\Statement,
	 *		 \Doctrine\Common\Cache\mixed>
	 */
	public function getAllByEntityId($entity_id, $entity_type)
	{

		return $this->getAllByEntityIdQuery($entity_id, $entity_type)->execute();

	}

}