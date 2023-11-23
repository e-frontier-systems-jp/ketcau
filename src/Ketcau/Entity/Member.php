<?php

namespace Ketcau\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

if (!class_exists('\Ketcau\Entity\Member')) {

    class Member extends \Ketcau\Entity\AbstractEntity implements UserInterface, \Serializable
    {

        public function serialize()
        {
            // TODO: Implement serialize() method.
        }

        public function unserialize(string $data)
        {
            // TODO: Implement unserialize() method.
        }

        public function getRoles(): array
        {
            // TODO: Implement getRoles() method.
        }

        public function eraseCredentials()
        {
            // TODO: Implement eraseCredentials() method.
        }

        public function getUserIdentifier(): string
        {
            // TODO: Implement getUserIdentifier() method.
        }
    }

}