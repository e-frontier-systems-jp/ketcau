<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Plugin::class, false)) {
    /**
     * @ORM\Table(name="dtb_plugin")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\PluginRepository")
     */
    class Plugin extends AbstractEntity
    {
        /**
         * @var int
         * @ORM\Column(name="id", type="integer", options={"unsigned": true})
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         * @ORM\Column(name="name", type="string", length=255)
         */
        private $name;

        /**
         * @var string
         * @ORM\Column(name="code", type="string", length=255)
         */
        private $code;

        /**
         * @var bool
         * @ORM\Column(name="enabled", type="boolean", options={"default": false})
         */
        private $enabled = false;

        /**
         * @var string
         * @ORM\Column(name="version", type="string", length=255)
         */
        private $version;

        /**
         * @var string
         * @ORM\Column(name="source", type="string", length=255)
         */
        private $source;

        /**
         * @var bool
         * @ORM\Column(name="initialized", type="boolean", options={"default": false})
         */
        private $initialized = false;

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



        public function getId(): int
        {
            return $this->id;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function setName(string $name): Plugin
        {
            $this->name = $name;
            return $this;
        }

        public function getCode(): string
        {
            return $this->code;
        }

        public function setCode(string $code): Plugin
        {
            $this->code = $code;
            return $this;
        }

        public function isEnabled(): bool
        {
            return $this->enabled;
        }

        public function setEnabled(bool $enabled): Plugin
        {
            $this->enabled = $enabled;
            return $this;
        }

        public function getVersion(): string
        {
            return $this->version;
        }

        public function setVersion(string $version): Plugin
        {
            $this->version = $version;
            return $this;
        }

        public function getSource(): string
        {
            return $this->source;
        }

        public function setSource(string $source): Plugin
        {
            $this->source = $source;
            return $this;
        }

        public function isInitialized(): bool
        {
            return $this->initialized;
        }

        public function setInitialized(bool $initialized): Plugin
        {
            $this->initialized = $initialized;
            return $this;
        }

        public function getCreateDate(): \DateTime
        {
            return $this->create_date;
        }

        public function setCreateDate(\DateTime $create_date): Plugin
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getUpdateDate(): \DateTime
        {
            return $this->update_date;
        }

        public function setUpdateDate(\DateTime $update_date): Plugin
        {
            $this->update_date = $update_date;
            return $this;
        }
    }
}
