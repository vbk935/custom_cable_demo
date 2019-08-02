<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_Dependencies {

    private $missing = array();
    private $wc_version_check = "2.3.0";

    public function check() {
        $enable_wcpv = true;
        global $woocommerce;
        if (!class_exists('WooCommerce')) {
            $this->missing['WooCommerce'] = 'http://www.woothemes.com/woocommerce/';
            $enable_wcpv = false;
        } elseif (( defined('WC_VERSION') && version_compare(WC_VERSION, $this->wc_version_check, '<') ) || ( isset($woocommerce->version) && version_compare($woocommerce->version, $this->wc_version_check, '<') )) {
            add_action('admin_notices', array($this, '_old_wc_warning'));
            $enable_wcpv = false;
        }

        if ($this->missing) {
            add_action('admin_notices', array($this, '_missing_plugins_warning'));
        }
        return $enable_wcpv;
    }

    public function _old_wc_warning() {
        ?>
        <div class="message error"><p><?php printf(__('WooCommerce Products Visibility is enabled but not effective. It is not compatible with  <a href="%s" target="_blank">Woocommerce</a> versions prior %s.', 'woocommerce-products-visibility'), 'http://www.woothemes.com/woocommerce/', $this->wc_version_check);
        ?></p></div>
        <?php
    }

    public function _missing_plugins_warning() {

        $missing = '';
        $counter = 0;
        foreach ($this->missing as $title => $url) {
            $counter++;
            if ($counter == sizeof($this->missing)) {
                $sep = '';
            } elseif ($counter == sizeof($this->missing) - 1) {
                $sep = ' ' . __('and', 'woocommerce-products-visibility') . ' ';
            } else {
                $sep = ', ';
            }
            $missing .= '<a href="' . $url . '" target="_blank">' . $title . '</a>' . $sep;
        }
        ?>

        <div class="message error"><p><?php printf(__('WooCommerce Products Visibility is enabled but not effective. It requires %s in order to work.', 'woocommerce-products-visibility'), $missing); ?></p></div>
        <?php
    }

}
