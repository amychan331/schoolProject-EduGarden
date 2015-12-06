<?php
    require('session.class.php');
    $session = new session();
    $session->start_session('_s', false);

    $_SESSION['something'] = 'A value.';
    echo $_SESSION['something'];
?>