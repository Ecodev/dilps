<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Acl\Acl;
use Application\Api\Exception;
use Application\ORM\Query\Filter\AclFilter;
use Application\Traits\HasInstitution;
use Application\Utility;
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
     * Empty shell used for legacy
     */
    const TYPE_LEGACY = 'legacy';

    const ROLE_ANONYMOUS = 'anonymous';
    const ROLE_STUDENT = 'student';
    const ROLE_JUNIOR = 'junior';
    const ROLE_SENIOR = 'senior';
    const ROLE_ADMINISTRATOR = 'administrator';

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
    public static function setCurrent(?self $user): void
    {
        self::$currentUser = $user;

        // Initalize ACL filter with current user if a logged in one exists
        _em()->getFilters()->getFilter(AclFilter::class)->setUser($user);
    }

    /**
     * Returns currently logged user or null
     *
     * @return null|self
     */
    public static function getCurrent(): ?self
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
     * @var string
     * @ORM\Column(type="UserRole", options={"default" = User::ROLE_STUDENT})
     */
    private $role = self::ROLE_STUDENT;

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
     *
     * @param string $role role for new user
     */
    public function __construct(string $role = self::ROLE_STUDENT)
    {
        $this->role = $role;
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
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        // Ignore empty password that could be sent "by mistake" by the client
        // when agreeing to terms
        if ($password === '') {
            return;
        }

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
     *
     * @API\Field(type="Application\Api\Enum\UserRoleType")
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Sets the user role
     *
     * The current user is allowed to promote another user up to the same role as himself. So
     * a Senior can promote a Student to Senior. Or an Admin can promote a Junior to Admin.
     *
     * But the current user is **not** allowed to demote a user who has a higher role than himself.
     * That means that a Senior cannot demote an Admin to Student.
     *
     * @param string $role
     */
    public function setRole(string $role): void
    {
        if ($role === $this->role) {
            return;
        }

        $currentRole = self::getCurrent() ? self::getCurrent()->getRole() : self::ROLE_ANONYMOUS;
        $orderedRoles = [
            self::ROLE_ANONYMOUS,
            self::ROLE_STUDENT,
            self::ROLE_JUNIOR,
            self::ROLE_SENIOR,
            self::ROLE_ADMINISTRATOR,
        ];

        $newFound = false;
        $oldFound = false;
        foreach ($orderedRoles as $r) {
            if ($r === $this->role) {
                $oldFound = true;
            }
            if ($r === $role) {
                $newFound = true;
            }

            if ($r === $currentRole) {
                break;
            }
        }

        if (!$newFound || !$oldFound) {
            throw new Exception($currentRole . ' is not allowed to change role to ' . $role);
        }

        $this->role = $role;
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

    /**
     * Get a list of global permissions for this user
     *
     * @API\Field(type="GlobalPermissionsList")
     *
     * @return array
     */
    public function getGlobalPermissions(): array
    {
        $acl = new Acl();
        $types = [
            Artist::class,
            Card::class,
            Change::class,
            Collection::class,
            Country::class,
            Dating::class,
            Institution::class,
            Tag::class,
            self::class,
        ];

        $permissions = ['create'];
        $result = [];

        $previousUser = self::getCurrent();
        self::setCurrent($this);
        foreach ($types as $type) {
            $instance = new $type();
            $sh = lcfirst(Utility::getShortClassName($instance));
            $result[$sh] = [];

            foreach ($permissions as $p) {
                $result[$sh][$p] = $acl->isCurrentUserAllowed($instance, $p);
            }
        }

        self::setCurrent($previousUser);

        return $result;
    }
}
