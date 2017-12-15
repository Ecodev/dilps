<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasInstitution;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="Application\Repository\UserRepository")
 */
class User extends AbstractModel
{
    use HasInstitution;

    /**
     * Someone who is a normal user, not part of UNIL
     */
    const TYPE_DEFAULT = 'default';

    /**
     * Someone who log in via UNIL system
     */
    const TYPE_UNIL = 'unil';

    /**
     * @var User
     */
    private static $currentUser;

    /**
     * Set currently logged in user
     * WARNING: this method should only be called from \Application\Authentication\AuthenticationListener
     *
     * @param \Application\Model\User $user
     */
    public static function setCurrentUser(self $user = null): void
    {
        self::$currentUser = $user;
    }

    /**
     * Returns currently logged user or null
     *
     * @return null|self
     */
    public static function getCurrentUser()
    {
        return self::$currentUser;
    }

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $login = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=191)
     */
    private $email;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isAdministrator = false;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $activeUntil;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $termsAgreement;

    /**
     * @var string
     * @ORM\Column(type="UserType", options={"default" = User::TYPE_DEFAULT})
     */
    private $type = self::TYPE_DEFAULT;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set login (eg: johndoe)
     *
     * @API\Input(type="Application\Api\Scalar\LoginType")
     *
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * Get login (eg: johndoe)
     *
     * @API\Field(type="Application\Api\Scalar\LoginType")
     *
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Encrypt and change the user password
     *
     * @API\Exclude
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Returns the hashed password
     *
     * @API\Exclude
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Returns whether the user is administrator and thus have can do anything.
     */
    public function isAdministrator(): bool
    {
        return $this->isAdministrator;
    }

    /**
     * Sets whether the user is administrator
     *
     * This property should only be set manually in DB by a developer.
     *
     * @API\Exclude
     *
     * @param bool $isAdministrator
     */
    public function setIsAdministrator(bool $isAdministrator): void
    {
        $this->isAdministrator = $isAdministrator;
    }

    /**
     * The date until the user is active. Or `null` if there is not limit in time
     *
     * @return null|DateTimeImmutable
     */
    public function getActiveUntil(): ?DateTimeImmutable
    {
        return $this->activeUntil;
    }

    /**
     * The date until the user is active. Or `null` if there is not limit in time
     *
     * @param null|DateTimeImmutable $activeUntil
     */
    public function setActiveUntil(?DateTimeImmutable $activeUntil): void
    {
        $this->activeUntil = $activeUntil;
    }

    /**
     * The date when the user agreed to the terms of usage
     *
     * @return null|DateTimeImmutable
     */
    public function getTermsAgreement(): ?DateTimeImmutable
    {
        return $this->termsAgreement;
    }

    /**
     * The date when the user agreed to the terms of usage.
     *
     * A user cannot un-agree once he agreed.
     *
     * @param null|DateTimeImmutable $termsAgreement
     */
    public function setTermsAgreement(?DateTimeImmutable $termsAgreement): void
    {
        $this->termsAgreement = $termsAgreement;
    }

    /**
     * Set user type
     *
     * @API\Input(type="Application\Api\Enum\UserTypeType")
     *
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get user type
     *
     * @API\Field(type="Application\Api\Enum\UserTypeType")
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
