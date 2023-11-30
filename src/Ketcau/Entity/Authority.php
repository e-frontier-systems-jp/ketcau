<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Authority::class, false)) {

    /**
     * @ORM\Table(name="dtb_authority")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\AuthorityRepository")
     */
    class Authority
    {
        /**
         * @var int
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         * @ORM\Column(name="id", type="integer", options={"unsigned": true})
         */
        private $id;


        /**
         * @var string
         * @ORM\Column(name="name", type="string", length=255)
         */
        private $name;


        /**
         * @var int
         * @ORM\Column(name="sort_no", type="integer", options={"unsigned": true})
         */
        private $sort_no;



        public function getId(): int
        {
            return $this->id;
        }

        public function setId(int $id): Authority
        {
            $this->id = $id;
            return $this;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function setName(string $name): Authority
        {
            $this->name = $name;
            return $this;
        }

        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        public function setSortNo(int $sort_no): Authority
        {
            $this->sort_no = $sort_no;
            return $this;
        }
    }
}