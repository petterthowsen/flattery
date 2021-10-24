<?php if (session()->has('flash.messages')): ?>
    <?php $messages = session()->remove('flash.messages'); ?>
    <?php
    dump($messages);
    ?>
    <div id="messages">
        <ul>
            <?php foreach($messages as $message): ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>