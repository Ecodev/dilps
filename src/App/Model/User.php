<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_email", columns={"email"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends AbstractModel
{
    use \App\Traits\Name;

    /**
     * @var User
     */
    private static $currentUser;

    /**
     * Set currently logged in user
     * WARNING: this method should only be called from \App\Authentication\AuthenticationListener
     *
     * @param \App\Model\User $user
     */
    public static function setCurrentUser(User $user = null)
    {
        self::$currentUser = $user;
    }

    /**
     * Returns currently logged user or null
     *
     * @return self|null
     */
    public static function getCurrentUser()
    {
        return self::$currentUser;
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"default" = "fr"}))
     */
    private $language = 'fr';

    /**
     * URL of photo for the user
     *
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $photo;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isAdministrator = false;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description = '';

    /**
     * @var int sex according to ISO/IEC 5218
     * @ORM\Column(type="smallint", options={"default" = "0"}))
     */
    private $sex = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $newsletter = true;

    /**
     * @var string
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profession;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ministry;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $firstLogin;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set the name automatically from firstname and lastname
     *
     * @param string $name
     *
     * @return self
     */
    private function setName($name = null)
    {
        $this->name = trim(trim($this->getFirstname()) . ' ' . trim($this->getLastname()));

        return $this;
    }

    /**
     * Set title (eg: Sir, Doctor)
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title (eg: Sir, Doctor)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        $this->setName();

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        $this->setName();

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     * Nobody can set an email, except via a Profile
     *
     * @param string $email
     *
     * @return self
     */
    private function setEmail($email)
    {
        $this->email = $email;
        $this->useGravatarAsDefaultPhoto();

        return $this;
    }

    /**
     * If photo is non-existent or already a gravatar, then
     * update it with latest email information
     */
    private function useGravatarAsDefaultPhoto()
    {
        $photo = $this->getPhoto();
        $email = $this->getEmail();
        $gravatarBase = 'https://secure.gravatar.com/avatar/';
        if ($email && (!$photo || mb_strpos($photo, $gravatarBase) !== false)) {
            $gravatar = $gravatarBase . md5(mb_strtolower(trim($email))) . '?d=identicon';
            $this->setPhoto($gravatar);
        }
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
     * Get ISO 639-1 language code
     *
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set ISO 639-1 language code
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set photo URL
     *
     * @param string $photo
     *
     * @return self
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo URL
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Returns whether the user is administrator and thus have can do anything.
     * This property should only be set manually in DB by a developer.
     */
    public function isAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Sets whether the user is administrator
     *
     * @param bool $isAdministrator
     *
     * @return self
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the ISO/IEC 5218 sex
     *
     * @param int $sex
     *
     * @return self
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get the ISO/IEC 5218 sex
     *
     * @return int
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Get the newsletter
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set the newsletter
     *
     * @param bool $newsletter
     *
     * @return self
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param string $skype
     *
     * @return self
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     *
     * @return self
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * @return string
     */
    public function getMinistry()
    {
        return $this->ministry;
    }

    /**
     * @param string $ministry
     *
     * @return self
     */
    public function setMinistry($ministry)
    {
        $this->ministry = $ministry;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTimeImmutable $lastLogin
     *
     * @return self
     */
    public function setLastLogin(\DateTimeImmutable $lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * @param \DateTimeImmutable $firstLogin
     *
     * @return self
     */
    public function setFirstLogin(\DateTimeImmutable $firstLogin)
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

}
