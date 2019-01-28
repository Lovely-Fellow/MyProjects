<?php
$hash             = !empty($_GET['hash']) ? $_GET['hash'] : '';
$saved_customizer = Customizer_Public::get_saved_customizer($hash);
$customizer_id    = Customizer_Public::get_customizer_id();
$curr_user        = get_current_user_id();
?>
    <a href="#" id="customizer_get_the_link"><?php echo __('Get the link', 'customizer'); ?></a>
    <br/>
    <div class="customizer-save-block" style="display: none;">
        <br/>
        <input type="hidden" name="save_customizer[id]"
               value="<?php echo $saved_customizer ? $saved_customizer->id : ''; ?>">
        <input type="hidden" name="save_customizer[hash]"
               value="<?php echo $saved_customizer ? $saved_customizer->hash : ''; ?>">
        <input type="hidden" name="saved_options" id="customizer_saved_options"
               value="<?php echo $saved_customizer ? esc_attr($saved_customizer->selected_options) : ''; ?>">
        <div class="save_customizer_input_row">
            <input name="save_customizer[name_customizer]"
                   placeholder="<?php echo __('Name (optional)', 'customizer'); ?>" class="save_customizer_name"
                   value="<?php echo !empty($saved_customizer->name_customizer) ? $saved_customizer->name_customizer : ''; ?>">
            <?php if (is_user_logged_in() && !empty($saved_customizer) && $curr_user == $saved_customizer->user_id) : ?>
                <button name="customizer-update" id="customizer-update" value="update" type="submit"
                        class="single_add_to_cart_button button save_customizer"><?php echo __('Update', 'customizer'); ?></button>
            <?php else: ?>
                <button type="submit" name="customizer-save" id="customizer-save" value="save"
                        class="single_add_to_cart_button button save_customizer">
                    <?php echo __('Get', 'customizer'); ?></button>
            <?php endif ?>
        </div>
    </div>
<?php if (!empty($saved_customizer)): ?>
    <?php $url = Customizer_Public::get_customizer_link($customizer_id, $saved_customizer->hash); ?>
    <a target="_blank" name="customizer-link" class="customizer-link" href="<?php echo $url; ?>"><?php echo $url; ?></a>
    <?php require_once 'social_buttons.php' ?>
    <?php /**<form name='print_form' action='' method='post' target="_blank">
     * <input name='print_customizer' type='submit' class='button primary' value='<?php echo __('Print', 'customizer') ?>'>
     * <input name='customizer_hash' type='hidden' value='<?php echo $saved_customizer->hash ?>'>
     * </form>*/ ?>
<?php endif; ?>