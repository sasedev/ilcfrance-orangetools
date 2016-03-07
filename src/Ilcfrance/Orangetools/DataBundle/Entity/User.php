<?php

namespace Ilcfrance\Orangetools\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Orangetools\DataBundle\Model\JsonSerializable;
use Sasedev\Commons\SharedBundle\Validator as ExtraAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 * @ORM\Table(name="ilc_orange_users")
 * @ORM\Entity(repositoryClass="Ilcfrance\Orangetools\DataBundle\EntityRepository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields={"username"}, errorPath="username", groups={"username"})
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class User implements UserInterface, \Serializable, JsonSerializable
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
	 * @var integer
	 */
	const INFOSENT_NO = 1;

	/**
	 *
	 * @var integer
	 */
	const INFOSENT_YES = 2;

	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 *
	 * @var string @ORM\Column(name="username", type="text", nullable=false)
	 *      @Assert\Length(min = "3", max = "100", groups={"username"})
	 *      @Assert\Regex(pattern="/^[a-z0-9][a-z0-9_.@][a-z0-9]+$/", groups={"username"})
	 */
	protected $username;

	/**
	 *
	 * @var string @ORM\Column(name="email", type="text", nullable=true)
	 *      @Assert\Email(checkMX=true, checkHost=true, groups={"email"})
	 */
	protected $email;

	/**
	 *
	 * @var string @ORM\Column(name="clearpassword", type="text", nullable=true)
	 *      @Assert\Length(min="8", max="200", groups={"clearPassword"})
	 */
	protected $clearPassword;

	/**
	 *
	 * @var string @ORM\Column(name="passwd", type="text", nullable=true)
	 */
	protected $password;

	/**
	 *
	 * @var string @ORM\Column(name="salt", type="text", nullable=true)
	 */
	protected $salt;

	/**
	 *
	 * @var string @ORM\Column(name="recoverycode", type="text", nullable=true)
	 */
	protected $recoveryCode;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="recoveryexpiration", type="datetime", nullable=true)
	 */
	protected $recoveryExpiration;

	/**
	 *
	 * @var integer @ORM\Column(name="lockout", type="integer", nullable=false)
	 *      @Assert\Choice(callback="choiceLockoutCallback", groups={"lockout"})
	 */
	protected $lockout;

	/**
	 *
	 * @var integer @ORM\Column(name="logins", type="integer", nullable=false)
	 */
	protected $logins;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="lastlogin", type="datetime", nullable=true)
	 */
	protected $lastLogin;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="lastactivity", type="datetime", nullable=true)
	 */
	protected $lastActivity;

	/**
	 *
	 * @var string @ORM\Column(name="lastname", type="text", nullable=true)
	 */
	protected $lastName;

	/**
	 *
	 * @var string @ORM\Column(name="firstname", type="text", nullable=true)
	 */
	protected $firstName;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="birthday", type="date", nullable=true)
	 */
	protected $birthday;

	/**
	 *
	 * @var string @ORM\Column(name="phone", type="text", nullable=true)
	 *      @ExtraAssert\Phone(groups={"phone"})
	 */
	protected $phone;

	/**
	 *
	 * @var string @ORM\Column(name="mobile", type="text", nullable=true)
	 *      @ExtraAssert\Phone(groups={"phone"})
	 */
	protected $mobile;

	/**
	 *
	 * @var string @ORM\Column(name="job", type="text", nullable=true)
	 */
	protected $job;

	/**
	 *
	 * @var string @ORM\Column(name="level", type="text", nullable=true)
	 */
	protected $level;

	/**
	 *
	 * @var string @ORM\Column(name="lastname2", type="text", nullable=true)
	 */
	protected $lastName2;

	/**
	 *
	 * @var string @ORM\Column(name="firstname2", type="text", nullable=true)
	 */
	protected $firstName2;

	/**
	 *
	 * @var string @ORM\Column(name="email2", type="text", nullable=true)
	 *      @Assert\Email(checkMX=true, checkHost=true, groups={"email2"})
	 */
	protected $email2;

	/**
	 *
	 * @var integer @ORM\Column(name="infosent", type="integer", nullable=false)
	 *      @Assert\Choice(callback="choiceInfoSentCallback", groups={"infoSent"})
	 */
	protected $infoSent;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="validunitil", type="datetime", nullable=true)
	 */
	protected $validUntil;

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
	 * @var Collection @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
	 *      @ORM\JoinTable(name="ilc_orange_users_roles",
	 *      joinColumns={
	 *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *      },
	 *      inverseJoinColumns={
	 *      @ORM\JoinColumn(name="role_id", referencedColumnName="id")
	 *      }
	 *      )
	 *      @ORM\OrderBy({"name" = "ASC"})
	 *
	 */
	protected $userRoles;

	/**
	 *
	 * @var Collection @ORM\OneToMany(targetEntity="Modulepreinscription", mappedBy="user", cascade={"persist", "remove"})
	 *      @ORM\OrderBy({"moduleformation" = "ASC"})
	 *
	 */
	protected $modulepreinscriptions;

	/**
	 *
	 * @var Collection @ORM\OneToMany(targetEntity="Sessioninscription", mappedBy="user", cascade={"persist", "remove"})
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
		$this->setSalt(md5(uniqid(null, true)));
		$this->setClearPassword(self::generateRandomChar(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'));
		$this->lockout = self::LOCKOUT_UNLOCKED;
		$this->logins = 0;
		$this->infoSent = self::INFOSENT_NO;

		$this->userRoles = new ArrayCollection();
		$this->modulepreinscriptions = new ArrayCollection();
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
	 * Get $username
	 *
	 * @return string
	 */
	public function getUsername()
	{

		return $this->username;

	}

	/**
	 * Set $username
	 *
	 * @param string $username
	 *
	 * @return User $this
	 */
	public function setUsername($username)
	{

		$this->username = strtolower(str_replace(' ', '.', trim($username)));

		return $this;

	}

	/**
	 * Get $email
	 *
	 * @return string
	 */
	public function getEmail()
	{

		return $this->email;

	}

	/**
	 * Set $email
	 *
	 * @param string $email
	 *
	 * @return User $this
	 */
	public function setEmail($email)
	{

		$this->email = strtolower(trim($email));

		return $this;

	}

	/**
	 * Get $clearPassword
	 *
	 * @return string
	 */
	public function getClearPassword()
	{

		return $this->clearPassword;

	}

	/**
	 * Set $clearPassword
	 *
	 * @param string $clearPassword
	 *
	 * @return User $this
	 */
	public function setClearPassword($clearPassword)
	{

		$this->clearPassword = $clearPassword;

		return $this->setPassword($clearPassword);

	}

	/**
	 * Get $password
	 *
	 * @return string
	 */
	public function getPassword()
	{

		return $this->password;

	}

	/**
	 * Set $password
	 *
	 * @param string $password
	 *
	 * @return User $this
	 */
	public function setPassword($password)
	{

		$encoder = new Pbkdf2PasswordEncoder('sha512', true, 1000);
		$this->password = $encoder->encodePassword($password, $this->getSalt());

		return $this;

	}

	/**
	 * Get $salt
	 *
	 * @return string
	 */
	public function getSalt()
	{

		return $this->salt;

	}

	/**
	 * Set $salt
	 *
	 * @param string $salt
	 *
	 * @return User $this
	 */
	public function setSalt($salt)
	{

		$this->salt = $salt;

		return $this;

	}

	/**
	 * Get $recoveryCode
	 *
	 * @return string
	 */
	public function getRecoveryCode()
	{

		return $this->recoveryCode;

	}

	/**
	 * Set $recoveryCode
	 *
	 * @param string $recoveryCode
	 *
	 * @return User $this
	 */
	public function setRecoveryCode($recoveryCode)
	{

		$this->recoveryCode = $recoveryCode;

		return $this;

	}

	/**
	 * Get $recoveryExpiration
	 *
	 * @return \DateTime
	 */
	public function getRecoveryExpiration()
	{

		return $this->recoveryExpiration;

	}

	/**
	 * Set $recoveryExpiration
	 *
	 * @param \DateTime $recoveryExpiration
	 *
	 * @return User $this
	 */
	public function setRecoveryExpiration(\DateTime $recoveryExpiration = null)
	{

		$this->recoveryExpiration = $recoveryExpiration;

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
	 * @return User $this
	 */
	public function setLockout($lockout)
	{

		$this->lockout = $lockout;

		return $this;

	}

	/**
	 * Get $logins
	 *
	 * @return integer
	 */
	public function getLogins()
	{

		return $this->logins;

	}

	/**
	 * Set $logins
	 *
	 * @param integer $logins
	 *
	 * @return User $this
	 */
	public function setLogins($logins)
	{

		$this->logins = $logins;

		return $this;

	}

	/**
	 * Get $lastLogin
	 *
	 * @return \DateTime
	 */
	public function getLastLogin()
	{

		return $this->lastLogin;

	}

	/**
	 * Set $lastLogin
	 *
	 * @param \DateTime $lastLogin
	 *
	 * @return User $this
	 */
	public function setLastLogin(\DateTime $lastLogin)
	{

		$this->lastLogin = $lastLogin;

		return $this;

	}

	/**
	 * Get $lastActivity
	 *
	 * @return \DateTime
	 */
	public function getLastActivity()
	{

		return $this->lastActivity;

	}

	/**
	 * Set $lastActivity
	 *
	 * @param \DateTime $lastActivity
	 *
	 * @return User $this
	 */
	public function setLastActivity(\DateTime $lastActivity)
	{

		$this->lastActivity = $lastActivity;

		return $this;

	}

	/**
	 * Get $lastName
	 *
	 * @return string
	 */
	public function getLastName()
	{

		return $this->lastName;

	}

	/**
	 * Set $lastName
	 *
	 * @param string $lastName
	 *
	 * @return User $this
	 */
	public function setLastName($lastName)
	{

		$this->lastName = $lastName;

		return $this;

	}

	/**
	 * Get $firstName
	 *
	 * @return string
	 */
	public function getFirstName()
	{

		return $this->firstName;

	}

	/**
	 * Set $firstName
	 *
	 * @param string $firstName
	 *
	 * @return User $this
	 */
	public function setFirstName($firstName)
	{

		$this->firstName = $firstName;

		return $this;

	}

	/**
	 * Get $birthday
	 *
	 * @return \DateTime
	 */
	public function getBirthday()
	{

		return $this->birthday;

	}

	/**
	 * Set $birthday
	 *
	 * @param \DateTime $birthday
	 *
	 * @return User $this
	 */
	public function setBirthday(\DateTime $birthday = null)
	{

		$this->birthday = $birthday;

		return $this;

	}

	/**
	 * Get $phone
	 *
	 * @return string
	 */
	public function getPhone()
	{

		return $this->phone;

	}

	/**
	 * Set $phone
	 *
	 * @param string $phone
	 *
	 * @return User $this
	 */
	public function setPhone($phone)
	{

		$this->phone = $phone;

		return $this;

	}

	/**
	 * Get $mobile
	 *
	 * @return string
	 */
	public function getMobile()
	{

		return $this->mobile;

	}

	/**
	 * Set $mobile
	 *
	 * @param string $mobile
	 *
	 * @return User $this
	 */
	public function setMobile($mobile)
	{

		$this->mobile = $mobile;

		return $this;

	}

	/**
	 * Get $job
	 *
	 * @return string
	 */
	public function getJob()
	{

		return $this->job;

	}

	/**
	 * Set $job
	 *
	 * @param string $job
	 *
	 * @return User $this
	 */
	public function setJob($job)
	{

		$this->job = $job;

		return $this;

	}

	/**
	 * Get $level
	 *
	 * @return string
	 */
	public function getLevel()
	{

		return $this->level;

	}

	/**
	 * Set $level
	 *
	 * @param string $level
	 *
	 * @return User $this
	 */
	public function setLevel($level)
	{

		$this->level = $level;

		return $this;

	}

	/**
	 * Get $lastName2
	 *
	 * @return string
	 */
	public function getLastName2()
	{

		return $this->lastName2;

	}

	/**
	 * Set $lastName2
	 *
	 * @param string $lastName2
	 *
	 * @return User $this
	 */
	public function setLastName2($lastName2)
	{

		$this->lastName2 = $lastName2;

		return $this;

	}

	/**
	 * Get $firstName2
	 *
	 * @return string
	 */
	public function getFirstName2()
	{

		return $this->firstName2;

	}

	/**
	 * Set $firstName2
	 *
	 * @param string $firstName2
	 *
	 * @return User $this
	 */
	public function setFirstName2($firstName2)
	{

		$this->firstName2 = $firstName2;

		return $this;

	}

	/**
	 * Get $email2
	 *
	 * @return string
	 */
	public function getEmail2()
	{

		return $this->email2;

	}

	/**
	 * Set $email2
	 *
	 * @param string $email2
	 *
	 * @return User $this
	 */
	public function setEmail2($email2)
	{

		$this->email2 = strtolower(trim($email2));

		return $this;

	}

	/**
	 * Get $infoSent
	 *
	 * @return integer
	 */
	public function getInfoSent()
	{

		return $this->infoSent;

	}

	/**
	 * Set $infoSent
	 *
	 * @param integer $infoSent
	 *
	 * @return User $this
	 */
	public function setInfoSent($infoSent)
	{

		$this->infoSent = $infoSent;

		return $this;

	}

	/**
	 * Get $validUntil
	 *
	 * @return \DateTime
	 */
	public function getValidUntil()
	{

		return $this->validUntil;

	}

	/**
	 * Set $validUntil
	 *
	 * @param \DateTime $validUntil
	 *
	 * @return User $this
	 */
	public function setValidUntil(\DateTime $validUntil = null)
	{

		$this->validUntil = $validUntil;

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
	 * @return User $this
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
	 * @return User $this
	 */
	public function setDtUpdate(\DateTime $dtUpdate = null)
	{

		$this->dtUpdate = $dtUpdate;

		return $this;

	}

	/**
	 * Add $role
	 *
	 * @param Role $role
	 *
	 * @return User $this
	 */
	public function addUserRole(Role $role)
	{

		$this->userRoles[] = $role;

		return $this;

	}

	/**
	 * Remove $role
	 *
	 * @param Role $role
	 *
	 * @return User $this
	 */
	public function removeUserRole(Role $role)
	{

		$this->userRoles->removeElement($role);

		return $this;

	}

	/**
	 * Get $userRoles
	 *
	 * @return ArrayCollection
	 */
	public function getUserRoles()
	{

		return $this->userRoles;

	}

	/**
	 * Set $userRoles
	 *
	 * @param Collection $userRoles
	 *
	 * @return User $this
	 */
	public function setUserRoles(Collection $userRoles)
	{

		$this->userRoles = $userRoles;

		return $this;

	}

	/**
	 *
	 * {@inheritDoc} @see UserInterface::getRoles()
	 */
	public function getRoles()
	{

		$roles = array();
		foreach ($this->userRoles as $role) {
			$roles[] = $role->getName();
			if ($role->getParents()) {
				foreach ($role->getParents() as $parent) {
					$roles = \array_merge($roles, $this->getRolesParentsIds($parent));
				}
			}
		}
		return $roles;

	}

	/**
	 *
	 * @param Roles $role
	 *
	 * @return array
	 */
	private function getRolesParentsIds(Role $role)
	{

		$roles = array();
		if ($role->getParents()) {
			foreach ($role->getParents() as $parent) {
				$roles = \array_merge($roles, $this->getRolesParentsIds($parent));
			}
		}
		$roles[] = $role->getName();
		return $roles;

	}

	/**
	 * Add $modulepreinscription
	 *
	 * @param Modulepreinscription $modulepreinscription
	 *
	 * @return User $this
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
	 * @return User $this
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
	 * @return User $this
	 */
	public function setModulepreinscriptions(Collection $modulepreinscriptions)
	{

		$this->modulepreinscriptions = $modulepreinscriptions;

		return $this;

	}

	/**
	 * Add $sessioninscription
	 *
	 * @param Sessioninscription $sessioninscription
	 *
	 * @return User $this
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
	 * @return User $this
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
	 * @return User $this
	 */
	public function setSessioninscriptions(Collection $sessioninscriptions)
	{

		$this->sessioninscriptions = $sessioninscriptions;

		return $this;

	}

	/**
	 * Get calculated fullName From username, firstName and lastName
	 *
	 * @return string
	 */
	public function getFullName()
	{

		if (null == $this->getFirstName() && null == $this->getLastName()) {
			return $this->getUsername();
		} elseif (null != $this->getFirstName() && null != $this->getLastName()) {
			return $this->getFirstName() . " " . $this->getLastName();
		} else {
			if (null != $this->getLastName()) {
				return $this->getLastName();
			}
			if (null != $this->getFirstName()) {
				return $this->getFirstName();
			}
		}

	}

	/**
	 * Get calculated fullName From username, firstName and lastName
	 *
	 * @return string
	 */
	public function getFullName2()
	{

		if (null == $this->getFirstName2() && null == $this->getLastName2()) {
			return $this->getEmail2();
		} elseif (null != $this->getFirstName2() && null != $this->getLastName2()) {
			return $this->getFirstName2() . " " . $this->getLastName2();
		} else {
			if (null != $this->getLastName2()) {
				return $this->getLastName2();
			}
			if (null != $this->getFirstName2()) {
				return $this->getFirstName2();
			}
		}

	}

	/**
	 * Get choiceLabel
	 *
	 * @return string
	 */
	public function getChoiceLabel()
	{

		return $this->getFullName().' ('.$this->getUsername().')';

	}

	/**
	 * Set the lastActivity to now
	 *
	 * @return User
	 */
	public function isActiveNow()
	{

		return $this->setLastActivity(new \DateTime());

	}

	/**
	 * Erases the user credentials.
	 *
	 * {@inheritDoc} @see UserInterface::eraseCredentials()
	 */
	public function eraseCredentials()
	{
		// $this->clearPassword = null;
		$this->recoveryCode = null;
		$this->recoveryExpiration = null;

	}

	/**
	 * Serializes the User.
	 * The serialized data have to contain the fields used by the equals method
	 * and the username.
	 *
	 * {@inheritDoc} @see Serializable::serialize()
	 * @return string
	 */
	public function serialize()
	{

		return \serialize(
			array(
				$this->password,
				$this->salt,
				$this->username,
				$this->email,
				$this->lockout,
				$this->id
			));

	}

	/**
	 * Unserializes the User.
	 *
	 * {@inheritDoc} @see Serializable::unserialize()
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{

		$data = \unserialize($serialized);
		// add a few extra elements in the array to ensure that we have enough
		// keys when
		// unserializing
		// older data which does not include all properties.
		$data = array_merge($data, array_fill(0, 2, null));

		list ($this->password, $this->salt, $this->username, $this->email, $this->lockout, $this->id) = $data;

	}

	/**
	 * Choice Form lockout
	 *
	 * @return multitype:string
	 */
	public static function choiceLockout()
	{

		return array(
			'User.lockout.choice.' . self::LOCKOUT_UNLOCKED => self::LOCKOUT_UNLOCKED,
			'User.lockout.choice.' . self::LOCKOUT_LOCKED => self::LOCKOUT_LOCKED
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
	 * Choice Form infoSent
	 *
	 * @return multitype:string
	 */
	public static function choiceInfoSent()
	{

		return array(
			'User.infoSent.choice.' . self::INFOSENT_NO => self::INFOSENT_NO,
			'User.infoSent.choice.' . self::INFOSENT_YES => self::INFOSENT_YES
		);

	}

	/**
	 * Choice Validator infoSent
	 *
	 * @return multitype:string
	 */
	public static function choiceInfoSentCallback()
	{

		return array(
			self::INFOSENT_NO,
			self::INFOSENT_YES
		);

	}

	/**
	 * Get a random char (for generated password)
	 *
	 * @param integer $length
	 * @param string $charset
	 *
	 * @return string
	 */
	public static function generateRandomChar($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#@!?+=*/-')
	{

		$str = '';
		$count = strlen($charset);
		while ($length --) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}

		return $str;

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
			"__initializer__",
			"clearPassword",
			"salt",
			"password",
			"recoveryCode",
			"recoveryExpiration"
		);

	}

	/**
	 *
	 * @return array
	 */
	public function getIgnoreList()
	{

		return array(
			'userRoles',
			'modulepreinscriptions',
			'sessioninscriptions'
		);

	}


}
