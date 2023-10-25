<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />
<table width="100%">
    <?php
    global $woocommerce, $post;

    // Get the post ID
    $order_id = $post->ID;

    // Then you can get the order object
    $order = new WC_Order($order_id); // This way
    $order = wc_get_order($order_id); // Or this way

    // Just for the sake of it
    $user_order_id = $order->get_id();
    // get user info data
    $get_user_info  = get_post_meta($user_order_id, "_user_info")[0];
    if (isset($get_user_info)) {
    ?>
        <tbody>
            <tr>
                <th> Factuurgegevens Email</th>
                <td><?php echo $get_user_info["_billing_custom_email"]; ?></td>
            </tr>
            <tr>
                <th> Registreer AS</th>
                <td><?php echo $get_user_info["_iscompany"] ?></td>
            </tr>
            <?php
            if (isset($get_user_info["company_name"])) {
            ?>
                <tr>
                    <th> Bedrijfsnaam</th>
                    <td> <?php echo $get_user_info["company_name"] ?></td>
                </tr>
            <?php } ?>
            <tr>
                <th> Voornaam</th>
                <td><?php echo $get_user_info["_first_name"] ?></td>
            </tr>
            <tr>
                <th> Achternaam</th>
                <td><?php echo $get_user_info["_last_name"] ?></td>
            </tr>
            <tr>
                <th> E-mailadres</th>
                <td> <a href="mailto:<?php echo $get_user_info["_email_adress"] ?>" target="_blank"><?php echo $get_user_info["_email_adress"] ?></a></td>
            </tr>
            <tr>
                <th> Telefoonnummer</th>
                <td><?php echo $get_user_info["_phone_number"] ?></td>
            </tr>
            <tr>
                <th>Course details</th>
                <td><?php $order = wc_get_order($order_id);
                    $order_key =  $order->get_order_key();
                    $detail_page_url = home_url() . "/factuurgegevens/bevestiging/order-received/" . $order_id . "/?key=" . $order_key;
                    ?>
                    <a target="_blank" href="<?php echo $detail_page_url ?>"><?php echo $detail_page_url ?></a>
                </td>
            </tr>

        </tbody>
    <?php } ?>
</table>