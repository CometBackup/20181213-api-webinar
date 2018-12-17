<?php

require 'vendor/autoload.php';

$c = new \Comet\Server("http://127.0.0.1:9960/", "admin", "admin");

$users = $c->AdminListUsersFull();

?>
<!DOCTYPE html>

<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Number of devices</th>
            <th>Current Storage</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $username => $profile) { ?>
        <tr>
            <td>
                <?php echo htmlentities($username); ?>
            </td>
            <td>
                <?php echo count($profile->Devices); ?> 
            </td>
            <td>
                <?php
                $total_storage_vault_size = 0;
                foreach($profile->Destinations as $storage_vault) {
                    if ($storage_vault->DestinationType == \Comet\Def::DESTINATIONTYPE_LOCALCOPY) {
                        continue;
                    }

                    $total_storage_vault_size += $storage_vault->Statistics->ClientProvidedSize->Size;
                }
                $total_storage_vault_size_gb = ($total_storage_vault_size / (1024*1024*1024));
                echo $total_storage_vault_size_gb . " GB";
                ?>
            </td>
        </tr>
        <?php } ?> 
    </tbody>
</table>
