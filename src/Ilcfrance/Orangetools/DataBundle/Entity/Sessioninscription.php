<?php

namespace Ilcfrance\Orangetools\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Orangetools\DataBundle\Model\JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sessioninscription
 * @ORM\Table(name="ilc_orange_sessioninscriptions", indexes={@ORM\Index(name="fk_ilc_orange_sessioninscriptions_sf", columns={"sf_id"}),
 * @ORM\Index(name="fk_ilc_orange_sessioninscriptions_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Ilcfrance\Orangetools\DataBundle\EntityRepository\SessioninscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"code"}, errorPath="code", groups={"code"})
 *
 */
class Sessioninscription implements JsonSerializable
{

	/**
	 *
	 * @var integer
	 */
	const CONVOCATION_NOTSENT = 1;

	/**
	 *
	 * @var integer
	 */
	const CONVOCATION_SENT = 2;

	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 *
	 * @var integer @ORM\Column(name="convocation", type="integer", nullable=false)
	 *      @Assert\Choice(callback="choiceConvocationCallback", groups={"convocation"})
	 */
	protected $convocation;

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
	 * @var Sessionformation @ORM\ManyToOne(targetEntity="Sessionformation", inversedBy="sessioninscriptions", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="sf_id", referencedColumnName="id")
	 *      })
	 *
	 */
	protected $sessionformation;

	/**
	 *
	 * @var User @ORM\ManyToOne(targetEntity="User", inversedBy="sessioninscriptions", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *      })
	 *
	 */
	protected $user;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->dtCrea = new \DateTime('now');
		$this->convocation = self::CONVOCATION_NOTSENT;

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
	 * Get $convocation
	 *
	 * @return integer
	 */
	public function getConvocation()
	{

		return $this->convocation;

	}

	/**
	 * Set $convocation
	 *
	 * @param integer $convocation
	 *
	 * @return Sessioninscription $this
	 */
	public function setConvocation($convocation)
	{

		$this->convocation = $convocation;

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
	 * @return Sessioninscription $this
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
	 * @return Sessioninscription $this
	 */
	public function setDtUpdate(\DateTime $dtUpdate = null)
	{

		$this->dtUpdate = $dtUpdate;

		return $this;

	}

	/**
	 * Get $sessionformation
	 *
	 * @return Sessionformation
	 */
	public function getSessionformation()
	{

		return $this->sessionformation;

	}

	/**
	 * Set $sessionformation
	 *
	 * @param Sessionformation $sessionformation
	 *
	 * @return Sessioninscription $this
	 */
	public function setSessionformation(Sessionformation $sessionformation)
	{

		$this->sessionformation = $sessionformation;

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
	 * @return Sessioninscription $this
	 */
	public function setUser(User $user)
	{

		$this->user = $user;

		return $this;

	}

	/**
	 * Choice Form sexe
	 *
	 * @return multitype:string
	 */
	public static function choiceConvocation()
	{

		return array(
			'Sessioninscription.convocation.choice.' . self::CONVOCATION_NOTSENT => self::CONVOCATION_NOTSENT,
			'Sessioninscription.convocation.choice.' . self::CONVOCATION_SENT => self::CONVOCATION_SENT
		);

	}

	/**
	 * Choice Validator sexe
	 *
	 * @return multitype:integer
	 */
	public static function choiceConvocationCallback()
	{

		return array(
			self::CONVOCATION_NOTSENT,
			self::CONVOCATION_SENT
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
			'sessionformation'
		);

	}


}
