<?php

namespace Ilcfrance\Orangetools\DataBundle\MongoDocument;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trace
 * @ODM\Document(collection="traces", repositoryClass="Ilcfrance\Orangetools\DataBundle\MongoRepository\TraceRepository")
 * @ODM\HasLifecycleCallbacks
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class Trace
{

	/**
	 *
	 * @var integer
	 */
	const AT_CREATE = 1;

	/**
	 *
	 * @var integer
	 */
	const AT_UPDATE = 2;

	/**
	 *
	 * @var integer
	 */
	const AT_DELETE = 3;

	/**
	 *
	 * @var string
	 */
	const AE_Role = "01Role";

	/**
	 *
	 * @var string
	 */
	const AE_User = "02User";

	/**
	 *
	 * @var string
	 */
	const AE_Groupmodule = "03Groupmodule";

	/**
	 *
	 * @var string
	 */
	const AE_Moduleformation = "04Moduleformation";

	/**
	 *
	 * @var string
	 */
	const AE_Modulepreinscription = "05Modulepreinscription";

	/**
	 *
	 * @var string
	 */
	const AE_Sessionformation = "06Sessionformation";

	/**
	 *
	 * @var string
	 */
	const AE_Sessioninscription = "07Sessioninscription";

	/**
	 *
	 * @var guid @ODM\Id(strategy="UUID")
	 */
	protected $id;

	/**
	 *
	 * @var string @ODM\String
	 */
	protected $msg;

	/**
	 *
	 * @var integer @ODM\Field(type="int")
	 *      @Assert\Choice(callback="choiceActionTypeCallback", groups={"actionType"})
	 */
	protected $actionType;

	/**
	 *
	 * @var string @ODM\Field(type="string")
	 *      @Assert\Choice(callback="choiceActionEntityCallback", groups={"actionType"})
	 */
	protected $actionEntity;

	/**
	 *
	 * @var string @ODM\Field(type="string")
	 */
	protected $actionId;

	/**
	 *
	 * @var string @ODM\Field(type="string")
	 */
	protected $userId;

	/**
	 *
	 * @var string @ODM\Field(type="string")
	 */
	protected $userFullname;

	/**
	 *
	 * @var \DateTime @ODM\Field(type="date")
	 */
	protected $dtCrea;

	/**
	 *
	 * @var \DateTime @ODM\Field(type="date")
	 *      @Gedmo\Timestampable(on="update")
	 */
	protected $dtUpdate;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->dtCrea = new \DateTime('now');

	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{

		return $this->id;

	}

	/**
	 * Get msg
	 *
	 * @return string
	 */
	public function getMsg()
	{

		return $this->msg;

	}

	/**
	 * Set msg
	 *
	 * @param string $msg
	 *
	 * @return Traces $this
	 */
	public function setMsg($msg)
	{

		$this->msg = $msg;

		return $this;

	}

	/**
	 * Get actionType
	 *
	 * @return integer
	 */
	public function getActionType()
	{

		return $this->actionType;

	}

	/**
	 * Set actionType
	 *
	 * @param integer $actionType
	 *
	 * @return Traces $this
	 */
	public function setActionType($actionType)
	{

		$this->actionType = $actionType;

		return $this;

	}

	/**
	 * Get actionEntity
	 *
	 * @return string
	 */
	public function getActionEntity()
	{

		return $this->actionEntity;

	}

	/**
	 * Set actionEntity
	 *
	 * @param string $actionEntity
	 *
	 * @return Traces $this
	 */
	public function setActionEntity($actionEntity)
	{

		$this->actionEntity = $actionEntity;

		return $this;

	}

	/**
	 * Get actionId
	 *
	 * @return string
	 */
	public function getActionId()
	{

		return $this->actionId;

	}

	/**
	 * Set actionId
	 *
	 * @param string $actionId
	 *
	 * @return Traces $this
	 */
	public function setActionId($actionId)
	{

		$this->actionId = $actionId;

		return $this;

	}

	/**
	 * Get userId
	 *
	 * @return string
	 */
	public function getUserId()
	{

		return $this->userId;

	}

	/**
	 * Set userId
	 *
	 * @param string $userId
	 *
	 * @return Traces $this
	 */
	public function setUserId($userId)
	{

		$this->userId = $userId;

		return $this;

	}

	/**
	 * Get userFullname
	 *
	 * @return string
	 */
	public function getUserFullname()
	{

		return $this->userFullname;

	}

	/**
	 * Set userFullname
	 *
	 * @param string $userFullname
	 *
	 * @return Traces $this
	 */
	public function setUserFullname($userFullname)
	{

		$this->userFullname = $userFullname;

		return $this;

	}

	/**
	 * Get dtCrea
	 *
	 * @return \DateTime
	 */
	public function getDtCrea()
	{

		return $this->dtCrea;

	}

	/**
	 * Set dtCrea
	 *
	 * @param \DateTime $dtCrea
	 *
	 * @return Traces $this
	 */
	public function setDtCrea($dtCrea)
	{

		$this->dtCrea = $dtCrea;

		return $this;

	}

	/**
	 * Get dtUpdate
	 *
	 * @return \DateTime
	 */
	public function getDtUpdate()
	{

		return $this->dtUpdate;

	}

	/**
	 * Set dtUpdate
	 *
	 * @param \DateTime $dtUpdate
	 *
	 * @return Traces $this
	 */
	public function setDtUpdate($dtUpdate)
	{

		$this->dtUpdate = $dtUpdate;

		return $this;

	}

	public function getDecodedMsg()
	{
		$str = \json_decode($this->getMsg(), true);
		if (null == $str) {
			return $this->getMsg();
		}
		return $str;
	}

	/**
	 * Choice Form actionType
	 *
	 * @return multitype:string
	 */
	public static function choiceActionType()
	{

		return array(
			'Traces.actionTypechoice.' . self::AT_CREATE => self::AT_CREATE,
			'Traces.actionTypechoice.' . self::AT_UPDATE => self::AT_UPDATE,
			'Traces.actionTypechoice.' . self::AT_DELETE => self::AT_DELETE
		);

	}

	/**
	 * Choice Validator actionType
	 *
	 * @return multitype:string
	 */
	public static function choiceActionTypeCallback()
	{

		return array(
			self::AT_CREATE,
			self::AT_UPDATE,
			self::AT_DELETE
		);

	}

	/**
	 * Choice Form actionEntity
	 *
	 * @return multitype:string
	 */
	public static function choiceActionEntity()
	{

		return array(
			'Trace.actionEntity.choice.' . self::AE_Role => self::AE_Role,
			'Trace.actionEntity.choice.' . self::AE_User => self::AE_User,
			'Trace.actionEntity.choice.' . self::AE_Groupmodule => self::AE_Groupmodule,
			'Trace.actionEntity.choice.' . self::AE_Moduleformation => self::AE_Moduleformation,
			'Trace.actionEntity.choice.' . self::AE_Modulepreinscription => self::AE_Modulepreinscription,
			'Trace.actionEntity.choice.' . self::AE_Sessionformation => self::AE_Sessionformation,
			'Trace.actionEntity.choice.' . self::AE_Sessioninscription => self::AE_Sessioninscription
		);

	}

	/**
	 * Choice Validator actionEntity
	 *
	 * @return multitype:string
	 */
	public static function choiceActionEntityCallback()
	{

		return array(
			self::AE_Role,
			self::AE_User,
			self::AE_Groupmodule,
			self::AE_Moduleformation,
			self::AE_Modulepreinscription,
			self::AE_Sessionformation,
			self::AE_Sessioninscription
		);

	}


}