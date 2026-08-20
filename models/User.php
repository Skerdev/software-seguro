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
            $sql = 'INSERT INTO ROLES (rol_code, rol_name)
                    VALUES (:rolCode, :rolName)';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(':rolCode', $this->getRolCode());
            $stmt->bindValue(':rolName', $this->getRolName());

            $stmt->execute();

            return true;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF04_CU04 - Consultar Roles
    public function readAll()
    {
        try {
            $rolList = [];

            $sql = 'SELECT rol_code, rol_name
                    FROM ROLES
                    ORDER BY rol_code';

            $stmt = $this->dbh->query($sql);

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rol) {

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
            if (empty($rolCode)) {
                return false;
            }

            $sql = 'SELECT rol_code, rol_name
                    FROM ROLES
                    WHERE rol_code = :rolCode';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':rolCode', $rolCode);
            $stmt->execute();

            $rolDb = $stmt->fetch(PDO::FETCH_ASSOC);

            /*
             * Si el rol no existe, devolvemos false
             * en lugar de intentar acceder a índices
             * inexistentes del resultado.
             */
            if (!$rolDb) {
                return false;
            }

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
            if (empty($this->getRolCode())) {
                return false;
            }

            /*
             * Se mantiene el código como identificador,
             * tal como funciona actualmente el proyecto.
             */
            $sql = 'UPDATE ROLES
                    SET rol_name = :rolName
                    WHERE rol_code = :rolCode';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(':rolName', $this->getRolName());
            $stmt->bindValue(':rolCode', $this->getRolCode());

            $stmt->execute();

            return true;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF07_CU07 - Eliminar Rol
    public function delete($rolCode)
    {
        try {
            if (empty($rolCode)) {
                return false;
            }

            $sql = 'DELETE FROM ROLES
                    WHERE rol_code = :rolCode';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(':rolCode', $rolCode);

            $stmt->execute();

            return true;

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

                $this->rolCode =
                    $data['rolCode'] ??
                    $data['rol_code'] ??
                    null;

                $this->userCode =
                    $data['userCode'] ??
                    $data['user_code'] ??
                    null;

                $this->userName =
                    $data['userName'] ??
                    $data['user_name'] ??
                    null;

                $this->userLastname =
                    $data['userLastname'] ??
                    $data['user_lastname'] ??
                    null;

                $this->userId =
                    $data['userId'] ??
                    $data['user_id'] ??
                    null;

                $this->userEmail =
                    $data['userEmail'] ??
                    $data['user_email'] ??
                    null;

                $this->userPass =
                    $data['userPass'] ??
                    $data['user_pass'] ??
                    null;

                $this->userState =
                    $data['userState'] ??
                    $data['user_state'] ??
                    null;

                $this->rolName =
                    $data['rolName'] ??
                    $data['rol_name'] ??
                    null;
            }

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    /*
     * Excluye $dbh de la serialización porque PDO
     * no debe almacenarse directamente en la sesión.
     */
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
            'rolName'
        ];
    }


    /*
     * Reconstruye la conexión a la base de datos
     * después de unserialize().
     */
    public function __wakeup()
    {
        $this->dbh = DataBase::connection();
    }


    /*
     * Permite asignar propiedades dinámicamente
     * cuando la propiedad existe.
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }


    /*
     * Permite obtener propiedades dinámicamente
     * cuando la propiedad existe.
     */
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

    /*
     * Getter agregado para permitir que el controlador
     * conserve la contraseña existente durante una edición.
     */
    public function getUserPassword()
    {
        return $this->userPass;
    }


    # RF01_CU01 - Iniciar Sesión
    public function login()
    {
        try {

            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        u.user_code,
                        u.user_name,
                        u.user_lastname,
                        u.user_id,
                        u.user_email,
                        u.user_pass,
                        u.user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                        ON r.rol_code = u.rol_code
                    WHERE u.user_email = :userEmail
                    AND u.user_pass = :userPass';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(
                ':userEmail',
                $this->userEmail
            );

            /*
             * Se mantiene SHA-256 para conservar
             * compatibilidad con las contraseñas actuales
             * almacenadas en la base de datos.
             */
            $stmt->bindValue(
                ':userPass',
                hash('sha256', $this->userPass)
            );

            $stmt->execute();

            $userDb = $stmt->fetch(PDO::FETCH_ASSOC);

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

            $sql = 'INSERT INTO USERS (
                        rol_code,
                        user_code,
                        user_name,
                        user_lastname,
                        user_id,
                        user_email,
                        user_pass,
                        user_state
                    ) VALUES (
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

            $stmt->bindValue(
                ':rolCode',
                $this->rolCode
            );

            $stmt->bindValue(
                ':userCode',
                $this->userCode
            );

            $stmt->bindValue(
                ':userName',
                $this->userName
            );

            $stmt->bindValue(
                ':userLastName',
                $this->userLastname
            );

            $stmt->bindValue(
                ':userId',
                $this->userId
            );

            $stmt->bindValue(
                ':userEmail',
                $this->userEmail
            );

            /*
             * Se mantiene SHA-256 para no romper
             * las contraseñas existentes.
             */
            $stmt->bindValue(
                ':userPass',
                hash('sha256', $this->userPass)
            );

            $stmt->bindValue(
                ':userState',
                $this->userState
            );

            $stmt->execute();

            return true;

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
                        u.user_code,
                        u.user_name,
                        u.user_lastname,
                        u.user_id,
                        u.user_email,
                        u.user_pass,
                        u.user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                        ON r.rol_code = u.rol_code
                    ORDER BY u.user_code';

            $stmt = $this->dbh->query($sql);

            foreach (
                $stmt->fetchAll(PDO::FETCH_ASSOC)
                as $user
            ) {

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

            if (empty($userCode)) {
                return false;
            }

            $sql = 'SELECT
                        r.rol_code,
                        r.rol_name,
                        u.user_code,
                        u.user_name,
                        u.user_lastname,
                        u.user_id,
                        u.user_email,
                        u.user_pass,
                        u.user_state
                    FROM ROLES AS r
                    INNER JOIN USERS AS u
                        ON r.rol_code = u.rol_code
                    WHERE u.user_code = :userCode';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(
                ':userCode',
                $userCode
            );

            $stmt->execute();

            $userDb = $stmt->fetch(PDO::FETCH_ASSOC);

            /*
             * Si no existe el usuario devolvemos false
             * para evitar errores al acceder a sus propiedades.
             */
            if (!$userDb) {
                return false;
            }

            return new User($userDb);

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    # RF11_CU11 - Actualizar Usuario
    public function update($originalUserCode = null)
    {
        try {

            /*
             * Si no se proporciona un código original,
             * se utiliza el código actual.
             */
            $originalUserCode =
                $originalUserCode ??
                $this->userCode;

            if (empty($originalUserCode)) {
                return false;
            }

            /*
             * Si userPass ya viene como SHA-256 desde el
             * controlador, se conserva.
             *
             * Si viene como contraseña normal, se cifra
             * con SHA-256.
             */
            $password = $this->userPass;

            if (
                !empty($password) &&
                !preg_match('/^[a-f0-9]{64}$/i', $password)
            ) {
                $password = hash('sha256', $password);
            }

            $sql = 'UPDATE USERS SET
                        rol_code = :rolCode,
                        user_code = :newUserCode,
                        user_name = :userName,
                        user_lastname = :userLastName,
                        user_id = :userId,
                        user_email = :userEmail,
                        user_pass = :userPass,
                        user_state = :userState
                    WHERE user_code = :originalUserCode';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(
                ':rolCode',
                $this->rolCode
            );

            $stmt->bindValue(
                ':newUserCode',
                $this->userCode
            );

            $stmt->bindValue(
                ':userName',
                $this->userName
            );

            $stmt->bindValue(
                ':userLastName',
                $this->userLastname
            );

            $stmt->bindValue(
                ':userId',
                $this->userId
            );

            $stmt->bindValue(
                ':userEmail',
                $this->userEmail
            );

            $stmt->bindValue(
                ':userPass',
                $password
            );

            $stmt->bindValue(
                ':userState',
                $this->userState
            );

            $stmt->bindValue(
                ':originalUserCode',
                $originalUserCode
            );

            $stmt->execute();

            return true;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    # RF12_CU12 - Eliminar Usuario
    public function delete($userCode)
    {
        try {

            if (empty($userCode)) {
                return false;
            }

            $sql = 'DELETE FROM USERS
                    WHERE user_code = :userCode';

            $stmt = $this->dbh->prepare($sql);

            $stmt->bindValue(
                ':userCode',
                $userCode
            );

            $stmt->execute();

            return true;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}

