<?php

/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!$notices) {
    return;
}

$notices_groups = [];

foreach ($notices as $k => $notice) {
    $error = $notice["notice"];
    // echo "<pre>";
    // print_r($notice["notice"]);
    // echo "</pre>";
    $id = !empty($notice["data"]["id"]) ? $notice["data"]["id"] : "";

    if (!empty(COURSE_ERROR_MESSAGES_REPLACES[$error])) {
        $notice["notice"] = COURSE_ERROR_MESSAGES_REPLACES[$error];
    }
    if (strpos($error, "Contactpersoon") !== false) {
        $notices_groups["contact"][] = $notice;
    } elseif (strpos($error, "Bedrijfsnaam") !== false) {
        $notices_groups["company"][] = $notice;
    } else {
        if (!empty($id)) {
            $notices_groups["others"][$id] = $notice;
        } else {
            $notices_groups["others"][] = $notice;
        }
    }
}
$other_validations = isset($notices_groups["others"]) ? $notices_groups["others"] : [];
$notices_groups["others"] = [];
$notices_groups["billing"] = [];

$billing_orders = [
    "billing_first_name" => 0,
    "billing_last_name" => 1,
    "billing_street" => 2,
    "billing_postcode" => 3,
    "billing_city" => 4,
    "billing_custom_email" => 5,
    "billing_phone" => 6,

];

foreach ($other_validations as $notice) {
    $id = !empty($notice["data"]["id"]) ? $notice["data"]["id"] : "";
    $error = $notice["notice"];
    if (isset($billing_orders[$id])) {
        $order = (int) $billing_orders[$id];
        $notices_groups["billing"][$order] = $notice;
    } else {
        if (strpos($error, "Selecteer bank") !== false) {
            $notices_groups["bank"][$id] = $notice;
        } else {
            $notices_groups["others"][] = $notice;
        }
    }
}
// echo "<pre>";
// print_r($notices_groups);
// echo "</pre>";
?>
<ul class="woocommerce-error octacoded-error" role="alert">
    <?php
    // print company name top on errors
    if (!empty($notices_groups["company"])) {

        foreach ($notices_groups["company"] as $notice) :

    ?>
            <li<?php echo wc_get_notice_data_attr($notice); ?>>
                <?php echo wc_kses_notice($notice['notice']); ?>
                </li>
            <?php
        endforeach;
    }



    if (!empty($notices_groups["contact"])) {
        foreach ($notices_groups["contact"] as $notice) : ?>

                <li<?php echo wc_get_notice_data_attr($notice); ?>>
                    <?php echo wc_kses_notice($notice['notice']); ?>
                    </li>
            <?php endforeach;
    }
            ?>


            <?php
            if (!empty($notices_groups["billing"])) {
                ksort($notices_groups["billing"]);

                foreach ($notices_groups["billing"] as $notice) :
            ?>
                    <li<?php echo wc_get_notice_data_attr($notice); ?>>
                        <?php echo wc_kses_notice($notice['notice']); ?>
                        </li>
                <?php
                endforeach;
            }
                ?>
                <?php
                if (!empty($notices_groups["bank"])) {

                    foreach ($notices_groups["bank"] as $notice) :
                ?>
                        <li<?php echo wc_get_notice_data_attr($notice); ?>>
                            <?php echo wc_kses_notice($notice['notice']); ?>
                            </li>
                    <?php
                    endforeach;
                }
                    ?>
                    <?php
                    if (!empty($notices_groups["others"])) {

                        foreach ($notices_groups["others"] as $notice) :
                    ?>
                            <li<?php echo wc_get_notice_data_attr($notice); ?>>
                                <?php echo wc_kses_notice($notice['notice']); ?>
                                </li>
                        <?php
                        endforeach;
                    }
                        ?>
</ul>