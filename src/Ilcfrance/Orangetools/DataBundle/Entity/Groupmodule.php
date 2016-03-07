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
 * Groupmodule
 * @ORM\Table(name="ilc_orange_groupmodules")
 * @ORM\Entity(repositoryClass="Ilcfrance\Orangetools\DataBundle\EntityRepository\GroupmoduleRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"name"}, errorPath="name", groups={"name"})
 *
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class Groupmodule implements JsonSerializable
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
	 * @var string @ORM\Column(name="name", type="text", nullable=false)
	 *      @Assert\Length(min = "2", max = "100", groups={"name"})
	 */
	protected $name;

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
	 * @var Collection @ORM\OneToMany(targetEntity="Moduleformation", mappedBy="groupmodule", cascade={"persist", "remove"}, fetch="EAGER")
	 *      @ORM\OrderBy({"code" = "ASC"})
	 *
	 */
	protected $moduleformations;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->dtCrea = new \DateTime('now');

		$this->moduleformations = new ArrayCollection();

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
	 * Get $name
	 *
	 * @return string
	 */
	public function getName()
	{

		return $this->name;

	}

	/**
	 * Set $name
	 *
	 * @param string $name
	 *
	 * @return Groupmodule $this
	 */
	public function setName($name)
	{

		$this->name = $name;

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
	 * @return Groupmodule $this
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
	 * @return Groupmodule $this
	 */
	public function setDtUpdate(\DateTime $dtUpdate = null)
	{

		$this->dtUpdate = $dtUpdate;

		return $this;

	}

	/**
	 * Add $moduleformation
	 *
	 * @param Moduleformation $moduleformation
	 *
	 * @return Groupmodule $this
	 */
	public function addModuleformation(Moduleformation $moduleformation)
	{

		$this->moduleformations[] = $moduleformation;

		return $this;

	}

	/**
	 * Remove $moduleformation
	 *
	 * @param Moduleformation $moduleformation
	 *
	 * @return Groupmodule $this
	 */
	public function removeModuleformation(Moduleformation $moduleformation)
	{

		$this->moduleformations->removeElement($moduleformation);

		return $this;

	}

	/**
	 * Get $moduleformations
	 *
	 * @return ArrayCollection
	 */
	public function getModuleformations()
	{

		return $this->moduleformations;

	}

	/**
	 * Set $moduleformations
	 *
	 * @param Collection $moduleformations
	 *
	 * @return Groupmodule $this
	 */
	public function setModuleformations(Collection $moduleformations)
	{

		$this->moduleformations = $moduleformations;

		return $this;

	}

	/**
	 * Get choiceLabel
	 *
	 * @return string
	 */
	public function getChoiceLabel()
	{

		return $this->getName();

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
			'moduleformations'
		);

	}

}
