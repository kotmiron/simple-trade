<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users implements UserInterface, \Serializable
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $login
     *
     * @ORM\Column(name="login", type="string", length=128, nullable=false)
     */
    private $login;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string $position
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @var string $grounds
     *
     * @ORM\Column(name="grounds", type="string", length=255, nullable=true)
     */
    private $grounds;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=45, nullable=false)
     */
    private $password;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=45, nullable=false)
     */
    private $salt;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var Regions
     *
     * @ORM\ManyToOne(targetEntity="Regions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     * })
     */
    private $region;

    /**
     * @var boolean $isGod
     *
     * @ORM\Column(name="is_god", type="boolean", nullable=false)
     */
    private $isGod = false;

    /**
     * @var boolean $isBlocked
     *
     * @ORM\Column(name="is_blocked", type="boolean", nullable=false)
     */
    private $isBlocked = false;

    /**
     * @ORM\OneToMany(targetEntity="UsersRoles", mappedBy="user")
     */
    private $usersRoles;

    public $permissions;

    public $regionId;

    private $pathRoles = array();

    private $systemRoles = array();

    private $acl;

    public $lastname;

    public $firstname;

    public $patronymic;

    /**
     * Set id
     *
     * @return Users
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set login
     *
     * @param string $login
     * @return Users
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Users
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Users
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set grounds
     *
     * @param string $grounds
     * @return Users
     */
    public function setGrounds($grounds)
    {
        $this->grounds = $grounds;

        return $this;
    }

    /**
     * Get grounds
     *
     * @return string
     */
    public function getGrounds()
    {
        return $this->grounds;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Users
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isBlocked
     *
     * @param string $isBlocked
     * @return Users
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * Get isBlocked
     *
     * @return string
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password, $factory = NULL)
    {
        if (!$factory)
        {
            $this->password = $password;
            return $this;
        }
        $encoder = $factory->getEncoder($this);
        $this->setSalt();
        $password = $encoder->encodePassword($password, $this->getSalt());
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Users
     */
    public function setSalt()
    {
        $n = 45;
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
        $counter = strlen($pattern)-1;
        for($i=0; $i<$n; $i++)
        {
            $key .= $pattern{rand(0,$counter)};
        }
        $this->salt = $key;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Users
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set region
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Regions $region
     * @return Users
     */
    public function setRegion(\SimpleSolution\SimpleTradeBundle\Entity\Regions $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Regions
     */
    public function getRegion()
    {
        return $this->region;
    }

    public function getUserName()
    {
        return $this->login;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
        return;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getRegionId()
    {
        return $this->regionId;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->login,
            $this->password
            ));
    }

    public function unserialize($data)
    {
        list(
        $this->id,
        $this->login,
        $this->password
        ) = unserialize($data);
    }

    public function setPathRoles($pathRoles)
    {
        $this->pathRoles = $pathRoles;

        return $this;
    }

    public function getPathRoles()
    {
        return $this->pathRoles;
    }

    public function setSystemRoles($systemRoles)
    {
        $this->systemRoles = $systemRoles;

        return $this;
    }

    public function getSystemRoles()
    {
        return $this->systemRoles;
    }

    public function checkSystemRole($systemRole)
    {
        return in_array($systemRole, $this->systemRoles);
    }

    public function canI($path, $object = NULL, $role = '')
    {
        if ($this->isGod === true)
            return true;

        if (in_array($path, $this->pathRoles))
        {
            if ($object)
            {
                switch($role)
                {
                    case 'change':
                        $mask = MaskBuilder::MASK_EDIT;
                    break;
                    case 'remove':
                        $mask = MaskBuilder::MASK_DELETE;
                    break;
                    default:
                        return false;
                }
                return $this->acl->isGranted($object, $mask);
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    public function isGod()
    {
        return $this->isGod;
    }

    public function isBlocked()
    {
        return $this->isBlocked;
    }

    public function setAcl($acl)
    {
        $this->acl = $acl;

        return $this;
    }

    public function getRestoreSalt()
    {
        return 'supersecretSalt';
    }

    public function generatePassword( $iChars = 8, $iComplexity = 1 )
    {
        $a = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', );
        if( $iComplexity > 1 )
        {
            $a = array_merge( $a, array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', ) );
        }
        if( $iComplexity > 2 )
        {
            $a = array_merge( $a, array( '.', ',', '(', ')', '[', ']', '!', '?', '&', '^', '%', '@', '*', '$', '<', '>', '/', '|', '+', '-', '{', '}', '`', '~', ) );
        }
        for( $i = 0, $s = '', $iCount = count( $a ) - 1; $i < $iChars; $i++ )
        {
            $s .= $a[ rand( 0, $iCount ) ];
        }
        return $s;
    }

    public function createRestoreLink()
    {
        return md5($this->getRestoreSalt().$this->getLogin().$this->getPassword());
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getPatronymic()
    {
        return $this->patronymic;
    }
}