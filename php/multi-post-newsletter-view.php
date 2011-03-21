<?php
/**
 * Multi Post Newsletter
 * @license CC-BY-SA-NC
 * @package Multi Post Newsletter
 * @subpackage View
 */

class multi_post_newsletter_view {
	public static function wrapper_start ( $headline ) {
		?>
		<div class="wrap nosubsub">
			<div id="icon-themes" class="icon32"><br></div>
			<h2>
				<?php _e( 'MP-Newsletter', multi_post_newsletter::get_textdomain() ); ?> - <?php echo $headline; ?>
			</h2>
			<div id="ajax-response"></div>
		<?php
	}
	
	/**
	 * Error Message, text_domain is set here
	 * @param string $error
	 */
	public function error ( $error ) {
		?>
		<div class="error"><p><?php _e( $error, multi_post_newsletter::get_textdomain() ); ?></p></div>
		<?php
	}
	
	public function update ( $msg ) {
		?>
		<div class="updated"><p><?php _e( $msg, multi_post_newsletter::get_textdomain() ); ?></p></div>
		<?php
	}
	
	public static function wrapper_end () {
		?>
		</div>
		<?php
	}
	
	public static function form_template ( $template_params ) {
		?>
            <div id="poststuff" class="metabox-holder">
             <div id="post-body">
              <div id="post-body-content">
               <div class="stuffbox">
                <h3><?php _e( 'Template Configuration', multi_post_newsletter::get_textdomain() ) ?></h3>
                <div class="inside">
                 <form action="" method="post">
                  <input name="save_settings" type="submit" class="button-primary" tabindex="14" value="<?php _e( 'Save' ); ?>" style="float: right;" />
                  <table class="form-table">
                   <tbody>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[color_even]"><?php _e( 'Background-Color (even)', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[color_even]" name="param[color_even]" type="text" value="<? echo $template_params['params']['color_even']; ?>" tabindex="6" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[color_odd]"><?php _e( 'Background-Color (odd)', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[color_odd]" name="param[color_odd]" type="text" value="<? echo $template_params['params']['color_odd']; ?>" tabindex="7" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents_before]"><?php _e( 'Contents Headline (before)', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[contents_before]" name="param[contents_before]" type="text" value="<? echo $template_params['params']['contents_before']; ?>" tabindex="8" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents_after]"><?php _e( 'Contents Headlines (after)', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[contents_after]" name="param[contents_after]" type="text" value="<? echo $template_params['params']['contents_after']; ?>" tabindex="9" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[categorie_before]"><?php _e( 'Categorie Headlines (before)', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[categorie_before]" name="param[categorie_before]" type="text" value="<? echo $template_params['params']['categorie_before']; ?>" tabindex="10" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[categorie_after]"><?php _e( 'Categorie Headlines (after)', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[categorie_after]" name="param[categorie_after]" type="text" value="<? echo $template_params['params']['categorie_after']; ?>" tabindex="11" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[contents]"><?php _e( 'Display contents', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[contents]" name="param[contents]" type="checkbox" tabindex="3" <?php if ( 'on' == $template_params['params']['contents'] ) { echo 'checked="checked"'; } ?> />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[excerpt]"><?php _e( 'Content as Excerpt', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[excerpt]" name="param[excerpt]" type="checkbox" tabindex="3" <?php if ( 'on' == $template_params['params']['excerpt'] ) { echo 'checked="checked"'; } ?> />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[header]"><?php _e( 'Intro-Text', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="param[header]" name="param[header]" tabindex="12" rows="20" class="large-text"><? echo $template_params['params']['header']; ?></textarea><br />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[footer]"><?php _e( 'Footer-Text', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="param[footer]" name="param[footer]" tabindex="12" rows="20" class="large-text"><? echo $template_params['params']['footer']; ?></textarea><br />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="template[main_template]"><?php _e( 'Newsletter Template', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="template[main_template]" name="template[main_template]" tabindex="12" rows="20" class="large-text"><? echo $template_params['main_template']; ?></textarea><br />
                      <small>Tags: %NAME% // %HEADER% // %DATE% // %CONTENTS% // %FOOTER% // %BODY%</small>
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="template[post_template]"><?php _e( 'Single Post Template', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <textarea id="template[post_template]" name="template[post_template]" tabindex="13" rows="20" class="large-text"><? echo $template_params['post_template']; ?></textarea><br />
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
	
	public static function form_settings ( $params ) {
		?>
            <div id="poststuff" class="metabox-holder">
             <div id="post-body">
              <div id="post-body-content">
               <div class="stuffbox">
                <h3><?php _e( 'Newsletter Configuration', multi_post_newsletter::get_textdomain() ) ?></h3>
                <div class="inside">
                 <form action="" method="post">
                  <table class="form-table">
                   <tbody>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[from_name]"><?php _e( 'Sender Name', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[from_name]" name="param[from_name]" type="text" value="<? echo $params['from_name']; ?>" tabindex="1" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[from_mail]"><?php _e( 'Sender E-Mail', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[from_mail]" name="param[from_mail]" type="text" value="<? echo $params['from_mail']; ?>" tabindex="2" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[to_test]"><?php _e( 'Recipient of Testmail', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[to_test]" name="param[to_test]" type="text" value="<? echo $params['to_test']; ?>" tabindex="3" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[to]"><?php _e( 'Recipient of Newsletter', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[to]" name="param[to]" type="text" value="<? echo $params['to']; ?>" tabindex="4" class="regular-text" />
                     </td>
                    </tr>
                    <tr valign="top">
                     <th scope="row">
                      <label for="param[exclude]"><?php _e( 'Exluded Categories <small>IDs, comma sperated</small>', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <input id="param[exclude]" name="param[exclude]" type="text" value="<? echo $params['exclude']; ?>" tabindex="5" class="regular-text" />
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
	
	public static function show_editions ( $editions ) {
		?>
            <div id="poststuff" class="metabox-holder has-right-sidebar">
             <div id="post-body">
              <div id="post-body-content">
               <div class="stuffbox">
                <h3><?php _e( 'Newsletter Configuration', multi_post_newsletter::get_textdomain() ) ?></h3>
                <div class="inside">
                 <form action="" method="post">
                  <input name="generate_newsletter" type="submit" class="button-primary" tabindex="1" value="<?php _e( 'Generate Newsletter', multi_post_newsletter::get_textdomain() ); ?>" style="float: right;" />
                  <table class="form-table" style="float: left; margin: -25px 0 0 0; width: 75%">
                   <tbody>
                    <tr valign="top">
                     <th scope="row">
                      <label for="edition"><?php _e( 'Choose Edition', multi_post_newsletter::get_textdomain() ); ?>:</label>
                     </th>
                     <td>
                      <select name="edition" id="edition">
			          <?php foreach ( $editions as $edition ) { ?>
			             <option value="<?php echo $edition->slug; ?>" <?php if ( $edition->slug == $_POST['edition'] ) { echo 'selected="selected"'; } ?>><?php echo $edition->name; ?></option>
			          <?php } ?>
			          </select>
                     </td>
                    </tr>
                   </tbody>
                  </table>
                  <br class="clear" />
                 </form>
                </div>
               </div>
              </div>
             </div>
            </div>
            <br class="clear" />
        <?php 
	}
	
	public static function sortable_start () {
		?>
            <div id="poststuff" class="metabox-holder has-right-sidebar" style="padding-top: 0;">
             <div id="post-body">
              <div id="post-body-content">
               <form action="" method="post" style="margin-bottom: 5px;">
                <a class="preview button" style="float: left;" href="javascript:history.back(-1)"><?php _e( 'Back', multi_post_newsletter::get_textdomain() ) ?></a>
                <input type="hidden" name="edition" id="edition" value="<?php echo $_POST['edition']; ?>" />
                <input name="preview_newsletter" type="submit" class="button-primary" tabindex="1" value="<?php _e( 'Generate Preview', multi_post_newsletter::get_textdomain() ); ?>" style="float: right;" />
                <br class="clear" />
               </form>
               <div class="stuffbox">
                <h3><?php _e( 'Order Newsletter Articles', multi_post_newsletter::get_textdomain() ) ?></h3>
                 <div class="inside">
                
        <?php
	}
	
	public static function sortable_end () {
		?>
                </div>
               </div>
              </div>
             </div>
            </div>
        <?php
	}
	
	public static function show_sortable_category ( $category ) {
		?>
            <h4><?php echo $category->name; ?></h4>
            <div id="<?php echo $category->term_id; ?>" class="sortable-holder" style="margin: 0 0 0 20px;">
        <?php
	}
	
	public static function show_sortable_end () {
		?>
            </div>
        <?php
	}
	
	public static function show_sortable_post () {
		?>
            <div id="<?php the_ID(); ?>" class="stuffbox post-sortable">
                <h3 class="hndle"><?php the_title(); ?></h3>
            </div>
        <?php
	}
	
	public static function newsletter_preview ( $newsletter_html, $newsletter_text ) {
		?>
            <div id="poststuff" class="metabox-holder has-right-sidebar" style="padding-top: 0;">
             <div id="post-body">
              <div id="post-body-content">
              
		        <div id="menu-management">
                    <a class="preview button" style="float: left; margin-right: 5px;" href="javascript:history.back(-1)"><?php _e( 'Back', multi_post_newsletter::get_textdomain() ) ?></a>
                    <?php if ( isset( $_POST['send_test_newsletter'] ) ) { ?>
                        <form action="" method="post">
                            <input type="hidden" name="edition" id="edition" value="<?php echo $_POST['edition']; ?>" />
                            <input name="send_newsletter" type="submit" class="button-primary" tabindex="1" value="<?php _e( 'Test successful? Send Newsletter now!', multi_post_newsletter::get_textdomain() ); ?>" style="float: right;" />
                        </form>
                    <?php } ?>
                    <form action="" method="post">
                        <input type="hidden" name="preview_newsletter" id="preview_newsletter" value="preview" />
                        <input type="hidden" name="edition" id="edition" value="<?php echo $_POST['edition']; ?>" />
                        <input name="send_test_newsletter" type="submit" class="button-<?php if ( isset( $_POST['send_test_newsletter'] ) ) { echo 'secondary'; } else { echo 'primary'; } ?>" tabindex="1" value="<?php _e( 'Send Test Newsletter', multi_post_newsletter::get_textdomain() ); ?>" style="float: right;" />
                    </form>
		            <div class="nav-tabs">
		                <a href="javascript:multi_post_newsletter.switch_preview( 'text' );" class="nav-tab hide-if-no-js" id="link_text_preview"><?php _e( 'Text-Preview', multi_post_newsletter::get_textdomain() ) ?></a>
		                <a href="javascript:multi_post_newsletter.switch_preview( 'html' );" class="nav-tab hide-if-no-js nav-tab-active" id="link_html_preview"><?php _e( 'HTML-Preview', multi_post_newsletter::get_textdomain() ) ?></a>
		            </div>
		        </div>
                
                <div class="stuffbox">
                <h3><?php _e( 'Newsletter Preview', multi_post_newsletter::get_textdomain() ); ?></h3>
                <div class="inside" id="html-preview">
                 <p><?php echo $newsletter_html; ?></p>
                </div>
                <div class="inside" id="text-preview" style="display: none">
                 <p><?php echo $newsletter_text; ?></p>
                </div>
               </div>
               
               <a href="#"><?php _e( 'to top', multi_post_newsletter::get_textdomain() ); ?></a>
	          </div>
	         </div>
	        </div>
        <?php
	}
}