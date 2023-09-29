<?php

//  ATTENTION!
//
//  DO NOT MODIFY THIS FILE BECAUSE IT WAS GENERATED AUTOMATICALLY,
//  SO ALL YOUR CHANGES WILL BE LOST THE NEXT TIME THE FILE IS GENERATED.
//  IF YOU REQUIRE TO APPLY CUSTOM MODIFICATIONS, PERFORM THEM IN THE FOLLOWING FILE:
//  /home/nexusbond/public_html/camdenbeautyspa/wp-content/maintenance/template.phtml


$protocol = $_SERVER['SERVER_PROTOCOL'];
if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
    $protocol = 'HTTP/1.0';
}

header("{$protocol} 503 Service Unavailable", true, 503);
header('Content-Type: text/html; charset=utf-8');
header('Retry-After: 600');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="icon" href="https://camdenbeautyspa.co.uk/wp-content/uploads/2021/10/crown-fav-2.png">
    <link rel="stylesheet" href="https://camdenbeautyspa.co.uk/wp-content/maintenance/assets/styles.css">
    <script src="https://camdenbeautyspa.co.uk/wp-content/maintenance/assets/timer.js"></script>
    <title>Scheduled Maintenance</title>
</head>

<body>

    <div class="container">

    <header class="header">
        <h1>The website is undergoing scheduled maintenance.</h1>
        <h2>Sorry for the inconvenience. Come back a bit later, we will be ready soon!</h2>
    </header>

    <!--START_TIMER_BLOCK-->
        <!--END_TIMER_BLOCK-->

    <!--START_SOCIAL_LINKS_BLOCK-->
    <section class="social-links">
            </section>
    <!--END_SOCIAL_LINKS_BLOCK-->

</div>

<footer class="footer">
    <div class="footer__content">
        Powered by WP Toolkit    </div>
</footer>

</body>
</html>
