<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CustomizerListTable extends WP_List_Table
{

    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'saved_customizer',
            'plural'   => 'saved_customizers',
            'ajax'     => false,
        ));

        $this->prepare_items();
    }

    public function prepare_items()
    {
        $per_page  = $this->get_items_per_page('saved_customizer_per_page', 10);
        $all_items = $this->get_all_customizers();

        $total_items = count($all_items);
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);

        $current_page = (int)$this->get_pagenum();
        $this->items  = array_slice($all_items, (($current_page - 1) * $per_page), $per_page);

        $columns               = $this->get_columns();
        $hidden                = $this->get_hidden_columns();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    }

    public function get_columns()
    {
        return array(
            'cb'              => '<input type="checkbox" />',
            'name_customizer' => __('Customizer name', 'customizer'),
            'user_email'      => __('User Email', 'customizer'),
            'public'          => __('Public', 'customizer'),
            'deleted'         => __('Deleted', 'customizer'),
            'count_view'      => __('Count view', 'customizer'),
            'updated_at'      => __('Updated at', 'customizer'),
            'print'           => __('Print', 'customizer')
        );
    }

    protected function get_sortable_columns()
    {
        return array(
            'name_customizer' => array('name_customizer', false),
            'user_email'      => array('user_email', false),
            'count_view'      => array('count_view', false),
            'updated_at'      => array('updated_at', false),
        );
    }

    public function get_hidden_columns()
    {
        $user   = get_current_user_id();
        $hidden = get_user_meta($user, 'managecustomizer_page_customizer-savedcolumnshidden', true);
        if (empty($hidden)) {
            $hidden = array();
        }
        return is_array($hidden) ? $hidden : [];
    }

    protected function get_bulk_actions()
    {
        return array(
            'delete'  => 'Delete',
            //todo add action controller
            'publish' => 'Publish',
            'hide'    => 'Hide'
        );
    }

    public function column_default($item, $colname)
    {
        return isset($item->$colname) ? $item->$colname : print_r($item, 1);
    }

    public function no_items()
    {
        echo __('No saved customizers', 'customizer');
    }

    public function column_cb($item)
    {
        echo '<input type="checkbox" name="permanent_del[]" id="cb-select-' . $item->id . '" value="' . $item->id . '"  />';
    }

    public function column_public($item)
    {
        $html = "<input type='checkbox' name='public_state'";
        $html .= !empty($item->public) ? "checked" : '';
        $html .= " onChange='document.getElementById(\"{$item->hash}_public_change\").value = \"changed\";this.form.submit();'>";
        $html .= "<input type='hidden' name='{$item->hash}_public_change' id='{$item->hash}_public_change' value=''>";
        echo $html;
    }

    public function column_deleted($item)
    {
        $html = "<input type='checkbox' name='deleted_state'";
        $html .= !empty($item->deleted) ? "checked" : '';
        $html .= " onChange='document.getElementById(\"{$item->hash}_deleted_change\").value = \"changed\";this.form.submit();'>";
        $html .= "<input type='hidden' name='{$item->hash}_deleted_change' id='{$item->hash}_deleted_change' value=''>";
        echo $html;
    }

    public function column_name_customizer($item)
    {
        $link = Customizer_Public::get_customizer_link($item->customizer_id, $item->hash);
        $html = "<a name='customizer-link' href='" . $link . "'>" . $item->name_customizer . "</a>";
        echo $html;
    }

    public function column_updated_at($item)
    {
        echo date_i18n(get_option('date_format'), ($item->updated_at ? $item->updated_at : $item->created_at));
    }

    public function get_all_customizers()
    {
        global $wpdb;

        $name_table = $wpdb->prefix . Customizer_Public::SAVE_TABLE_NAME;
        $where      = [];

        $users_table = $wpdb->prefix . 'users';
        $sql         = "SELECT customizer_id, name_customizer, {$users_table}.user_email," .
            " public, deleted, count_view, hash, {$name_table}.id, " .
            " updated_at FROM {$name_table}";
        $sql         .= " left join {$users_table} on {$users_table}.ID={$name_table}.user_id ";

        $where = implode(' AND ', $where);
        if (!empty($where)) {
            $sql .= ' where ' . $where;
        }
        if (!empty($_GET['orderby'])) {
            $sql .= ' order by ' . $_GET['orderby'] . ' ' . $_GET['order'];
        }

        $rows = $wpdb->get_results($sql);
        return $rows;
    }

    /**
     * @param $item
     * @return string
     */
    public function column_print($item)
    {
        $link = Customizer_Public::get_customizer_link($item->customizer_id, $item->hash);
        $link .= '&print=1';
        return '<a href="' . $link . '" target="_blank">' . __('Print', 'customizer') . '</a>';
    }

    /**
     * @param $item
     * @return string
     */
    public function column_user_email($item)
    {
        if (!empty($item->user_email)) {
            return $item->user_email;
        }
        return '&mdash;';
    }

}