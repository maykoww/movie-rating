<?php
require_once("templates/header.php");
require_once("models/User.php");
require_once("dao/UserDAO.php");

$user = new User();

$userDao = new UserDAO($conn, $BASE_URL);

$userData = $userDao->verifyToken(true);

$fullName = $user->getFullName($userData);

if ($userData->image == "") {
    $userData->image = "user.png";
}

?>

<div id="main-container" class="container-fluid">
    <div class="col-md-12">
        <form action="<?= $BASE_URL ?>user_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="type" value="update">
            <div class="row">
                <div class="col-md-4">
                    <h1><?= $fullName ?></h1>
                    <p class="page-description">Altere seus dados no formulário abaixo: </p>
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Digite seu nome" value="<?= $userData->name ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Sobrenome</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Digite seu sobrenome" value="<?= $userData->lastname ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="text" class="form-control disabled" id="email" name="email" placeholder="Digite seu e-mail" value="<?= $userData->email ?>" readonly>
                    </div>
                    <input type="submit" class="btn card-btn" value="Enviar">
                </div>
                <div class="col-md-4">
                    <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>images/users/<?= $userData->image ?>');"></div>
                    <div class="form-group">
                        <label for="image">Foto: </label>
                        <input type="file" class="form-control-file" name="image">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea class="form-control" name="bio" id="bio" rows="5" placeholder="Conte sobre você"><?= $userData->bio ?></textarea>
                    </div>
                </div>
            </div>
        </form>
        <div class="row" id="change-password-container">
            <div class="col-md-4">
                <h2>Alterar Senha: </h2>
                <p class="page-description">Digite a nova senha e confirme, para alterar a senha: </p>
                <form action="<?= $BASE_URL ?>user-process.php" method="POST">
                    <input type="hidden" name="type" value="changepassword">
                    <input type="hidden" name="id" value="<?= $userData->id ?>">
                    <div class="form-group">
                        <label for="password">Senha: </label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Digite sua nova senha">
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Repita a Senha: </label>
                        <input id="confirmpassword" name="confirmpassword" type="password" class="form-control" placeholder="Digite sua senha novamente">
                    </div>
                    <input type="submit" value="Alterar Senha" class="btn card-btn">
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once("templates/footer.php");
?>