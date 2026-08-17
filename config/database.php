<?php
class Database
{
    private string $host = 'db';
    private string $database = 'workmatch';
    private string $username = 'root';
    private string $password = 'root';

    public function connect(): PDO
    {
        try {
            $connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password
            );

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $connection;
        } catch (PDOException $exception) {
            throw new RuntimeException('No fue posible conectar con la base de datos.');
        }
    }
}
