<?php

namespace App;

use React\MySQL\ConnectionInterface;
use React\MySQL\Exception;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

final class Users
{
    private ConnectionInterface $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function all(): PromiseInterface
    {
        return $this->db
            ->query('SELECT id, name, email FROM users ORDER BY id')
            ->then(function (QueryResult $command) {
                echo 'Successfully get all users' . PHP_EOL;
                return $command->resultRows;
            }, function (Exception $error) {
                    echo 'Error ' . PHP_EOL;
            });
    }

    public function find(string $id): PromiseInterface
    {
        return $this->db->query('SELECT id, name, email FROM users WHERE id = ? LIMIT 1', [$id])
            ->then(
                function (QueryResult $result) {
                    if (empty($result->resultRows)) {
                        throw new UserNotFoundError();
                    }

                    return $result->resultRows[0];
                }
            );
    }

    public function update(string $id, string $newName): PromiseInterface
    {
        return $this->find($id)
            ->then(
                function () use ($id, $newName) {
                    $this->db->query('UPDATE users SET name = ? WHERE id = ?', [$newName, $id]);
                }
            );
    }

    public function delete(string $id): PromiseInterface
    {
        return $this->db
            ->query('DELETE FROM users WHERE id = ?', [$id])
            ->then(
                function (QueryResult $result) {
                    if ($result->affectedRows === 0) {
                        throw new UserNotFoundError();
                    }
                }
            );
    }

    public function create(string $name, string $email): PromiseInterface
    {
        return $this->db->query('INSERT INTO users(name, email) VALUES (?, ?)', [$name, $email])
            ->then(function (QueryResult $command) {
                echo 'Successfully add user' . PHP_EOL;
            }, function (Exception $error) {
                echo 'Error ' . PHP_EOL;
            });
    }
}
