<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?php echo 'This is a title'; ?></title>
    </head>
    <body>

        <h1><?php echo 'Title ?> ' . htmlentities($title); ?></h1>
        <p><?php echo htmlentities($description); ?></p>
        <!--This is a HTML comment dude-->

        <p>Test <a href="<?php echo 'lol'; ?>">link</a> <span>span</span>.</p>
    </body>
</html>
