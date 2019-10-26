<?php

/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection SqlResolve */

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Database\Connection;
use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\RepositoryInterface;
use PDO;

trait Factory
{
    /**
     * Create PDO instance
     *
     * @return PDO
     */
    public function pdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE user (
                      id            INTEGER PRIMARY KEY,
                      type_id       INTEGER  NOT NULL,
                      username      TEXT     NOT NULL,
                      first_name    TEXT     NOT NULL,
                      middle_name   TEXT     NULL,
                      last_name     TEXT     NOT NULL,
                      active        INTEGER  NOT NULL DEFAULT 1,
                      register_date DATETIME NOT NULL,
                      update_date   DATETIME NOT NULL,
                      deleted_date  DATETIME NULL
                    )');
        $createdDate = new DateTime();
        $pdo->exec("INSERT INTO `user` (id, type_id, username, first_name, middle_name, last_name, active, register_date, update_date, deleted_date)
                    VALUES (1, 1, 'jdoe', 'John', NULL, 'Doe', 1, '2019-01-01 12:00:00', '{$createdDate->format('Y-m-d H:i:s')}', NULL),
                      (2, 1, 'janedoe', 'Jane', NULL, 'Doe', 0, '2019-01-01 12:00:00', '{$createdDate->format('Y-m-d H:i:s')}', '{$createdDate->format('Y-m-d H:i:s')}'),
                      (3, 1, 'bobthecow', 'Bob', 'the', 'Cow', 1, '2019-01-01 12:00:00', '{$createdDate->format('Y-m-d H:i:s')}', NULL)");
        $pdo->exec("CREATE TABLE `group_user` (
                      user_id  INTEGER NOT NULL,
                      group_id INTEGER NOT NULL,
                      CONSTRAINT group_user_user_id_group_id_pk PRIMARY KEY (user_id, group_id)
                    );");
        $pdo->exec("INSERT INTO `group_user` (user_id, group_id) 
                    VALUES (1, 1), 
                    (1, 2);");
        $pdo->exec("CREATE TABLE `group` (
                      id    INTEGER PRIMARY KEY,
                      name  VARCHAR(100) NOT NULL,
                      title TEXT NOT NULL
                    )");
        $pdo->exec("INSERT INTO `group` (id, name, title)
                    VALUES (1, 'test_name', 'Test Title'),
                      (2, 'test_name_2', 'Test Title 2');");
        $pdo->exec("CREATE TABLE address (
                      id           INTEGER PRIMARY KEY,
                      user_id      INTEGER NOT NULL,
                      street       TEXT,
                      house_number TEXT,
                      zip_code     TEXT,
                      city         TEXT,
                      country      TEXT
                    );");
        $pdo->exec("INSERT INTO address (id, user_id)
                    VALUES (1, 1);");
        $pdo->exec("CREATE TABLE `type` (
                      id           INTEGER PRIMARY KEY,
                      name  VARCHAR(100) NOT NULL
                    )");
        $pdo->exec("CREATE TABLE `time_stamps` (
                      id INTEGER PRIMARY KEY ,
                      created_at DATETIME NOT NULL,
                      updated_at DATETIME NOT NULL,
                      deleted_at DATETIME DEFAULT NULL
                    )");

        return $pdo;
    }

    /**
     * Create connection instance
     *
     * @return Connection
     */
    public function connection(): Connection
    {
        return new Connection($this->pdo());
    }

    /**
     * Create policy instance
     *
     * @return Policy
     */
    public function policy(): Policy
    {
        return new Policy();
    }

    /**
     * Create manager instance
     *
     * @return Manager
     */
    public function manager(): Manager
    {
        return new Manager($this->connection(), $this->policy());
    }

    /**
     * Create repository instance
     *
     * @param string $class
     * @return RepositoryInterface
     */
    public function repository(string $class)
    {
        return $this->policy()->repository($class, $this->connection());
    }
}
