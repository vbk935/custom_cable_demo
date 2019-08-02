<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_ROLES_PRIORITY {

    public function get_default_priority($role) {
        switch ($role) {
            case 'default':
                return -1;
            case 'guest':
                return 999999999;
            default:
                return 50;
        }
    }

    private function get_priority($role) {
        $check_if_multiple_user_roles_exist = get_option('wcpv_multiple_roles');
        if ($check_if_multiple_user_roles_exist) {
            return get_option('wcpv_role_priority_' . $role, $this->get_default_priority($role));
        } else {
            return $this->get_default_priority($role);
        }
        
    }

    public function sort_by_priority($role_a, $role_b) {

        $priority_a = $this->get_priority($role_a);
        $priority_b = $this->get_priority($role_b);

        if ($priority_a < $priority_b)
            return -1;
        else if ($priority_a == $priority_b)
            return 0;
        else
            return 1;
    }

}
