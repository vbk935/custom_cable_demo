<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_FRONTEND_GET_SETTINGS {

    public $productids = array();
    public $has_productids = false;
    public $products_visibility;
    public $categoryids = array();
    public $has_categoryids = false;
    public $categories_visibility;
    public $tagids = array();
    public $has_tagids = false;
    public $tags_visibility;

    public function __construct() {

        if (is_user_logged_in()) {
            $rolesPriority = new WCPV_ROLES_PRIORITY();

            $get_role = self::get_user_roles_from_id(get_current_user_id());
            $check_if_multiple_user_roles_exist = get_option('wcpv_multiple_roles');
            if ($check_if_multiple_user_roles_exist) {
                uasort($get_role, array($rolesPriority, "sort_by_priority"));
            }
            array_unshift($get_role, 'default');

            $get_settings = self::get_options_value_from_roles($get_role);
        } else {
            $get_role = "guest";
            $get_settings = self::get_options_value_from_roles(array($get_role));
        }

        foreach ($get_settings as $role_settings) {
            $this->combine_role_settings($role_settings, 'productids', 'productids', 'visibility_products', 'products_visibility', 'has_productids');
            $this->combine_role_settings($role_settings, 'categoryids', 'categoryids', 'visibility_categories', 'categories_visibility', 'has_categoryids');
            $this->combine_role_settings($role_settings, 'tagids', 'tagids', 'visibility_tags', 'tags_visibility', 'has_tagids');
        }
    }

    private function combine_role_settings($role_settings, $ids_settings_field, $ids_property, $visibility_settings_field, $visibility_property, $has_ids_property) {

        $new_ids = array_filter((array) $role_settings[$ids_settings_field]);

        if (!empty($new_ids)) {
            if (!$this->$has_ids_property) {
                if ($role_settings[$visibility_settings_field] != 'cancel-exclude') {
                    $this->$ids_property = $new_ids;
                    $this->$has_ids_property = !empty($this->$ids_property);
                    $this->$visibility_property = $role_settings[$visibility_settings_field];
                }
            } else {
                if ($role_settings[$visibility_settings_field] == 'include' && $this->$visibility_property == 'include') {
                    $wcpv_mur_intersect_include = apply_filters('wcpv_mur_intersect_include', true);
                    if ($wcpv_mur_intersect_include) {
                        $this->$ids_property = array_intersect($this->$ids_property, $new_ids);
                    } else {
                        $this->$ids_property = array_merge($this->$ids_property, $new_ids);
                    }
                } else if ($role_settings[$visibility_settings_field] == 'include' && $this->$visibility_property == 'exclude') {
                    $this->$ids_property = array_merge(array_diff($new_ids, $this->$ids_property));
                    $this->$visibility_property = 'include';
                } else if ($role_settings[$visibility_settings_field] == 'exclude' && $this->$visibility_property == 'exclude') {
                    $this->$ids_property = array_merge($this->$ids_property, $new_ids);
                } else if ($role_settings[$visibility_settings_field] == 'exclude' && $this->$visibility_property == 'include') {
                    $this->$ids_property = array_merge(array_diff($this->$ids_property, $new_ids));
                } else if ($role_settings[$visibility_settings_field] == 'cancel-exclude' && $this->$visibility_property == 'include') {
                    $this->$ids_property = array_merge($this->$ids_property, $new_ids);
                } else if ($role_settings[$visibility_settings_field] == 'cancel-exclude' && $this->$visibility_property == 'exclude') {
                    $this->$ids_property = array_merge(array_diff($this->$ids_property, $new_ids));
                }

                $this->$has_ids_property = !empty($this->$ids_property);
            }
        }
    }

    private function get_user_roles_from_id($user_id) {
        $get_user_data = get_userdata($user_id);
        return $get_user_data->roles;
    }

    private function get_visibility_option($option) {
        switch (get_option($option)) {
            case '1':
                return 'include';
            case '2':
                return 'exclude';
            case '3':
                return 'cancel-exclude';
            default:
                return 'exclude';
        }
    }

    private function get_options_value_from_roles($roles) {
        $array_structure = array();

        foreach ($roles as $role) {
            $array_structure[] = array(
                'visibility_products' => self::get_visibility_option('wcpv_products_visibility_' . $role),
                'productids' => explode(",", get_option('wcpv_products_' . $role)),
                'visibility_tags' => self::get_visibility_option('wcpv_tags_visibility_' . $role),
                'tagids' => get_option('wcpv_tags_' . $role),
                'visibility_categories' => self::get_visibility_option('wcpv_categories_visibility_' . $role),
                'categoryids' => get_option('wcpv_categories_' . $role),
            );
        }
        return $array_structure;
    }

}
