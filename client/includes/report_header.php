<div style="font-size:12px; font-family: 'Helvetica'; display: flex; align-items: center; gap: 10px; text-align: left;">

    <!-- Image Section -->
    <div style="flex-shrink: 0;">
        <img
            src="<?php echo is_null($user[0]['blogo']) ? 'icons/favicon.png' : $user[0]['blogo']; ?>"
            style="height: 100%; max-height: 150px;"
            onerror="this.onerror=null; this.src='icons/favicon.png';"
            alt="Bank Logo">
    </div>

    <!-- Text Section -->
    <div>
        <b style="font-size:15px; display: block;">
            <?= is_null($user[0]['bankName']) ? '' : strtoupper($user[0]['bankName']); ?>
        </b>
        <div style="font-weight:bold; font-size:13px;">
            Location: <?php echo is_null($user[0]['blocation']) ? '' : $user[0]['blocation']; ?><br>
            Tel: <?php echo is_null($user[0]['bcontacts']) ? '' : $user[0]['bcontacts']; ?><br>
            Email: <?php echo is_null($user[0]['bemail']) ? '' : $user[0]['bemail']; ?><br>
        </div>
    </div>

</div>
<hr>