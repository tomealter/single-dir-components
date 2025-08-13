<?php
// Run drush deploy to update database and import config with proper
// cache clearing.
echo "Triggering drush deploy...\n";
passthru('drush deploy');
echo "Drush deploy complete.\n";
