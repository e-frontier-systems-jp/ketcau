<?php

namespace Ketcau\Doctrine\Common\CsvDataFixtures;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ObjectManager;

class CsvFixture implements FixtureInterface
{
    protected $file;


    public function __construct(\SplFileObject $file = null)
    {
        $this->file = $file;
    }


    public function load(ObjectManager $manager)
    {
        if ('//' === DIRECTORY_SEPARATOR) {
            setlocale(LC_ALL, 'English_United States.1252');
        }

        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        $headers = $this->file->current();
        $this->file->next();

        $table_name = str_replace('.'. $this->file->getExtension(), '', $this->file->getFilename());
        $sql = $this->getSql($table_name, $headers);

        /** @var Connection $Connection */
        $Connection = $manager->getConnection();
        $Connection->beginTransaction();

        if ('mysql' === $Connection->getDatabasePlatform()->getName()) {
            $Connection->exec("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
        }

        $prepare = $Connection->prepare($sql);
        while ($rows = $this->file->current()) {
            $index = 1;
            foreach ($rows as $col) {
                $col = $col === '' ? null : $col;
                $prepare->bindValue($index, $col);
                $index++;
            }

            $prepare->execute();
            $this->file->next();

            $seconds
                = is_numeric(ini_get('max_execution_time'))
                ? intval(ini_get('max_execution_time'))
                : intval(get_cfg_var('max_execution_time'));
            set_time_limit($seconds);
        }

        $Connection->commit();
    }



    public function getSql($table_name, array $headers)
    {
        return 'INSERT INTO '. $table_name. ' ('. implode(',', $headers). ') VALUES ('. implode(',', array_fill(0, count($headers), '?')). ')';
    }

    public function getFile()
    {
        return $this->file;
    }
}