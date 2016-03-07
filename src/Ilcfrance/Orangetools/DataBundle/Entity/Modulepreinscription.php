<?php

namespace Ilcfrance\Orangetools\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Orangetools\DataBundle\Model\JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Modulepreinscription
 * @ORM\Table(name="ilc_orange_modulepreinscriptions", indexes={@ORM\Index(name="fk_ilc_orange_modulepreinscriptions_mf",
 * columns={"mf_id"}), @ORM\Index(name="fk_ilc_orange_modulepreinscriptions_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Ilcfrance\Orangetools\DataBundle\EntityRepository\ModulepreinscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"moduleformation", "user"}, errorPath="moduleformation", groups={"moduleformation"})
 *
 */
class Modulepreinscription
{

	/**
	 *
	 * @var integer
	 */
	const LOCKOUT_UNLOCKED = 1;

	/**
	 *
	 * @var integer
	 */
	const LOCKOUT_LOCKED = 2;

	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 *
	 * @var integer @ORM\Column(name="lockout", type="integer", nullable=false)
	 *      @Assert\Choice(callback="choiceLockoutCallback", groups={"lockout"})
	 */
	protected $lockout;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="created_at", type="datetime", nullable=true)
	 */
	protected $dtCrea;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="updated_at", type="datetime", nullable=true)
	 *      @Gedmo\Timestampable(on="update")
	 */
	protected $dtUpdate;

	/**
	 *
	 * @var Moduleformation @ORM\ManyToOne(targetEntity="Moduleformation", inversedBy="modulepreinscriptions", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="mf_id", referencedColumnName="id")
	 *      })
	 *
	 */
	protected $moduleformation;

	/**
	 *
	 * @var User @ORM\ManyToOne(targetEntity="User", inversedBy="modulepreinscriptions", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *      })
	 */
	protected $user;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->dtCrea = new \DateTime('now');
		$this->lockout = self::LOCKOUT_UNLOCKED;

	}

	/**
	 * Get $id
	 *
	 * @return integer
	 */
	public function getId()
	{

		return $this->id;

	}

	/**
	 * Get $lockout
	 *
	 * @return integer
	 */
	public function getLockout()
	{

		return $this->lockout;

	}

	/**
	 * Set $lockout
	 *
	 * @param integer $lockout
	 *
	 * @return Modulepreinscription $this
	 */
	public function setLockout($lockout)
	{

		$this->lockout = $lockout;

		return $this;

	}

	/**
	 * Get $dtCrea
	 *
	 * @return \DateTime
	 */
	public function getDtCrea()
	{

		return $this->dtCrea;

	}

	/**
	 * Set $dtCrea
	 *
	 * @param \DateTime $dtCrea
	 *
	 * @return Modulepreinscription $this
	 */
	public function setDtCrea(\DateTime $dtCrea = null)
	{

		$this->dtCrea = $dtCrea;

		return $this;

	}

	/**
	 * Get $dtUpdate
	 *
	 * @return \DateTime
	 */
	public function getDtUpdate()
	{

		return $this->dtUpdate;

	}

	/**
	 * Set $dtUpdate
	 *
	 * @param \DateTime $dtUpdate
	 *
	 * @return Modulepreinscription $this
	 */
	public function setDtUpdate(\DateTime $dtUpdate = null)
	{

		$this->dtUpdate = $dtUpdate;

		return $this;

	}

	/**
	 * Get $moduleformation
	 *
	 * @return Moduleformation
	 */
	public function getModuleformation()
	{

		return $this->moduleformation;

	}

	/**
	 * Set $moduleformation
	 *
	 * @param Moduleformation $moduleformation
	 *
	 * @return Modulepreinscription $this
	 */
	public function setModuleformation(Moduleformation $moduleformation)
	{

		$this->moduleformation = $moduleformation;

		return $this;

	}

	/**
	 * Get $user
	 *
	 * @return User
	 */
	public function getUser()
	{

		return $this->user;

	}

	/**
	 * Set $user
	 *
	 * @param User $user
	 *
	 * @return Modulepreinscription $this
	 */
	public function setUser(User $user)
	{

		$this->user = $user;

		return $this;

	}

	/**
	 * Choice Form lockout
	 *
	 * @return multitype:string
	 */
	public static function choiceLockout()
	{

		return array(
			'Sessionformation.lockout.choice.' . self::LOCKOUT_UNLOCKED => self::LOCKOUT_UNLOCKED,
			'Sessionformation.lockout.choice.' . self::LOCKOUT_LOCKED => self::LOCKOUT_LOCKED
		);

	}

	/**
	 * Choice Validator lockout
	 *
	 * @return multitype:string
	 */
	public static function choiceLockoutCallback()
	{

		return array(
			self::LOCKOUT_UNLOCKED,
			self::LOCKOUT_LOCKED
		);

	}

	/**
	 *
	 * @param array $ignoreList
	 *
	 * @return array
	 */
	function getJsonData($ignoreList = array())
	{

		$ignoreList = \array_merge($this->getAutoIgnoreList(), $ignoreList);

		$var = get_object_vars($this);
		foreach ($var as $name => &$value) {
			if (!\in_array($name, $ignoreList)) {
				if (\is_object($value)) {
					if ($value instanceof \ArrayAccess) {
						$array = array();
						foreach ($value as $subname => &$val) {
							if ($val instanceof JsonSerializable) {
								$array[$subname] = $val->getJsonData($val->getIgnoreList());
							} else {
								$array[$subname] = $val;
							}
						}
						$value = $array;
					} elseif ($value instanceof JsonSerializable) {
						$value = $value->getJsonData($value->getIgnoreList());
					}
				}
			} else {
				unset($var[$name]);
			}
		}
		return $var;

	}

	/**
	 *
	 * @return string
	 */
	public function getDataJson($ignoreList = array())
	{

		$str = \json_encode($this->getJsonData($ignoreList));
		return \str_ireplace(array(
			'\/',
			','
		), array(
			'/',
			',<br/>'
		), $str);

	}

	/**
	 *
	 * @return array
	 */
	public function getAutoIgnoreList()
	{

		return array(
			"__cloner__",
			"__isInitialized__",
			"__initializer__"
		);

	}

	/**
	 *
	 * @return array
	 */
	public function getIgnoreList()
	{

		return array(
			'moduleformation'
		);

	}


}
