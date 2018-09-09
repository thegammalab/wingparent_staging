<?php
/*
  New User Fields Class
  Version: 0.2
 */

class Bjm_user_fields {

    var $args;

    function __construct($fields) {

        $this->fields = $fields;

        add_action('show_user_profile', array($this, 'fields'));
        add_action('edit_user_profile', array($this, 'fields'));
        add_action('register_form', array($this, 'fields'));

        add_action('personal_options_update', array($this, 'save'));
        add_action('edit_user_profile_update', array($this, 'save'));
        add_action('user_register', array($this, 'save'));
    }

    function fields($user_id = null) {
        foreach ($this->fields as $field_name => $field_args)
            call_user_func(
                    array($this, 'display_' . $field_args['type']), array(
                'user_id' => is_object($user_id) ? $user_id->ID : $user_id,
                'name' => $field_name,
                'args' => $field_args
                    )
            );
    }

    function save($user_id = false) {
        // If user ID is false, means this will be a user sign up, not an update. 
        // @todo fix below, as it fails on user sign up. 
        // if ( $user_id && ( !current_user_can( 'edit_user', $user_id ) ) ) return false;
        // @todo validation would be good. 

        foreach ($this->fields as $field_name => $field_args) {
            if (isset($_REQUEST[$field_name])) {
                delete_user_meta($user_id, $field_name);
                if(is_array($_REQUEST[$field_name])){
                    foreach($_REQUEST[$field_name] as $v){
                        add_user_meta($user_id, $field_name, $v);
                    }
                }else{
                   add_user_meta($user_id, $field_name, $_REQUEST[$field_name]); 
                }
            } else {
                delete_user_meta($user_id, $field_name);
            }
        }
    }

    function display_title($arg_array) {
        extract($arg_array);
        ?>

        <h3> <?php echo $args['label']; ?> </h3>
        <?php
    }

    function display_text($arg_array) {
        extract($arg_array);
        ?>
        <table class="form-table"><tbody>
                <tr id="field-<?php echo $name; ?>">
                    <th><label for="<?php echo $name; ?>"> <?php echo $args['label']; ?> </label></th>
                    <td><input 
                            type="text" 
                            name="<?php echo $name; ?>" 
                            id="<?php echo $name; ?>" 
                            value="<?php echo get_user_meta($user_id,$name,true); ?>"  
                            /> 
                    </td></tr>
            </tbody></table>
        <?php
    }

    function display_textarea($arg_array) {
        extract($arg_array);
        ?>
        <table class="form-table"><tbody>
                <tr id="field-<?php echo $name; ?>">
                    <th><label for="<?php echo $name; ?>"> <?php echo $args['label']; ?> </label></th>
                    <td><textarea 
                            name="<?php echo $name; ?>" 
                            id="<?php echo $name; ?>" ><?php echo get_user_meta($user_id,$name,true); ?> 
                        </textarea> 	
                    </td></tr>
            </tbody></table>
        <?php
    }

    function display_checkbox($arg_array) {
        extract($arg_array);
        $vals = get_user_meta($user_id,$name);
        ?>
        <table class="form-table"><tbody>
                <tr id="field-<?php echo $name; ?>">
                    <th><label for="<?php echo $name; ?>"> <?php echo $args['label']; ?> </label></th>
                    <td><?php foreach ($args['options'] as $val => $option): ?>				
                            <label style="display:block;"><input 
                                    type="checkbox" 
                                    name="<?php echo $name; ?>[]" 
                                    id="<?php echo $name; ?>" 
                                    value="<?php echo $val; ?>"  
                                    <?php
                                    if (in_array($val,$vals))
                                        echo 'checked="checked";';
                                    ?> 
                                    /> 

                                <span><?php echo $option; ?></span></label>

                        <?php endforeach; ?>
                    </td></tr>
            </tbody></table>
        <?php
    }

    function display_radio($arg_array) {
        extract($arg_array);
        $vals = get_user_meta($user_id,$name);
        ?>
        <table class="form-table"><tbody>
                <tr id="field-<?php echo $name; ?>">
                    <th><label for="<?php echo $name; ?>"> <?php echo $args['label']; ?> </label></th>
                    <td><?php foreach ($args['options'] as $val => $option): ?>				
                        <label style="display:block;"><input 
                                    type="radio" 
                                    name="<?php echo $name; ?>" 
                                    id="<?php echo $name; ?>" 
                                    value="<?php echo $val; ?>"  
                                    <?php
                                    if (in_array($val,$vals))
                                        echo 'checked="checked";';
                                    ?> 
                                    /> 

                                <span><?php echo $option; ?></span></label>

                        <?php endforeach; ?>
                    </td></tr></tbody></table>
        <?php
    }

    function display_select($arg_array) {
        extract($arg_array);
        $vals = get_user_meta($user_id,$name);
        ?>
        <table class="form-table"><tbody>
                <tr id="field-<?php echo $name; ?>">
                    <th><label for="<?php echo $name; ?>"> <?php echo $args['label']; ?> </label></th>
                    <td><select name="<?php echo $name; ?>" id="<?php echo $name; ?>" >

                            <?php foreach ($args['options'] as $val => $option): ?>		

                                <option value="<?php echo trim($val); ?>"
                                <?php
                                if (in_array($val,$vals))
                                    echo 'selected="selected"';
                                ?> 
                                        >
                                            <?php echo trim($option); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </td></tr></tbody></table>
        <?php
    }

}
