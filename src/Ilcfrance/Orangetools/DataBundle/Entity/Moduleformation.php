<?php

namespace Ilcfrance\Orangetools\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Orangetools\DataBundle\Model\JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Moduleformation
 * @ORM\Table(name="ilc_orange_moduleformations", indexes={@ORM\Index(name="fk_ilc_orange_moduleformations_gm", columns={"gm_id"})})
 * @ORM\Entity(repositoryClass="Ilcfrance\Orangetools\DataBundle\EntityRepository\ModuleformationRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"code"}, errorPath="code", groups={"code"})
 *
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class Moduleformation implements JsonSerializable
{

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
	 * @var string @ORM\Column(name="description", type="text", nullable=true)
	 */
	protected $description;

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
	 * @var Groupmodule @ORM\ManyToOne(targetEntity="Groupmodule", inversedBy="moduleformations", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="gm_id", referencedColumnName="id")
	 *      })
	 *
	 */
	protected $groupmodule;

	/**
	 *
	 * @var Collection @ORM\OneToMany(targetEntity="Sessionformation", mappedBy="moduleformation", cascade={"persist", "remove"})
	 *      @ORM\OrderBy({"dtStart" = "ASC"})
	 *
	 */
	protected $sessionformations;

	/**
	 *
	 * @var Collection @ORM\OneToMany(targetEntity="Modulepreinscription", mappedBy="moduleformation", cascade={"persist", "remove"})
	 *      @ORM\OrderBy({"moduleformation" = "ASC"})
	 *
	 */
	protected $modulepreinscriptions;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->dtCrea = new \DateTime('now');

		$this->sessionformations = new ArrayCollection();
		$this->modulepreinscriptions = new ArrayCollection();

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
	 * @return Moduleformation $this
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
	 * @return Moduleformation $this
	 */
	public function setTitle($title)
	{

		$this->title = $title;

		return $this;

	}

	/**
	 * Get $title
	 *
	 * @return string
	 */
	public function getDescription()
	{

		return $this->description;

	}

	/**
	 * Set $description
	 *
	 * @param string $description
	 *
	 * @return Moduleformation $this
	 */
	public function setDescription($description)
	{

		$this->description = $description;

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
	 * @return Moduleformation $this
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
	 * @return Moduleformation $this
	 */
	public function setDtUpdate(\DateTime $dtUpdate = null)
	{

		$this->dtUpdate = $dtUpdate;

		return $this;

	}

	/**
	 * Get $groupmodule
	 *
	 * @return Groupmodule
	 */
	public function getGroupmodule()
	{

		return $this->groupmodule;

	}

	/**
	 * Set $groupmodule
	 *
	 * @param Groupmodule $groupmodule
	 *
	 * @return Moduleformation $this
	 */
	public function setGroupmodule(Groupmodule $groupmodule)
	{
		$this->groupmodule = $groupmodule;

		return $this;

	}

	/**
	 * Add $sessionformation
	 *
	 * @param Sessionformation $sessionformation
	 *
	 * @return Moduleformation $this
	 */
	public function addSessionformation(Sessionformation $sessionformation)
	{

		$this->sessionformations[] = $sessionformation;

		return $this;

	}

	/**
	 * Remove $sessionformation
	 *
	 * @param Sessionformation $sessionformation
	 *
	 * @return Moduleformation $this
	 */
	public function removeSessionformation(Sessionformation $sessionformation)
	{

		$this->sessionformations->removeElement($sessionformation);

		return $this;

	}

	/**
	 * Get $sessionformations
	 *
	 * @return ArrayCollection
	 */
	public function getSessionformations()
	{

		return $this->sessionformations;

	}

	/**
	 * Set $sessionformations
	 *
	 * @param Collection $sessionformations
	 *
	 * @return Moduleformation $this
	 */
	public function setSessionformations(Collection $sessionformations)
	{

		$this->sessionformations = $sessionformations;

		return $this;

	}

	/**
	 * Add $modulepreinscription
	 *
	 * @param Modulepreinscription $modulepreinscription
	 *
	 * @return Moduleformation $this
	 */
	public function addModulepreinscription(Modulepreinscription $modulepreinscription)
	{

		$this->modulepreinscriptions[] = $modulepreinscription;

		return $this;

	}

	/**
	 * Remove $modulepreinscription
	 *
	 * @param Modulepreinscription $modulepreinscription
	 *
	 * @return Moduleformation $this
	 */
	public function removeModulepreinscription(Modulepreinscription $modulepreinscription)
	{

		$this->modulepreinscriptions->removeElement($modulepreinscription);

		return $this;

	}

	/**
	 * Get $modulepreinscriptions
	 *
	 * @return ArrayCollection
	 */
	public function getModulepreinscriptions()
	{

		return $this->modulepreinscriptions;

	}

	/**
	 * Set $modulepreinscriptions
	 *
	 * @param Collection $modulepreinscriptions
	 *
	 * @return Moduleformation $this
	 */
	public function setModulepreinscriptions(Collection $modulepreinscriptions)
	{

		$this->modulepreinscriptions = $modulepreinscriptions;

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
			'sessionformations',
			'modulepreinscriptions'
		);

	}

}
