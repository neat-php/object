<?php

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Database\Connection;
use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\RepositoryInterface;
use PDO;
use ReflectionProperty;

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
        $createdDate = new DateTime();
        /** @lang SQLite */
        $sql = <<<SQL
CREATE TABLE `type`
(
    id   INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);
INSERT INTO `type` (id, name)
VALUES (1, 'admin'),
       (2, 'user');
CREATE TABLE user
(
    id            INTEGER PRIMARY KEY,
    type_id       INTEGER  NOT NULL,
    username      TEXT     NOT NULL,
    first_name    TEXT     NOT NULL,
    middle_name   TEXT     NULL,
    last_name     TEXT     NOT NULL,
    active        INTEGER  NOT NULL DEFAULT 1,
    register_date DATETIME NOT NULL,
    update_date   DATETIME NOT NULL,
    deleted_date  DATETIME NULL,
    FOREIGN KEY (type_id)
        REFERENCES type (id)
        ON DELETE RESTRICT
);
INSERT INTO `user` (id, type_id, username, first_name, middle_name, last_name, active, register_date, update_date, deleted_date)
VALUES (1, 1, 'jdoe', 'John', NULL, 'Doe', 1, '2019-01-01 12:00:00', '{$createdDate->format('Y-m-d H:i:s')}', NULL),
       (2, 2, 'janedoe', 'Jane', NULL, 'Doe', 0, '2019-01-01 12:00:00', '{$createdDate->format('Y-m-d H:i:s')}', '{$createdDate->format('Y-m-d H:i:s')}'),
       (3, 2, 'bobthecow', 'Bob', 'the', 'Cow', 1, '2019-01-01 12:00:00', '{$createdDate->format('Y-m-d H:i:s')}', NULL);
CREATE TABLE `group`
(
    id    INTEGER PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    title TEXT         NOT NULL
);
INSERT INTO `group` (id, name, title)
VALUES (1, 'test_name', 'Test Title'),
       (2, 'test_name_2', 'Test Title 2');
CREATE TABLE `group_user`
(
    user_id  INTEGER NOT NULL,
    group_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, group_id),
    FOREIGN KEY (user_id)
        REFERENCES user (id)
        ON DELETE CASCADE,
    FOREIGN KEY (group_id)
        REFERENCES `group` (id)
        ON DELETE RESTRICT
);
INSERT INTO `group_user` (user_id, group_id)
VALUES (1, 1),
       (1, 2),
       (2, 2);
CREATE TABLE address
(
    id           INTEGER PRIMARY KEY,
    user_id      INTEGER NOT NULL,
    street       TEXT,
    house_number TEXT,
    zip_code     TEXT,
    city         TEXT,
    country      TEXT,
    FOREIGN KEY (user_id)
        REFERENCES user (id)
        ON DELETE CASCADE
);
INSERT INTO address (id, user_id)
VALUES (1, 1);
CREATE TABLE `time_stamps`
(
    id         INTEGER PRIMARY KEY,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME DEFAULT NULL
);
CREATE TABLE `property`
(
    id      INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    name    VARCHAR NOT NULL,
    value   VARCHAR NOT NULL
);
SQL;
        $pdo->exec($sql);

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

    public function propertyInteger(string $class, string $property): Property\Integer
    {
        return new Property\Integer(new ReflectionProperty($class, $property));
    }

    public function propertyDateTime(string $class, $property): Property\DateTime
    {
        return new Property\DateTime(new ReflectionProperty($class, $property));
    }

    public function property(string $class, $property): Property
    {
        return new Property(new ReflectionProperty($class, $property));
    }
}
