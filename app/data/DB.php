<?php

namespace App\data;
use PDO;
use PDOException;

class DB
{
    protected static $pdo;
    public static function dbconnect(): void
    {
        try {
            self::$pdo = new PDO('sqlite:' . DATA . 'db.sqlite');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql =
                "create table if not exists users (
                id integer primary key autoincrement,
                vkid integer(10) null,
                login varchar(64) null,
                password varchar(128) null,
                email varchar(64) null,
                role varchar(32) null,
                cookiehash varchar(128) null,
                created datetime default current_timestamp)";

            self::$pdo->exec($sql);

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public static function getAll(string $table)
    {
        $stmt = self::$pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByProp(string $table, string $prop, string $value)
    {
        $stmt = self::$pdo->prepare("SELECT * FROM $table WHERE $prop = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create(string $table, array $values)
    {
        $colums = implode(', ', array_keys($values));
        $placeholders = ':' . implode(', :', array_keys($values));
        $stmt = self::$pdo->prepare("INSERT INTO $table ($colums) VALUES ($placeholders)");
        $stmt->execute($values);
        return self::$pdo->lastInsertId();
    }

    public static function update(string $table, array $values)
    {
        $id = $values['id'];
        unset($values['id']);
        $set = '';
        foreach ($values as $key => $value) {
            $set .= "$key = :$key, ";
        }
        $set = rtrim($set, ', ');
        $stmt = self::$pdo->prepare("UPDATE $table SET $set WHERE id = :id");
        $values['id'] = $id;
        $stmt->execute($values);
        return $id;
    }

    public static function delete(string $table, string $id): void
    {
        $stmt = self::$pdo->prepare("DELETE FROM $table WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}