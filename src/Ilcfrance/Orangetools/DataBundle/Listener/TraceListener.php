<?php

namespace Ilcfrance\Orangetools\DataBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Ilcfrance\Orangetools\DataBundle\MongoDocument\Trace;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ilcfrance\Orangetools\DataBundle\Entity\Role;
use Ilcfrance\Orangetools\DataBundle\Entity\User;
use Ilcfrance\Orangetools\DataBundle\Entity\Groupmodule;
use Ilcfrance\Orangetools\DataBundle\Entity\Moduleformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Modulepreinscription;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessionformation;
use Ilcfrance\Orangetools\DataBundle\Entity\Sessioninscription;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ilcfrance\Orangetools\DataBundle\Model\JsonSerializable;
/**
 * TraceListener
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TraceListener implements EventSubscriber
{

	/**
	 *
	 * @var Container
	 */
	private $container;

	/**
	 *
	 * @var User
	 */
	private $user;

	/**
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{

		$this->container = $container;

	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents()
	{
		return array(
			Events::postPersist,
			Events::preUpdate,
			Events::preRemove
		);
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getObject();

		$trace = $this->initTrace();
		$trace->setActionType(Trace::AT_CREATE);
		$trace->setActionId($entity->getId());

		if ($entity instanceof Role) {
			$trace->setActionEntity(Trace::AE_Role);
			$trace->setMsg(\json_encode($entity->getJsonData(array()), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof User) {
			$trace->setActionEntity(Trace::AE_User);
			$trace->setMsg(\json_encode($entity->getJsonData(array('clearPassword', 'password', 'salt', 'recoveryCode', 'recoveryExpiration', 'lockout', 'logins', 'lastLogin', 'lastActivity')), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Groupmodule) {
			$trace->setActionEntity(Trace::AE_Groupmodule);
			$trace->setActionId($entity->getId());
			$trace->setMsg(\json_encode($entity->getJsonData(array()), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Moduleformation) {
			$trace->setActionEntity(Trace::AE_Moduleformation);
			$trace->setMsg(\json_encode($entity->getJsonData(array()), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Modulepreinscription) {
			$trace->setActionEntity(Trace::AE_Modulepreinscription);
			$trace->setMsg(\json_encode($entity->getJsonData(array()), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Sessionformation) {
			$trace->setActionEntity(Trace::AE_Sessionformation);
			$trace->setMsg(\json_encode($entity->getJsonData(array()), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Sessioninscription) {
			$trace->setActionEntity(Trace::AE_Sessioninscription);
			$trace->setMsg(\json_encode($entity->getJsonData(array()), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		}
	}

	/**
	 *
	 * @param PreUpdateEventArgs $args
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity = $args->getObject();

		$trace = $this->initTrace();
		$trace->setActionType(Trace::AT_UPDATE);
		$trace->setActionId($entity->getId());
		$changes = $args->getEntityChangeSet();

		if ($entity instanceof Role) {
			$keys = array('dtCrea', 'dtUpdate');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_Role);
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		} elseif ($entity instanceof User) {

			$keys = array('id', 'dtCrea', 'dtUpdate', 'lastActivity', 'password', 'clearPassword', 'salt', 'recoveryCode', 'recoveryExpiration', 'lockout');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_User);
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		} elseif ($entity instanceof Groupmodule) {

			$keys = array('dtCrea', 'dtUpdate');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_Groupmodule);
				$trace->setActionId($entity->getId());
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		} elseif ($entity instanceof Moduleformation) {

			$keys = array('dtCrea', 'dtUpdate');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_Moduleformation);
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		} elseif ($entity instanceof Modulepreinscription) {

			$keys = array('dtCrea', 'dtUpdate');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_Modulepreinscription);
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		} elseif ($entity instanceof Sessionformation) {

			$keys = array('dtCrea', 'dtUpdate');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_Sessionformation);
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		} elseif ($entity instanceof Sessioninscription) {

			$keys = array('dtCrea', 'dtUpdate');
			foreach ($keys as $key) {
				if (\array_key_exists($key, $changes)) {
					unset($changes[$key]);
				}
			}
			if (\count($changes) != 0) {
				foreach ($changes as &$change) {
					if (\is_object($change[0])) {
						if ($change[0] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[0] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[0] = $array;
						} elseif ($change instanceof JsonSerializable) {
							$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
						} elseif ( \method_exists($change[0], 'getJsonData')) {
							if (\method_exists($change[0], 'getIgnoreList')) {
								$change[0] = $change[0]->getJsonData($change[0]->getIgnoreList());
							} else {
								$change[0] = $change[0]->getJsonData();
							}
						}
					}
					if (\is_object($change[1])) {
						if ($change[1] instanceof \ArrayAccess) {
							$array = array();
							foreach ($change[1] as $subname => &$val) {
								if ($val instanceof JsonSerializable) {
									$array[$subname] = $val->getJsonData($val->getIgnoreList());
								} else {
									$array[$subname] = $val;
								}
							}
							$change[1] = $array;
						} elseif ($change[1] instanceof JsonSerializable) {
							$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
						} elseif ( \method_exists($change[1], 'getJsonData')) {
							if (\method_exists($change[1], 'getIgnoreList')) {
								$change[1] = $change[1]->getJsonData($change[1]->getIgnoreList());
							} else {
								$change[1] = $change[1]->getJsonData();
							}
						}
					}
				}
				$trace->setActionEntity(Trace::AE_Sessioninscription);
				$trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				$this->persist($trace);
			}
		}
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args)
	{
		$entity = $args->getObject();

		$trace = $this->initTrace();
		$trace->setActionType(Trace::AT_DELETE);

		if ($entity instanceof Role) {
			$trace->setActionEntity(Trace::AE_Role);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof User) {
			$trace->setActionEntity(Trace::AE_User);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Groupmodule) {
			$trace->setActionEntity(Trace::AE_Groupmodule);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Moduleformation) {
			$trace->setActionEntity(Trace::AE_Moduleformation);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Modulepreinscription) {
			$trace->setActionEntity(Trace::AE_Modulepreinscription);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Sessionformation) {
			$trace->setActionEntity(Trace::AE_Sessionformation);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		} elseif ($entity instanceof Sessioninscription) {
			$trace->setActionEntity(Trace::AE_Sessioninscription);
			$trace->setMsg(\json_encode($entity->getJsonData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			$this->persist($trace);
		}
	}

	/**
	 *
	 * @return Trace
	 */
	private function initTrace()
	{
		$trace = new Trace();

		$tokenStorage = $this->container->get('security.token_storage');
		if (null != $tokenStorage && null != $tokenStorage->getToken()) {
			$this->user = $tokenStorage->getToken()->getUser();
		}
		if ($this->user != null && $this->user instanceof User) {
			$trace->setUserId($this->user->getId());
			$trace->setUserFullname($this->user->getFullName());
		} else {
			$trace->setUserFullname("--<=SYSTEM=>--");
		}
		return $trace;
	}

	/**
	 *
	 * @param mixed $entity
	 * @param EntityManager $em
	 */
	private function persist($entity)
	{
		//$dm = $this->container->get('doctrine.odm.mongodb.document_manager');
		$dm = $this->container->get('doctrine_mongodb')->getManager();
		$dm->persist($entity);
		$dm->flush();
	}

}
