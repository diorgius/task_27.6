<h2>Новый пользователь</h2>
<div class="div-edituser-form">
    <form class="form-edituser" action="/admin/createuser" method="post">
        <label for="vkid">VK ID</label>
        <input type="text" id="vkid" name="vkid">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login" required>
        <label for="password">Пароль</label>
        <input type="text" id="password" name="password" required>
        <label for="email">Email</label>
        <input type="emaail" id="email" name="email" value="example@example.com" required>
        <label for="role">Роль</label>       
        <select id="role" name="role" required>
            <option selected disabled></option>
            <option value="admin">Администратор</option>
            <option value="user">Пользователь</option>
            <option value="uservk">Пользователь VK</option>
        </select>
        <button class="btn-edituser" type="submit">Записать</button>
    </form>
</div>