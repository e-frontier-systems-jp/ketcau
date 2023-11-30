<?php

namespace Ketcau\Doctrine\Common\CsvDataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    protected $fixtures;


    public function loadFromDirectory($dir)
    {
        if (!dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist.', $dir));
        }

        $file = $dir. '/definition.yml';
        if (!file_exists($file)) {
            $finder = Finder::create()
                ->in($dir)
                ->name('*.csv');
        }
        $definition = Yaml::parse(file_get_contents($file));
        $definition = array_flip($definition);

        $finder = Finder::create()
            ->in($dir)
            ->name('*.csv')
            ->sort(
                function (\SplFileInfo $a, \SplFileInfo $b) use ($definition) {
                    if (!isset($definition[$a->getFilename()])) {
                        throw new \Exception(sprintf('"%s" is undefined in definition.yml', $a->getFilename()));
                    }
                    if (!isset($definition[$b->getFilename()])) {
                        throw new \Exception(sprintf('"%s" is undefined in definition.yml', $b->getFilename()));
                    }

                    $a_sortNo = $definition[$a->getFilename()];
                    $b_sortNo = $definition[$b->getFilename()];

                    if ($a_sortNo < $b_sortNo) {
                        return -1;
                    } else if ($a_sortNo > $b_sortNo) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            )
            ->files();

        return $this->loadFromIterator($finder->getIterator());
    }


    /**
     * @param \Iterator $iterator
     * @return CsvFixture[]
     */
    public function loadFromIterator(\Iterator $iterator)
    {
        $fixtures = [];
        foreach ($iterator as $fixture) {
            $CsvFixture = new CsvFixture($fixture->openFile());
            $this->addFixture($CsvFixture);
            $fixtures[] = $CsvFixture;
        }

        return $fixtures;
    }



    public function getFixture()
    {
        return $this->fixtures;
    }

    public function addFixture(FixtureInterface $fixture)
    {
        $this->fixtures[] = $fixture;
    }
}