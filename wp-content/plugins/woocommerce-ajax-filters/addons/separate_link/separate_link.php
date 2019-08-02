<?php
class BeRocket_aapf_separate_link_addon extends BeRocket_framework_addon_lib {
    public $addon_file = __FILE__;
    public $plugin_name = 'ajax_filters';
    public $php_file_name   = 'separate_vars';
    function get_addon_data() {
        $data = parent::get_addon_data();
        return array_merge($data, array(
            'addon_name'    => 'Separate Query Vars (BETA)'
        ));
    }
}
new BeRocket_aapf_separate_link_addon();
