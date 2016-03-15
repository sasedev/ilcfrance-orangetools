<?php

namespace Ilcfrance\Orangetools\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Orangetools\DataBundle\Model\JsonSerializable;
use Sasedev\Commons\SharedBundle\Validator as ExtraAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sessionformation
 * @ORM\Table(name="ilc_orange_sessionformations", indexes={@ORM\Index(name="fk_ilc_orange_sessionformations_mf", columns={"mf_id"})})
 * @ORM\Entity(repositoryClass="Ilcfrance\Orangetools\DataBundle\EntityRepository\SessionformationRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"code"}, errorPath="code", groups={"code"})
 *
 */
class Sessionformation implements JsonSerializable
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
	 * @var string @ORM\Column(name="code", type="text", nullable=false)
	 *      @Assert\Length(min = "2", max = "100", groups={"code"})
	 */
	protected $code;

	/**
	 *
	 * @var string @ORM\Column(name="title", type="text", nullable=false)
	 */
	protected $title;

	/**
	 *
	 * @var string @ORM\Column(name="dtstart", type="text", nullable=false)
	 */
	protected $dtStart;

	/**
	 *
	 * @var string @ORM\Column(name="dtend", type="text", nullable=false)
	 */
	protected $dtEnd;

	/**
	 *
	 * @var string @ORM\Column(name="location", type="text", nullable=false)
	 */
	protected $location;

	/**
	 *
	 * @var string @ORM\Column(name="phonecontactcenter", type="text", nullable=true)
	 *      @ExtraAssert\Phone(groups={"phoneContactCenter"})
	 */
	protected $phoneContactCenter;

	/**
	 *
	 * @var string @ORM\Column(name="conditionsreport", type="text", nullable=true)
	 */
	protected $conditionsReport;

	/**
	 *
	 * @var string @ORM\Column(name="dateinfo", type="text", nullable=true)
	 */
	protected $dtInfo;

	/**
	 *
	 * @var string @ORM\Column(name="otherinfo", type="text", nullable=true)
	 */
	protected $otherInfos;

	/**
	 *
	 * @var integer @ORM\Column(name="maxparticipants", type="integer", nullable=false)
	 * @Assert\GreaterThan(value="0", groups={"maxParticipants"})
	 */
	protected $maxParticipants;

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
	 * @var Moduleformation @ORM\ManyToOne(targetEntity="Moduleformation", inversedBy="sessionformations", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="mf_id", referencedColumnName="id")
	 *      })
	 *
	 */
	protected $moduleformation;

	/**
	 *
	 * @var Collection @ORM\OneToMany(targetEntity="Sessioninscription", mappedBy="sessionformation", cascade={"persist", "remove"})
	 *      @ORM\OrderBy({"sessionformation" = "ASC"})
	 *
	 */
	protected $sessioninscriptions;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->dtCrea = new \DateTime('now');
		$this->lockout = self::LOCKOUT_UNLOCKED;
		$this->maxParticipants = 0;

		$this->sessioninscriptions = new ArrayCollection();

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
	 * Get $code
	 *
	 * @return string
	 */
	public function getCode()
	{

		return $this->code;

	}

	/**
	 * Set $code
	 *
	 * @param string $code
	 *
	 * @return Sessionformation $this
	 */
	public function setCode($code)
	{

		$this->code = $code;

		return $this;

	}

	/**
	 * Get $title
	 *
	 * @return string
	 */
	public function getTitle()
	{

		return $this->title;

	}

	/**
	 * Set $title
	 *
	 * @param string $title
	 *
	 * @return Sessionformation $this
	 */
	public function setTitle($title)
	{

		$this->title = $title;

		return $this;

	}

	/**
	 * Get $dtStart
	 *
	 * @return string
	 */
	public function getDtStart()
	{
		return $this->dtStart;
	}

	/**
	 * Get $dtStart
	 *
	 * @return \DateTime
	 */
	public function getDtStartTz()
	{
		return \DateTime::createFromFormat('Y-m-d H:i:s', $this->dtStart, new \DateTimeZone('Europe/Paris')) ;
	}

	/**
	 * Set $dtStart
	 *
	 * @param string $dtStart
	 *
	 * @return Sessionformation $this
	 */
	public function setDtStart($dtStart)
	{

		$this->dtStart = $dtStart;

		return $this;

	}

	/**
	 * Get $dtEnd
	 *
	 * @return string
	 */
	public function getDtEnd()
	{
		return $this->dtEnd;
	}

	/**
	 * Get $dtEnd
	 *
	 * @return \DateTime
	 */
	public function getDtEndTz()
	{
		return \DateTime::createFromFormat('Y-m-d H:i:s', $this->dtEnd, new \DateTimeZone('Europe/Paris')) ;
	}

	/**
	 * Set $dtEnd
	 *
	 * @param string $dtEnd
	 *
	 * @return Sessionformation $this
	 */
	public function setDtEnd($dtEnd)
	{

		$this->dtEnd = $dtEnd;

		return $this;

	}

	/**
	 * Get $location
	 *
	 * @return string
	 */
	public function getLocation()
	{

		return $this->location;

	}

	/**
	 * Set $location
	 *
	 * @param string $location
	 *
	 * @return Sessionformation $this
	 */
	public function setLocation($location)
	{

		$this->location = $location;

		return $this;

	}

	/**
	 * Get $phoneContactCenter
	 *
	 * @return string
	 */
	public function getPhoneContactCenter()
	{

		return $this->phoneContactCenter;

	}

	/**
	 * Set $phoneContactCenter
	 *
	 * @param string $phoneContactCenter
	 *
	 * @return Sessionformation $this
	 */
	public function setPhoneContactCenter($phoneContactCenter)
	{

		$this->phoneContactCenter = $phoneContactCenter;

		return $this;

	}

	/**
	 * Get $conditionsReport
	 *
	 * @return string
	 */
	public function getConditionsReport()
	{

		return $this->conditionsReport;

	}

	/**
	 * Set $conditionsReport
	 *
	 * @param string $conditionsReport
	 *
	 * @return Sessionformation $this
	 */
	public function setConditionsReport($conditionsReport)
	{

		$this->conditionsReport = $conditionsReport;

		return $this;

	}

	/**
	 * Get $dtInfo
	 *
	 * @return string
	 */
	public function getDtInfo()
	{

		return $this->dtInfo;

	}

	/**
	 * set $dtInfo
	 *
	 * @param string $dtInfo
	 *
	 * @return Sessionformation $this
	 */
	public function setDtInfo($dtInfo)
	{

		$this->dtInfo = $dtInfo;

		return $this;

	}

	/**
	 * Get $otherInfos
	 *
	 * @return string
	 */
	public function getOtherInfos()
	{

		return $this->otherInfos;

	}

	/**
	 * Set $otherInfos
	 *
	 * @param string $otherInfos
	 *
	 * @return Sessionformation $this
	 */
	public function setOtherInfos($otherInfos)
	{

		$this->otherInfos = $otherInfos;

		return $this;

	}

	/**
	 * Get $maxParticipants
	 *
	 * @return integer
	 */
	public function getMaxParticipants()
	{

		return $this->maxParticipants;

	}

	/**
	 * Set et $maxParticipants
	 *
	 * @param integer $maxParticipants
	 *
	 * @return Sessionformation $this
	 */
	public function setMaxParticipants($maxParticipants)
	{

		$this->maxParticipants = $maxParticipants;

		return $this;

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
	 * @return Sessionformation $this
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
	 * @return Sessionformation $this
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
	 * @return Sessionformation $this
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
	 * @return Sessionformation $this
	 */
	public function setModuleformation(Moduleformation $moduleformation)
	{

		$this->moduleformation = $moduleformation;

		return $this;

	}

	/**
	 * Add $sessioninscription
	 *
	 * @param Sessioninscription $sessioninscription
	 *
	 * @return Sessionformation $this
	 */
	public function addSessioninscription(Sessioninscription $sessioninscription)
	{

		$this->sessioninscriptions[] = $sessioninscription;

		return $this;

	}

	/**
	 * Remove $sessioninscription
	 *
	 * @param Sessioninscription $sessioninscription
	 *
	 * @return Sessionformation $this
	 */
	public function removeSessioninscription(Sessioninscription $sessioninscription)
	{

		$this->sessioninscriptions->removeElement($sessioninscription);

		return $this;

	}

	/**
	 * Get $sessioninscriptions
	 *
	 * @return ArrayCollection
	 */
	public function getSessioninscriptions()
	{

		return $this->sessioninscriptions;

	}

	/**
	 * Set $sessioninscriptions
	 *
	 * @param Collection $sessioninscriptions
	 *
	 * @return Sessionformation $this
	 */
	public function setSessioninscriptions(Collection $sessioninscriptions)
	{

		$this->sessioninscriptions = $sessioninscriptions;

		return $this;

	}

	/**
	 * Get choiceLabel
	 *
	 * @return string
	 */
	public function getChoiceLabel()
	{

		return $this->getCode().' ('.$this->getTitle().')';

	}

	/**
	 * Get choiceLabelFull
	 *
	 * @return string
	 */
	public function getChoiceLabelFull()
	{

		return $this->getCode().' :'.$this->getTitle().' ( '.$this->getDtStartTz()->format('d/m/Y @ H:i').' -> '.$this->getDtEndTz()->format('d/m/Y @ H:i').' - '.$this->getLocation().')';

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
			'moduleformation',
			'sessioninscriptions'
		);

	}

}
