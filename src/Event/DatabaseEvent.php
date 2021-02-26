<?php
namespace NeutronStars\Neutrino\Event;

use NeutronStars\Database\Database;
use NeutronStars\Events\Event;

class DatabaseEvent implements Event
{
    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * @param Database $database
     */
    public function setDatabase(Database $database): void
    {
        $this->database = $database;
    }
}
