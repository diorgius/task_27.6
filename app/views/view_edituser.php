<?php if(!empty($data)): ?>
<h2>Пользователь: <?= $data['login']?></h2>
<div class="div-edituser-form">
    <form class="form-edituser" action="/admin/updateuser" method="post">
        <label for="id">ID</label>
        <input type="text" id="id" name="id" value="<?= $data['id'] ?>" readonly>
        <label for="vkid">VK ID</label>
        <input type="text" id="vkid" name="vkid" value="<?= $data['vkid'] ?>">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login" value="<?= $data['login'] ?>">
        <label for="password">Пароль</label>
        <input type="text" id="password" name="password" value="<?= $data['password'] ?>">
        <label for="email">Email</label>
        <input type="emaail" id="email" name="email" value="<?= $data['email'] ?>">
        <label for="role">Роль</label>       
        <select id="role" name="role">
            <option value="<?= $data['role'] ?>"><?= $data['role'] ?></option>
            <option value="admin">Администратор</option>
            <option value="user">Пользователь</option>
        </select>
        <button class="btn-edituser" type="submit">Записать</button>
    </form>
    <button class="btn-edituser" type="submit" onclick="location.href='/admin/deleteuser/<?= $data['id'] ?>'">Удалить пользователя</button>
</div>
<?php endif; ?>