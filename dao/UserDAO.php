<?php

require_once("models/User.php");
require_once("models/Message.php");

class UserDAO implements IUser
{
    private $conn;
    private $url;
    private $message;

    public function __construct($conn, $url)
    {
        $this->conn = $conn;
        $this->url = $url;
        $this->message = new Message($url);
    }

    public function buildUser($data): User
    {
        $user = new User();

        $user->id = $data["id"];
        $user->name = $data["name"];
        $user->lastname = $data["lastname"];
        $user->email = $data["email"];
        $user->password = $data["password"];
        $user->image = $data["image"];
        $user->bio = $data["bio"];
        $user->token = $data["token"];

        return $user;
    }

    public function findByEmail($email)
    {
        if ($email != "") {
            $query = "SELECT * FROM users WHERE email=:email";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":email", $email);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetch();
                $user = $this->buildUser($data);

                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function create(User $user, $authUser = false)
    {
        $query = "INSERT INTO users (name, lastname, email, password, token) VALUES (:name, :lastname, :email, :password, :token)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $user->name);
        $stmt->bindParam(":lastname", $user->lastname);
        $stmt->bindParam(":email", $user->email);
        $stmt->bindParam(":password", $user->password);
        $stmt->bindParam(":token", $user->token);

        $stmt->execute();

        if ($authUser) {
            $this->setTokenToSession($user->token);
        }
    }

    public function update(User $user, $redirect = true)
    {
        $query = "UPDATE users SET
            name = :name,
            lastname = :lastname,
            email = :email,
            image = :image,
            bio = :bio,
            token = :token
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $user->name);
        $stmt->bindParam(":lastname", $user->lastname);
        $stmt->bindParam(":email", $user->email);
        $stmt->bindParam(":image", $user->image);
        $stmt->bindParam(":bio", $user->bio);
        $stmt->bindParam(":token", $user->token);
        $stmt->bindParam(":id", $user->id);

        $stmt->execute();

        if ($redirect) {
            $this->message->setMessage("Dados atualizados com sucesso", "success", "editprofile.php");
        }
    }

    public function verifyToken($protected = false)
    {
        if (!empty($_SESSION["token"])) {

            $token = $_SESSION["token"];

            $user = $this->findByToken($token);

            if ($user) {
                return $user;
            } else if ($protected) {
                $this->message->setMessage("Faça login para continuar !", "error", "index.php");
            }
        } else if ($protected) {
            $this->message->setMessage("Faça login para continuar !", "error", "index.php");
        }
    }

    public function setTokenToSession($token, $redirect = true)
    {
        $_SESSION["token"] = $token;

        if ($redirect) {
            $this->message->setMessage("Seja bem vindo", "success", "editprofile.php");
        }
    }

    public function authenticateUser($email, $password)
    {
        $user = $this->findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                $token = $user->generateToken();

                $this->setTokenToSession($token, false);

                $user->token = $token;

                $this->update($user, false);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function findById($id)
    {
        if ($id != "") {
            $query = "SELECT * FROM users WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $id);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetch();

                $user = $this->buildUser($data);

                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function findByToken($token)
    {
        if ($token != "") {
            $query = "SELECT * FROM users WHERE token=:token";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":token", $token);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $data = $stmt->fetch();
                $user = $this->buildUser($data);

                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function changePassword(User $user)
    {

        $query = "UPDATE users SET
            password = :password
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":password", $user->password);
        $stmt->bindParam(":id", $user->id);

        $stmt->execute();

        $this->message->setMessage("Senha alterada com sucesso !", "success", "editprofile.php");
    }

    public function destroyToken()
    {
        $_SESSION["token"] = "";

        $this->message->setMessage("logout feito com sucesso", "success", "index.php");
    }
}
