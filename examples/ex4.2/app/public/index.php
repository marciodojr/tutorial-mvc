<?php
// app/public/index.php

require '../vendor/autoload.php';

$klein = new \Klein\Klein();

require '../src/routes.php';

$klein->dispatch();