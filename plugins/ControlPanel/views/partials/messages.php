<?php if (session()->has('flash')): ?>
    <?php $messages = session()->remove('flash'); ?>
    <div id="flash-messages">
        <ul>
            <?php foreach($messages as $message): ?>
                <li><?=$message?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>