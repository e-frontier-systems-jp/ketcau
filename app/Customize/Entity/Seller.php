<?php

namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ketcau\Entity\AbstractEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

if (!class_exists(Seller::class, false)) {

    /**
     * @ORM\Table(name="dtb_seller")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Customize\Repository\SellerRepository")
     */
    class Seller extends AbstractEntity implements UserInterface, \Serializable
    {
        /**
         * @var integer
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         */
        private $id;

        /**
         * @var string
         * @ORM\Column(name="name01", type="string", length=255)
         */
        private $name01;

        /**
         * @var string
         * @ORM\Column(name="name02", type="string", length=255)
         */
        private $name02;

        /**
         * @var string
         * @ORM\Column(name="kana01", type="string", length=255)
         */
        private $kana01;

        /**
         * @var string
         * @ORM\Column(name="kana02", type="string", length=255)
         */
        private $kana02;

        /**
         * @var string
         * @ORM\Column(name="company_name", type="string", length=255)
         */
        private $company_name;

        /**
         * @var string
         * @ORM\Column(name="company_url", type="string", length=255, nullable=true)
         */
        private $company_url;

        /**
         * @var string
         * @ORM\Column(name="email", type="string", length=255)
         */
        private $email;

        /**
         * @var string
         * @ORM\Column(name="phone_number", type="string", length=14)
         */
        private $phone_number;

        /**
         * @var string|null
         * @ORM\Column(name="postal_code", type="string", length=8, nullable=true)
         */
        private $postal_code;

        /**
         * @var string
         * @ORM\Column(name="addr01", type="string", length=255)
         */
        private $addr01;

        /**
         * @var string
         * @ORM\Column(name="addr02", type="string", length=255)
         */
        private $addr02;


        /**
         * @var string
         * @Assert\NotBlank()
         * @Assert\Length(max="4096")
         */
        private $plain_password;

        /**
         * @var string|null
         * @ORM\Column(name="password", type="string", length=255)
         */
        private $password;

        /**
         * @var string|null
         * @ORM\Column(name="salt", type="string", length=255, nullable=true)
         */
        private $salt;

        /**
         * @var string
         * @ORM\Column(name="secret_key", type="string", length=255)
         */
        private $secret_key;


        /**
         * @var string|null
         * @ORM\Column(name="note", type="string", length=4000, nullable=true)
         */
        private $note;

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
         * @var \Ketcau\Entity\Master\Pref
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Master\Pref")
         * @ORM\JoinCOlumns({
         *     @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         */
        private $Pref;




        public function getId(): int
        {
            return $this->id;
        }

        public function setId(int $id): Seller
        {
            $this->id = $id;
            return $this;
        }

        public function getName01(): string
        {
            return $this->name01;
        }

        public function setName01(string $name01): Seller
        {
            $this->name01 = $name01;
            return $this;
        }

        public function getName02(): string
        {
            return $this->name02;
        }

        public function setName02(string $name02): Seller
        {
            $this->name02 = $name02;
            return $this;
        }

        public function getKana01(): string
        {
            return $this->kana01;
        }

        public function setKana01(string $kana01): Seller
        {
            $this->kana01 = $kana01;
            return $this;
        }

        public function getKana02(): string
        {
            return $this->kana02;
        }

        public function setKana02(string $kana02): Seller
        {
            $this->kana02 = $kana02;
            return $this;
        }

        public function getCompanyName(): string
        {
            return $this->company_name;
        }

        public function setCompanyName(string $company_name): Seller
        {
            $this->company_name = $company_name;
            return $this;
        }

        public function getCompanyUrl(): string
        {
            return $this->company_url;
        }

        public function setCompanyUrl(string $company_url): Seller
        {
            $this->company_url = $company_url;
            return $this;
        }

        public function getEmail(): string
        {
            return $this->email;
        }

        public function setEmail(string $email): Seller
        {
            $this->email = $email;
            return $this;
        }

        public function getPhoneNumber(): string
        {
            return $this->phone_number;
        }

        public function setPhoneNumber(string $phone_number): Seller
        {
            $this->phone_number = $phone_number;
            return $this;
        }

        public function getPostalCode(): ?string
        {
            return $this->postal_code;
        }

        public function setPostalCode(?string $postal_code): Seller
        {
            $this->postal_code = $postal_code;
            return $this;
        }

        public function getAddr01(): string
        {
            return $this->addr01;
        }

        public function setAddr01(string $addr01): Seller
        {
            $this->addr01 = $addr01;
            return $this;
        }

        public function getAddr02(): string
        {
            return $this->addr02;
        }

        public function setAddr02(string $addr02): Seller
        {
            $this->addr02 = $addr02;
            return $this;
        }

        public function getPlainPassword(): string
        {
            return $this->plain_password;
        }

        public function setPlainPassword(string $plain_password): Seller
        {
            $this->plain_password = $plain_password;
            return $this;
        }

        public function getPassword(): ?string
        {
            return $this->password;
        }

        public function setPassword(?string $password): Seller
        {
            $this->password = $password;
            return $this;
        }

        public function getSalt(): ?string
        {
            return $this->salt;
        }

        public function setSalt(?string $salt): Seller
        {
            $this->salt = $salt;
            return $this;
        }

        public function getSecretKey(): string
        {
            return $this->secret_key;
        }

        public function setSecretKey(string $secret_key): Seller
        {
            $this->secret_key = $secret_key;
            return $this;
        }

        public function getNote(): ?string
        {
            return $this->note;
        }

        public function setNote(?string $note): Seller
        {
            $this->note = $note;
            return $this;
        }

        public function getCreateDate(): \DateTime
        {
            return $this->create_date;
        }

        public function setCreateDate(\DateTime $create_date): Seller
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getUpdateDate(): \DateTime
        {
            return $this->update_date;
        }

        public function setUpdateDate(\DateTime $update_date): Seller
        {
            $this->update_date = $update_date;
            return $this;
        }

        public function getPref(): \Ketcau\Entity\Master\Pref
        {
            return $this->Pref;
        }

        public function setPref(\Ketcau\Entity\Master\Pref $Pref): Seller
        {
            $this->Pref = $Pref;
            return $this;
        }


        public function serialize()
        {
            return serialize([
                $this->id,
                $this->email,
                $this->password,
                $this->salt,
            ]);
        }

        public function unserialize(string $data)
        {
            list(
                $this->id,
                $this->email,
                $this->password,
                $this->salt
                ) = unserialize($data);
        }

        public function getRoles(): array
        {
            return ['ROLE_USER', 'ROLE_SELLER'];
        }

        public function eraseCredentials()
        {
        }

        public function getUserIdentifier(): string
        {
            return $this->email;
        }

        public function __serialize(): array
        {
            return [
                $this->id,
                $this->email,
                $this->password,
                $this->salt,
            ];
        }

        public function __unserialize(array $data): void
        {
            list(
                $this->id,
                $this->email,
                $this->password,
                $this->salt
                ) = $data;
        }


        public function __toString(): string
        {
            return (string) ($this->getName01()). ' '. ($this->getName02());
        }
    }
}