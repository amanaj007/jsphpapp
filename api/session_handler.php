<?php
require_once __DIR__ . '/db_pdo.php';

class DBSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function open($path, $name): bool {
        $this->pdo = getPDO();
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS php_sessions (
            session_id VARCHAR(128) NOT NULL,
            data TEXT NOT NULL,
            expires DATETIME NOT NULL,
            PRIMARY KEY (session_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string|false {
        $stmt = $this->pdo->prepare('SELECT data FROM php_sessions WHERE session_id = :id AND expires > NOW()');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['data'] : '';
    }

    public function write($id, $data): bool {
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $stmt = $this->pdo->prepare('
            INSERT INTO php_sessions (session_id, data, expires)
            VALUES (:id, :data, :expires)
            ON DUPLICATE KEY UPDATE data = :data2, expires = :expires2
        ');
        $stmt->execute([
            ':id'      => $id,
            ':data'    => $data,
            ':expires' => $expires,
            ':data2'   => $data,
            ':expires2'=> $expires,
        ]);
        return true;
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM php_sessions WHERE session_id = :id');
        $stmt->execute([':id' => $id]);
        return true;
    }

    public function gc($max_lifetime): int|false {
        $stmt = $this->pdo->prepare('DELETE FROM php_sessions WHERE expires < NOW()');
        $stmt->execute();
        return $stmt->rowCount();
    }
}

function start_db_session() {
    $handler = new DBSessionHandler();
    session_set_save_handler($handler, true);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
