<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ketcau\Entity\Master\Work;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

if (!class_exists('\Ketcau\Entity\Member')) {

    /**
     * @ORM\Table(name="dtb_member")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\MemberRepository")
     */
    class Member extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
    {
        /**
         * @var int
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         * @ORM\Column(name="id", type="integer", options={"unsigned": true})
         */
        private $id;


        /**
         * @var string|null
         * @ORM\Column(name="name", type="string", length=255, nullable=true)
         */
        private $name;


        #[ORM\Column(type: 'string', length: 180, unique: true, nullable: false)]
        private $mail;


        #[ORM\Column(type: 'json')]
        private $roles = [];

        /**
         * @var string|null
         * @ORM\Column(name="department", type="string", length=255, nullable=true)
         */
        private $department;


        /**
         * @var string
         * @ORM\Column(name="login_id", type="string", length=255)
         */
        private $login_id;


        /**
         * @var string
         * @Assert\NotBlank()
         * @Assert\Length(max=4096)
         */
        private $plainPassword;


        /**
         * @var string
         * @ORM\Column(name="password", type="string", length=255)
         */
        private $password;


        /**
         * @var string
         * @ORM\Column(name="salt", type="string", length=255, nullable=true)
         */
        private $salt;


        /**
         * @var int
         * @ORM\Column(name="sort_no", type="smallint", options={"unsigned": true})
         */
        private $sort_no;


        /**
         * @var string
         * @ORM\Column(name="two_factor_auth_key", type="string", length=255, nullable=true, options={"fixed": false})
         */
        private $two_factor_auth_key;


        /**
         * @var bool
         * @ORM\Column(name="two_factor_auth_enabled", type="boolean", nullable=false, options={"default": false})
         */
        private $two_factor_auth_enabled = false;


        /**
         * @var \DateTime
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;


        /**
         * @var \DateTime
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;


        /**
         * @var \DateTime|null
         * @ORM\Column(name="login_date", type="datetimetz", nullable=true)
         */
        private $login_date;


        /**
         * @var Work
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Master\Work")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="work_id", referencedColumnName="id")
         * })
         */
        private $Work;


        /**
         * @var Authority
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Authority")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="authority_id", referencedColumnName="id")
         * })
         */
        private $Authority;


        /**
         * @var Member
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Member")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;



        public function getId(): int
        {
            return $this->id;
        }

        public function setId(int $id): Member
        {
            $this->id = $id;
            return $this;
        }

        public function getName(): ?string
        {
            return $this->name;
        }

        public function setName(?string $name): Member
        {
            $this->name = $name;
            return $this;
        }

        public function getDepartment(): ?string
        {
            return $this->department;
        }

        public function setDepartment(?string $department): Member
        {
            $this->department = $department;
            return $this;
        }

        public function getLoginId(): string
        {
            return $this->login_id;
        }

        public function setLoginId(string $login_id): Member
        {
            $this->login_id = $login_id;
            return $this;
        }

        public function getPlainPassword(): string
        {
            return $this->plainPassword;
        }

        public function setPlainPassword(string $plainPassword): Member
        {
            $this->plainPassword = $plainPassword;
            return $this;
        }

        public function getPassword(): string
        {
            return $this->password;
        }

        public function setPassword(string $password): Member
        {
            $this->password = $password;
            return $this;
        }

        public function getSalt(): ?string
        {
            return null;
        }

        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        public function setSortNo(int $sort_no): Member
        {
            $this->sort_no = $sort_no;
            return $this;
        }

        public function getTwoFactorAuthKey(): string
        {
            return $this->two_factor_auth_key;
        }

        public function setTwoFactorAuthKey(string $two_factor_auth_key): Member
        {
            $this->two_factor_auth_key = $two_factor_auth_key;
            return $this;
        }

        public function isTwoFactorAuthEnabled(): bool
        {
            return $this->two_factor_auth_enabled;
        }

        public function setTwoFactorAuthEnabled(bool $two_factor_auth_enabled): Member
        {
            $this->two_factor_auth_enabled = $two_factor_auth_enabled;
            return $this;
        }

        public function getCreateDate(): \DateTime
        {
            return $this->create_date;
        }

        public function setCreateDate(\DateTime $create_date): Member
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getUpdateDate(): \DateTime
        {
            return $this->update_date;
        }

        public function setUpdateDate(\DateTime $update_date): Member
        {
            $this->update_date = $update_date;
            return $this;
        }

        public function getLoginDate(): ?\DateTime
        {
            return $this->login_date;
        }

        public function setLoginDate(?\DateTime $login_date): Member
        {
            $this->login_date = $login_date;
            return $this;
        }

        public function getWork(): Master\Work
        {
            return $this->Work;
        }

        public function setWork(Master\Work $Work): Member
        {
            $this->Work = $Work;
            return $this;
        }

        public function getAuthority(): Authority
        {
            return $this->Authority;
        }

        public function setAuthority(Authority $Authority): Member
        {
            $this->Authority = $Authority;
            return $this;
        }

        public function getCreator(): Member
        {
            return $this->Creator;
        }

        public function setCreator(Member $Creator): Member
        {
            $this->Creator = $Creator;
            return $this;
        }

        /**
         * @return ?string
         */
        public function getMail(): ?string
        {
            return $this->mail;
        }

        /**
         * @param string $mail
         * @return Member
         */
        public function setMail(string $mail): self
        {
            $this->mail = $mail;
            return $this;
        }

        public function getRoles(): array
        {
            $roles = $this->roles;
            $roles[] = 'ROLE_ADMIN';
            return array_unique($roles);
        }


        public function setRoles($roles): self
        {
            $this->roles = $roles;
            return $this;
        }


        public function eraseCredentials()
        {
        }

        public function getUserIdentifier(): string
        {
            return $this->login_id;
        }


        public static function loadValidatorMetadata(ClassMetadata $metadata)
        {
            $metadata->addConstraint(new UniqueEntity([
                'fields' => 'login_id',
                'message' => 'form_error.member_already_exists',
            ]));
        }

        public function serialize()
        {
            return serialize([
                $this->id,
                $this->login_id,
                $this->password,
                $this->salt,
            ]);
        }

        public function unserialize(string $data)
        {
            list(
                $this->id,
                $this->login_id,
                $this->password,
                $this->salt
                ) = unserialize($data);
        }

        public function __serialize(): array
        {
            return [
                'id' => $this->id,
                'login_id' => $this->login_id,
                'password' => $this->password,
                'salt' => $this->salt,
            ];
        }

        public function __unserialize(array $data): void
        {
            $this->id = $data['id'];
            $this->login_id = $data['login_id'];
            $this->password = $data['password'];
            $this->salt = $data['salt'];
        }


        public function __toString(): string
        {
            return (string) $this->getName();
        }
    }
}