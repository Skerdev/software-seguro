<?php

class Rol
{
    private $dbh;
    private $rolCode;
    private $rolName;

    public function __construct(array $data = [])
    {
        try {
            $this->dbh = DataBase::connection();
            if (!empty($data)) {
                $this->rolCode = $data['rolCode'] ?? $data['rol_code'] ?? null;
                $this->rolName = $data['rolName'] ?? $data['rol_name'] ?? null;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function setRolCode($rolCode)
    {
        $this->rolCode = $rolCode;
    }

    public function getRolCode()
    {
        return $this->rolCode;
    }

    public function setRolName($rolName)
    {
        $this->rolName = $rolName;
    }

    public function getRolName()
    {
        return $this->rolName;
    }

    # RF03_CU03 - Registrar Rol
    public function create()
    {
        try {
            $sql = 'INSERT INTO ROLES VALUES (:rolCode, :rolName)';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF04_CU04 - Consultar Roles
    public function readAll()
    {
        try {
            $rolList = [];
            $sql = 'SELECT * FROM ROLES';
            $stmt = $this->dbh->query($sql);
            foreach ($stmt->fetchAll() as $rol) {
                $rolObj = new Rol();
                $rolObj->setRolCode($rol['rol_code']);
                $rolObj->setRolName($rol['rol_name']);
                $rolList[] = $rolObj;
            }
            return $rolList;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF05_CU05 - Obtener el Rol por el código
    public function getByCode($rolCode)
    {
        try {
            $sql = 'SELECT * FROM ROLES WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
            $rolDb = $stmt->fetch();
            
            $rol = new Rol();
            $rol->setRolCode($rolDb['rol_code']);
            $rol->setRolName($rolDb['rol_name']);
            return $rol;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF06_CU06 - Actualizar Rol
    public function update()
    {
        try {
            $sql = 'UPDATE ROLES SET
                        rol_code = :rolCode,
                        rol_name = :rolName
                    WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF07_CU07 - Eliminar Rol
    public function delete($rolCode)
    {
        try {
            $sql = 'DELETE FROM ROLES WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}

class User
{
    private $dbh;
    private $rolCode;
    private $userCode;
    private $userName;
    private $userLastname;
    private $userId;
    private $userEmail;
    private $userPass;
    private $userState;
    private $rolName;

    public function __construct(array $data = [])
    {
        try {
            $this->dbh = DataBase::connection();
            if (!empty($data)) {
                $this->rolCode = $data['rolCode'] ?? $data['rol_code'] ?? null;
                $this->userCode = $data['userCode'] ?? $data['user_code'] ?? null;
                $this->userName = $data['userName'] ?? $data['user_name'] ?? null;
                $this->userLastname = $data['userLastname'] ?? $data['user_lastname'] ?? null;
                $this->userId = $data['userId'] ?? $data['user_id'] ?? null;
                $this->userEmail = $data['userEmail'] ?? $data['user_email'] ?? null;
                $this->userPass = $data['userPass'] ?? $data['user_pass'] ?? null;
                $this->userState = $data['userState'] ?? $data['user_state'] ?? null;
                $this->rolName = $data['rolName'] ?? $data['rol_name'] ?? null;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    // Generic property accessors to reduce method count
// Excluye $dbh (PDO) de la serialización: PDO no es serializable
    public function __sleep()
    {
        return [
            'rolCode',
            'userCode',
            'userName',
            'userLastname',
            'userId',
            'userEmail',
            'userPass',
            'userState',
            'rolName',
        ];
    }

        // Reconstruye la conexión al deserializar (p. ej. al leer $_SESSION['profile'])
        public function __wakeup()
        {
            $this->dbh = DataBase::connection();
        }

        // Generic property accessors to reduce method count
        public function __set($name, $value)
        {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
    public function getUserState()
    {
        return $this->userState;
    }
    public function getRolName()
    {
        return $this->rolName;
    }
    public function getRolCode()
    {
        return $this->rolCode;
    }
    public function getUserCode()
    {
        return $this->userCode;
    }
    public function getUserName()
    {
        return $this->userName;
    }
    public function getUserLastName()
    {
        return $this->userLastname;
    }
    public function getUserId()
    {
        return $this->userId;
    }
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    // Setters requeridos por tests/TestCase.php (createTestUser, loginAs)
    public function setRolCode($rolCode)
    {
        $this->rolCode = $rolCode;
    }
    public function setRolName($rolName)
    {
        $this->rolName = $rolName;
    }
    public function setUserCode($userCode)
    {
        $this->userCode = $userCode;
    }
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }
    public function setUserLastName($userLastname)
    {
        $this->userLastname = $userLastname;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }
    public function setUserPass($userPass)
    {
        $this->userPass = $userPass;
    }
    public function setUserState($userState)
    {
        $this->userState = $userState;
    }

    # RF01_CU01 - Iniciar Sesión
    public function login()
    {
        try {
            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                    ON r.rol_code = u.rol_code
                    WHERE user_email = :userEmail AND user_pass = :userPass';
            
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userEmail', $this->userEmail);
            $stmt->bindValue('userPass', hash('sha256', $this->userPass));
            $stmt->execute();
            
            $userDb = $stmt->fetch();
            if ($userDb) {
                return new User($userDb);
            }
            return false;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF08_CU08 - Registrar Usuario
    public function create()
    {
        try {
            $sql = 'INSERT INTO USERS VALUES (
                        :rolCode,
                        :userCode,
                        :userName,
                        :userLastName,
                        :userId,
                        :userEmail,
                        :userPass,
                        :userState
                    )';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->rolCode);
            $stmt->bindValue('userCode', $this->userCode);
            $stmt->bindValue('userName', $this->userName);
            $stmt->bindValue('userLastName', $this->userLastname);
            $stmt->bindValue('userId', $this->userId);
            $stmt->bindValue('userEmail', $this->userEmail);
            $stmt->bindValue('userPass', hash('sha256', $this->userPass));
            $stmt->bindValue('userState', $this->userState);
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF09_CU09 - Consultar Usuarios
    public function readAll()
    {
        try {
            $userList = [];
            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                    ON r.rol_code = u.rol_code';
            $stmt = $this->dbh->query($sql);
            foreach ($stmt->fetchAll() as $user) {
                $userList[] = new User($user);
            }
            return $userList;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF10_CU10 - Obtener el Usuario por el código
    public function getByCode($userCode)
    {
        try {
            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                    ON r.rol_code = u.rol_code
                    WHERE user_code = :userCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userCode', $userCode);
            $stmt->execute();
            $userDb = $stmt->fetch();
            
            return new User($userDb);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF11_CU11 - Actualizar usuario
    public function update()
    {
        try {
            $sql = 'UPDATE USERS SET
                        rol_code = :rolCode,
                        user_code = :userCode,
                        user_name = :userName,
                        user_lastname = :userLastName,
                        user_id = :userId,
                        user_email = :userEmail,
                        user_pass = :userPass,
                        user_state = :userState
                    WHERE user_code = :userCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->rolCode);
            $stmt->bindValue('userCode', $this->userCode);
            $stmt->bindValue('userName', $this->userName);
            $stmt->bindValue('userLastName', $this->userLastname);
            $stmt->bindValue('userId', $this->userId);
            $stmt->bindValue('userEmail', $this->userEmail);
            $stmt->bindValue('userPass', hash('sha256', $this->userPass));
            $stmt->bindValue('userState', $this->userState);
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF12_CU12 - Eliminar Usuario
    public function delete($userCode)
    {
        try {
            $sql = 'DELETE FROM USERS WHERE user_code = :userCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('userCode', $userCode);
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}

