<?php if (!$auth): ?>
    <h2>Пожалуйста авторизуйтесь</h2>
<?php else: ?>
    <h2>Добро пожаловать <?= $login ?></h2>
    <?php if ($role === 'admin'): ?>
        <button class="btn-admin-action" onclick="location.href='/admin'">Страница администрирования</button>
    <?php endif; ?>
    <div class="div-text">
        <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. 
            Dicta libero reprehenderit pariatur veritatis cum dignissimos sequi enim 
            voluptatem repellendus eius. Quasi porro veniam blanditiis inventore odio 
            cum ad provident recusandae nam saepe! Hic debitis quos delectus odio quas 
            provident tempora, porro perferendis voluptatibus quae eius, animi consequatur 
            voluptatem temporibus eligendi officiis, placeat ad facilis rerum impedit culpa. 
            Perferendis ut at consectetur alias illo nam, perspiciatis neque dolorem odit reiciendis et ad? 
            Suscipit quia animi vitae, nisi perspiciatis delectus ea qui ex vero, 
            officia dignissimos repudiandae corporis quae saepe ducimus. 
            Nulla unde natus sequi harum laboriosam qui ipsa incidunt eaque vero.
        </p>
    </div>
    <?php if ($role === 'admin' || $role === 'uservk'): ?>
        <div class="div-image">
            <img class="img-image" src="/img/image.jpg" alt="image">
        </div>
    <?php endif; ?>   
<?php endif; ?>   
