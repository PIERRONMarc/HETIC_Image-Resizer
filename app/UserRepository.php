<?php

class UserRepository {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function insert(Array $input)
    {
        try {
            $sql = 'INSERT INTO user (Username, Password, Email, Created_at) VALUES (:username, :password, :email, :createdAt)' ;

            $query = $this->db->getConnection()->prepare($sql);
            $today = new DateTime();
            $result = $query->execute([
                'username' => $input['username'],
                'password' => password_hash($input['password'], PASSWORD_ARGON2I),
                'email' => $input['email'],
                'createdAt' => $today->format('Y-m-d H:i:s'),
            ]);

            return $this->db->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        return $result;
    }
}