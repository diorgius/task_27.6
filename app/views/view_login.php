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

<!-- Авторизация через VK ID 
    Для интеграции авторизации через VK ID, зарегистрировался на VK ID бизнес, регистрировался как самозянятый (был вариант как ИП)
    создал и настроил приложение для тестирования (домен: localhost, редирект: http://localhost)
    но при подтверждении профиля через госуслуги возникает ошибка 
    "Организация ликвидирована или в процессе ликвидации При подключении возникла ошибка. Попробуйте авторизоваться позже."
    без подтверждения профиля бизнеса передаются только базовые данные (без email) и не могу получить ключи доступа, чтобы использовать VK SDK PHP
    поэтому использую вариант авторизации с VK ID low-code.
    Если получить реальный email, то по нему можно проводить и регистрацию и авторизацию (или только авторизацию, 
    а регистрацию по форме, чтобы были полные данные), но у меня не подтверждается профиль, а для тестирования сделаю так:
    при регистрации через VK ID будем использовать VK user_id и email (т.к. не могу получить реальный email, 
    буду использовать example@example.com) VK user_id записывваем в базу и потом по нему будем авторизовать пользователя, 
    а из email извлечем название ящика и будем его записывать как login и роль автоматом назначать uservk.
    как в этом случае назначать пароль не понимаю, если пользователь захочет зайти не черех VK ID, а через логин/пароль.
    Или вообще не регистрировать пользователя через VK ID, а дать возможность только входа с VK ID c проверкой аккаунта по email.

    Еще бы надо сделать:
    - При регистрации или авторизации по VK ID делать проверку по email и если есть совпадение, то делать update учетной записи с добавлением VD ID и не регать нового пользователя.
    - При регистрации через форму проверять не только по логину, а еще по email, возможно пользователь уже регистрировался по VK ID и делать update этой записи 
-->

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

                    // console.log(result)
                    // user = JSON.stringify(result)
                    // или
                    // uservk = {
                    //     "userid": result.user.user_id,
                    //     "firstname": result.user.first_name,
                    //     "lastname": result.user.last_name,
                    //     "email": "example@example.com" // если подтвердить vk id бизнес, то его можно получить из vk
                    // }
                    // console.log(uservk)
                    
                    uservkid = result.user.user_id
                    useremail = "example@example.com" // если подтвердить vk id бизнес, то его можно получить из vk
                    // console.log(uservkid)

                    // передаем в php метод данные для регистрации пользователя 

                    // извращенный вариат 1 - передача данных в адресной строке
                    // передача парамметров таким образом лютая хрень по безопасности
                    // window.location.href = '/registration/signupvk/' + uservkid + '/' + useremail

                    // извращенный вариат 2 - генерируем форму передаем данные и csrf токен
                    const form = document.createElement('form');
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "/login/signupvk");
                    const fieldid = document.createElement('input');
                    fieldid.setAttribute("type", "text");
                    fieldid.setAttribute("name", "vkid");
                    fieldid.setAttribute("value", uservkid);
                    fieldid.hidden = true;
                    // const fieldemail = document.createElement('input');
                    // fieldemail.setAttribute("type", "text");
                    // fieldemail.setAttribute("name", "email");
                    // fieldemail.setAttribute("value", useremail);
                    // fieldemail.hidden = true;
                    const fieldcsrftoken = document.createElement('input');
                    fieldcsrftoken.setAttribute("type", "text");
                    fieldcsrftoken.setAttribute("name", "csrf_token");
                    fieldcsrftoken.setAttribute("value", "<?= $_SESSION['csrf_token'] ?>");
                    fieldcsrftoken.hidden = true;
                    const divwrap = document.querySelector('#divwrap');
                    divwrap.append(form);
                    form.appendChild(fieldid);
                    // form.appendChild(fieldemail);
                    form.appendChild(fieldcsrftoken);
                    form.submit()


                    // я думаю был бы нормальный вариант с передачей данных в обработчик или сразу в метод, 
                    // данные передаются, но я не понимаю как мне перейти в (запустить) контроллер и метод

                    // let params = {
                    //     "method": "POST",
                    //     "headers": {"Content-Type": "application/json; charset=utf-8"},
                    //     "body": JSON.stringify(uservk)
                    // }
                    // fetch("/core/hendler.php", params) // данные передаются
                    // fetch("/registration/signupvk", params) // данные передаются

                    // или

                    // $.ajax({
                    //     url: '/core/hendler.php',     
                    //     type: 'post',
                    //     datatype: 'json',                   
                    //     data: {data: uservk},
                    //     success: function(data) // так тоже данные передаются
                    //     {
                    //         // alert(data)
                    //     }
                    // });

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