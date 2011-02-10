<?php

class mpnl_view {
	public static function form_config ( $params, $template ) {
		?>
            <div id="poststuff" class="metabox-holder">
             <div id="post-body">
              <div id="post-body-content">
               <div class="stuffbox">
                <h3><?php _e( 'Newsletter Configuration', 'th_mpnl' ) ?></h3>
                <div class="inside">
                 <form action="" method="post">
                  <input name="save_settings" type="submit" class="button-primary" tabindex="14" value="<?php _e( 'Save' ); ?>" style="float: right;" />
                  
                  <table class="form-table">
                   <tbody>
                    <tr>
                     <th scope="row" colspan="2">
                      <h4><big><?php _e( 'General Configuration', 'th_mpnl' ); ?></big></h4>
                     </th>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[from_name]"><?php _e( 'Sender Name', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[from_name]" name="param[from_name]" type="text" value="<? echo $params['from_name']; ?>" tabindex="1" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[from_mail]"><?php _e( 'Sender E-Mail', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[from_mail]" name="param[from_mail]" type="text" value="<? echo $params['from_mail']; ?>" tabindex="2" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[to_test]"><?php _e( 'Recipient of Testmail', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[to_test]" name="param[to_test]" type="text" value="<? echo $params['to_test']; ?>" tabindex="3" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[to]"><?php _e( 'Recipient of Newsletter', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[to]" name="param[to]" type="text" value="<? echo $params['to']; ?>" tabindex="4" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[exclude]"><?php _e( 'Exluded Categories <small>IDs, comma sperated</small>', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[exclude]" name="param[exclude]" type="text" value="<? echo $params['exclude']; ?>" tabindex="5" class="regular-text" />
                     </td>
                    </tr>
                    <tr>
                     <th scope="row" colspan="2">
                      <h4><big><?php _e( 'Template Configuration', 'th_mpnl' ); ?></big></h4>
                     </th>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[color_even]"><?php _e( 'Background-Color (even)', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[color_even]" name="param[color_even]" type="text" value="<? echo $params['color_even']; ?>" tabindex="6" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[color_odd]"><?php _e( 'Background-Color (odd)', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[color_odd]" name="param[color_odd]" type="text" value="<? echo $params['color_odd']; ?>" tabindex="7" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents_before]"><?php _e( 'Contents Headline (before)', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[contents_before]" name="param[contents_before]" type="text" value="<? echo $params['contents_before']; ?>" tabindex="8" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents_after]"><?php _e( 'Contents Headlines (after)', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[contents_after]" name="param[contents_after]" type="text" value="<? echo $params['contents_after']; ?>" tabindex="9" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[categorie_before]"><?php _e( 'Categorie Headlines (before)', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[categorie_before]" name="param[categorie_before]" type="text" value="<? echo $params['categorie_before']; ?>" tabindex="10" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[categorie_after]"><?php _e( 'Categorie Headlines (after)', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[categorie_after]" name="param[categorie_after]" type="text" value="<? echo $params['categorie_after']; ?>" tabindex="11" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="template[main_template]"><?php _e( 'Newsletter Template', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="template[main_template]" name="template[main_template]" tabindex="12" rows="20" class="large-text"><? echo $template['main_template']; ?></textarea><br />
                      <small>Tags: %NAME% // %HEADER% // %DATE% // %CONTENTS% // %BODY%</small>
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="template[post_template]"><?php _e( 'Single Post Template', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="template[post_template]" name="template[post_template]" tabindex="13" rows="20" class="large-text"><? echo $template['post_template']; ?></textarea><br />
                      <small>Tags: %TITLE% // %CONTENT% // %DATE% // %AUTHOR% // %COLOR% // %LINK%</small>
                     </td>
                    </tr>
                   </tbody>
                  </table>
                  
                  <input name="save_settings" type="submit" class="button-primary" tabindex="14" value="<?php _e( 'Save' ); ?>" style="float: right;" />
                  <br class="clear" />
                 </form>
                </div>
               </div>
              </div>
             </div>
            </div>
        <?php
	}
	
	public static function generate_newsletter ( $letters, $params ) {
		?>
            <div id="poststuff" class="metabox-holder">
             <div id="post-body">
              <div id="post-body-content">
               <div class="stuffbox">
                <h3><?php _e( 'Generate Newsletter', 'th_mpnl' ); ?></h3>
                <div class="inside">
                 <form action="" method="post">
                  <?php if ( isset( $_POST['send_test'] ) ) { ?>
                    <input name="send_mail" type="submit" class="button-primary" tabindex="7" value="<?php _e( 'Test successful? Send Newsletter now!', 'th_mpnl' ); ?>" style="float: right;" />
                  <?php } ?>
                  <input name="send_test" type="submit" class="button-secondary" tabindex="6" value="<?php _e( 'Send Testmail', 'th_mpnl' ); ?>" style="float: right;" />
                  <input name="preview" type="submit" class="button-secondary" tabindex="5" value="<?php _e( 'Preview' ); ?>" style="float: right;" />
                  
                  <table class="form-table">
                   <tbody>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[letter]"><?php _e( 'Select Newsletter', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <select name="param[letter]" tabindex="1">
                      <?php foreach ( $letters as $letter ) { ?>
                       <option value="<? echo $letter->slug; ?>"<? echo ($params['letter'] == $letter->slug ? " selected='seclected'" : ""); ?>><? echo $letter->name; ?></option>
                      <?php } ?>
                      </select>
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[title]"><?php _e( 'Title' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[title]" name="param[title]" type="text" value="<? echo $params['title']; ?>" tabindex="2" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents]"><?php _e( 'Display contents', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[contents]" name="param[contents]" type="checkbox" tabindex="3" <?php if ( 'on' == $params['contents'] ) { echo 'checked="checked"'; } ?> />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents]"><?php _e( 'Content as Excerpt', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[excerpt]" name="param[excerpt]" type="checkbox" tabindex="3" <?php if ( 'on' == $params['excerpt'] ) { echo 'checked="checked"'; } ?> />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[header]"><?php _e( 'Intro-Text', 'th_mpnl' ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="param[header]" name="param[header]" tabindex="4" rows="20" class="large-text"><? echo $params['header']; ?></textarea>
                     </td>
                    </tr>
                   </tbody>
                  </table>

                 </form>
                </div>
               </div>
              </div>
             </div>
            </div>
        <?php
	}
	
	public static function updated ( $text ) {
		?>
            <div class="updated"><p><?php echo $text; ?></p></div>
        <?php	
	}
}