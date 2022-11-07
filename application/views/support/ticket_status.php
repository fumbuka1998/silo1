<?php
$color = '';
if ($ticket->status != 'Active') {
    $color = 'a8a8a8';
}else{
    $color = '00CC00';
}

?>

<a href="#" style="color: <?= '#'.$color ?>;" class="status" ><?= $ticket->status ?></a>

