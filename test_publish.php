<?php
require __DIR__ . "/lib/db.php";
require __DIR__ . "/lib/save_event.php";

save_event("test/topic", "TEST LOG FROM PHP", "system");
