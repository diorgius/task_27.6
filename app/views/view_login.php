<?php
$csrf_token = hash('gost-crypto', random_int(0, 999999));
$_SESSION['csrf_token'] = $csrf_token;
?>

<div class="div-main-login-container">

    <div class="div-alert">
        <?php if ($data): ?>
            <p><?= $data ?></p>
        <?php endif; ?>
    </div>

    <form class="form-login" action="/login/signup" method="post">
        <input class="input-login" name="login" type="text" placeholder="логин" required>
        <input class="input-login" name="password" type="password" placeholder="пароль" required>
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="div-input-checkbox">
            <input class="input-checkbox" name="remember" type="checkbox">&nbsp&nbspЗапомнить меня
        </div>

        <button class="btn-signup" name="submit" type="submit">Войти</button>
    </form>
</div>

<div id="divwrap"></div> <!-- для вставки формы -->

<div>
    <script nonce="csp_nonce" src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
    <script nonce="csp_nonce" type="text/javascript">
        if ('VKIDSDK' in window) {
            const VKID = window.VKIDSDK;

            VKID.Config.init({
                app: 54495902,
                redirectUrl: 'http://localhost',
                responseMode: VKID.ConfigResponseMode.Callback,
                source: VKID.ConfigSource.LOWCODE,
                scope: 'email', // Заполните нужными доступами по необходимости, без подтверждения профиля бизнеса передаются только базовые данные (без email) 
            });

            const oAuth = new VKID.OAuthList();

            oAuth.render({
                    container: document.currentScript.parentElement,
                    oauthList: [
                        'vkid'
                    ]
                })
                .on(VKID.WidgetEvents.ERROR, vkidOnError)
                .on(VKID.OAuthListInternalEvents.LOGIN_SUCCESS, function(payload) {
                    const code = payload.code;
                    const deviceId = payload.device_id;

                    VKID.Auth.exchangeCode(code, deviceId)
                        .then(vkidOnSuccess)
                        .catch(vkidOnError);
                });

            function vkidOnSuccess(data) {
                // Обработка полученного результата
                // console.log(data);
                VKID.Auth.userInfo(data.access_token).then((result) => { // получаем данные пользователя по access_token

                    uservkid = result.user.user_id
                    useremail = "example@example.com" // если подтвердить vk id бизнес, то его можно получить из vk

                    const form = document.createElement('form');
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "/login/signupvk");
                    const fieldid = document.createElement('input');
                    fieldid.setAttribute("type", "text");
                    fieldid.setAttribute("name", "vkid");
                    fieldid.setAttribute("value", uservkid);
                    fieldid.hidden = true;
                    const fieldemail = document.createElement('input');
                    fieldemail.setAttribute("type", "text");
                    fieldemail.setAttribute("name", "email");
                    fieldemail.setAttribute("value", useremail);
                    fieldemail.hidden = true;
                    const fieldcsrftoken = document.createElement('input');
                    fieldcsrftoken.setAttribute("type", "text");
                    fieldcsrftoken.setAttribute("name", "csrf_token");
                    fieldcsrftoken.setAttribute("value", "<?= $_SESSION['csrf_token'] ?>");
                    fieldcsrftoken.hidden = true;
                    const divwrap = document.querySelector('#divwrap');
                    divwrap.append(form);
                    form.appendChild(fieldid);
                    form.appendChild(fieldemail);
                    form.appendChild(fieldcsrftoken);
                    form.submit()
                })
            }

            function vkidOnError(error) {
                // Обработка ошибки
                console.log(error)
                divalert = document.querySelector('.div-alert');
                divalert.textContent = 'Что-то пошло не так'
            }
        }
    </script>
</div>