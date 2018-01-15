<?php
class Bea_Post_Offline_Admin{
	/**
	 * Constructor, register hooks
	 */
	public function __construct() {
		add_action( 'admin_print_footer_scripts',  array( __CLASS__, 'admin_print_footer_scripts'  ) );
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'post_submitbox_misc_actions' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
	}

	/**
	 * Add javascript on footer admin for add custom status with JS
	 *
	 * @return boolean
	 * @author Amaury Balmer
	 */
	public static function admin_print_footer_scripts() {
		global $post, $pagenow;

		// Get custom status and post types
		$custom_status 		= Bea_Post_Offline_Base::getStatus();
		$custom_post_types 	= Bea_Post_Offline_Base::getPostTypes();

		if ( ( $pagenow == 'post-new.php' && isset($_GET['post_type']) && in_array( $_GET['post_type'], $custom_post_types) ) || ( $pagenow == 'post.php' && isset($_GET['post']) && in_array( $post->post_type, $custom_post_types ) ) ) {
			echo '<script type="text/javascript">' . "\n";
				echo 'jQuery(document).ready( function() {' . "\n";
					// Insert custom status on select
					echo 'var cur = jQuery("#post_status").html();' . "\n";
					foreach( $custom_status as $name ) {
						echo 'cur += "<option value=\"'.esc_attr($name).'\" '.selected($post->post_status, $name, false).'>'.esc_html(get_post_status_object($name)->label).'</option>";' . "\n";
					}
					echo 'jQuery("#post_status").html( cur );' . "\n";

					// Inject custom status on state line
					if ( in_array($post->post_status, $custom_status) ) {
						echo 'if ( jQuery("#post-status-display").html().trim() == "" ) {' . "\n";
							echo 'jQuery("#post-status-display").html("'.esc_html(get_post_status_object($post->post_status)->label).'");' . "\n";
						echo '}' . "\n";
					}
				echo '});' . "\n";
			echo '</script>' . "\n";
			return true;
		} elseif ( ( $pagenow == 'edit.php' && isset($_GET['post_type']) && in_array( $_GET['post_type'], $custom_post_types) ) ) {
			echo '<script type="text/javascript">' . "\n";
				echo 'jQuery(document).ready( function() {' . "\n";
					// Insert custom status on select
					echo 'var cur = jQuery(".inline-edit-status select[name=_status]").html();' . "\n";
					foreach( $custom_status as $name ) {
						echo 'cur += "<option value=\"'.esc_attr($name).'\">'.esc_html(get_post_status_object($name)->label).'</option>";' . "\n";
					}
					echo 'jQuery(".inline-edit-status select[name=_status]").html( cur );' . "\n";
				echo '});' . "\n";
			echo '</script>' . "\n";
			return true;
		}
		return false;
	}

	public static function post_submitbox_misc_actions() {
		global $post;

		// Get custom post types
		$custom_post_types 	= Bea_Post_Offline_Base::getPostTypes();

		// Check if current page is CPT
		if ( ( isset($_GET['post_type']) && !in_array( $_GET['post_type'], $custom_post_types) ) || ( isset($_GET['post']) && !in_array( $post->post_type, $custom_post_types ) ) ) {
			return false;
		}

		$date = '';
		if ( (int) $post->ID > 0 ) {
			$now = current_time('timestamp');

			// Timestamp
			$current_timestamp = get_post_meta( $post->ID, '_offline_date', true );
			if ( $current_timestamp == false ) {
				$checked = false;
				$current_timestamp = $now;
			} else {
				$checked = true;
			}

			$date = date_i18n( __( 'M j, Y @ G:i' ), $current_timestamp ); // translators: Publish box date format, see http://php.net/date
			if ( $post->post_status == 'offline' || ( $checked == true && $now >= $current_timestamp ) ) { // draft, 1 or more saves, date specified
				$stamp = __('Offline since: <b>%1$s</b>', 'bea-po');
			} else {
				if ( $now < $current_timestamp ) { // draft, 1 or more saves, future date specified
					$stamp = __('Offline Schedule for: <b>%1$s</b>', 'bea-po');
				} else {
					$stamp = __('Never expires', 'bea-po');
					$date = '';
				}
			}

		} else { // draft (no saves, and thus no date specified)
			$checked = false;
			$stamp = __('Never expires', 'bea-po');
			$current_timestamp = current_time('timestamp');
		}
		?>
		<div class="misc-pub-section curtime curtime-offline">
			<span id="offline_timestamp"><?php printf($stamp, $date); ?></span>
			<a href="#edit_offline_timestamp" class="edit-offline-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit', 'bea-po') ?></a>
			<div id="offline-timestampdiv" class="hide-if-js">
				<label for="enable-offline">
					<input type="checkbox" id="enable-offline" name="enable-offline" <?php checked($checked, true); ?> value="1" />
					<?php _e('Enable expiration', 'bea-po'); ?>
				</label>

				<?php echo self::touch_time( 5, $current_timestamp ); ?>
			</div>
		</div>
		<input type="hidden" name="enable-offline-box" value="1" />
		<?php
		return true;
	}

	/**
	 * Hack function from WordPress, touch_time, for need of this plugin
	 *
	 * @param int $tab_index
	 * @param int $current_timestamp
	 *
	 * @return string
	 */
	public static function touch_time( $tab_index = 0, $current_timestamp = 0 ) {
		global $wp_locale;

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 )
			$tab_index_attribute = " tabindex=\"$tab_index\"";

		// DB date
		$jj = date ( 'd', $current_timestamp );
		$mm = date ( 'm', $current_timestamp );
		$aa = date ( 'Y', $current_timestamp );
		$hh = date ( 'H', $current_timestamp );
		$mn = date ( 'i', $current_timestamp );
		$ss = date ( 's', $current_timestamp );

		// Current date
		$time_adj = current_time('timestamp');
		$cur_jj = date ( 'd', $time_adj );
		$cur_mm = date ( 'm', $time_adj );
		$cur_aa = date ( 'Y', $time_adj );
		$cur_hh = date ( 'H', $time_adj );
		$cur_mn = date ( 'i', $time_adj );

		$month = "<select id=\"offline-mm\" name=\"offline_mm\"$tab_index_attribute>\n";
		for ( $i = 1; $i < 13; $i = $i +1 ) {
			$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"' . selected($i, (int) $mm, false) . '>' .zeroise( $i, 2 ).'-'.$wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
		}
		$month .= '</select>';

		$day = '<input type="text" id="offline-jj" name="offline_jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
		$year = '<input type="text" id="offline-aa" name="offline_aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
		$hour = '<input type="text" id="offline-hh" name="offline_hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
		$minute = '<input type="text" id="offline-mn" name="offline_mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

		$output = '<div class="offline-timestamp-wrap">';
		/* translators: 1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input */
		$output .= sprintf(__('%2$s%1$s, %3$s @ %4$s : %5$s'), $month, $day, $year, $hour, $minute);

		$output .= '</div><input type="hidden" id="offline-ss" name="offline_ss" value="' . $ss . '" />';

		$output .= "\n\n";
		foreach ( array('jj', 'mm', 'aa', 'hh', 'mn') as $timeunit ) {
			$output .= '<input type="hidden" id="offline-hidden_' . $timeunit . '" name="offline_hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
			$cur_timeunit = 'cur_' . $timeunit;
			$output .= '<input type="hidden" id="offline-'. $cur_timeunit . '" name="offline_'. $cur_timeunit . '" value="' . $$cur_timeunit . '" />' . "\n";
		}

		$output .= '<p>';
			$output .= '<a href="#edit_offline_timestamp" class="save-offline-timestamp hide-if-no-js button">'.__('OK', 'bea-po').'</a>';
			$output .= '<a href="#edit_offline_timestamp" class="cancel-offline-timestamp hide-if-no-js cancel-button">'.__('Cancel', 'bea-po').'</a>';
		$output .= '</p>';

		return $output;
	}

	/**
	 * Add custom JS on edit page
	 *
	 * @param string $hook_suffix
	 */
	public static function admin_enqueue_scripts( $hook_suffix = '' ) {
		if ( $hook_suffix == 'post.php' || $hook_suffix == 'post-new.php' ) {
			wp_enqueue_style  ( 'bea-post-admin', BEA_PO_URL . '/ressources/admin-post.css', array(), BEA_PO_VERSION );
			wp_enqueue_script ( 'bea-post-admin', BEA_PO_URL . '/ressources/admin-post.js', array('jquery'), BEA_PO_VERSION, true );
			wp_localize_script( 'bea-post-admin', 'beaPostL10n', array(
				'since' => __('Offline since: ', 'bea-po'),
				'schedule' => __('Offline Schedule for: ', 'bea-po'),
				'never' => __('Never expires', 'bea-po')
			));
		}
	}

	/**
	 * Save expiration date on post meta
	 *
	 * @param int $object_id
	 * @param null $object
	 */
	public static function save_post( $object_id = 0, $object = null ) {
		if ( isset($_POST['enable-offline-box']) ) {
			if ( isset($_POST['enable-offline']) ) {
				if ( $object == null )
					$object = get_post($object_id);

				// Take from WP core
				$aa = $_POST['offline_aa'];
				$mm = $_POST['offline_mm'];
				$jj = $_POST['offline_jj'];
				$hh = $_POST['offline_hh'];
				$mn = $_POST['offline_mn'];
				$ss = $_POST['offline_ss'];
				$aa = ($aa <= 0 ) ? date('Y') : $aa;
				$mm = ($mm <= 0 ) ? date('n') : $mm;
				$jj = ($jj > 31 ) ? 31 : $jj;
				$jj = ($jj <= 0 ) ? date('j') : $jj;
				$hh = ($hh > 23 ) ? $hh -24 : $hh;
				$mn = ($mn > 59 ) ? $mn -60 : $mn;
				$ss = ($ss > 59 ) ? $ss -60 : $ss;

				// Fields to timestamp
				$new_timestamp = mysql2date('U', sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss ));

				// Save meta
				update_post_meta( $object_id, '_offline_date', $new_timestamp );

				// Change status on fly ?
				if ( $object->post_status != 'offline' && $new_timestamp <= current_time('timestamp') ) {
					wp_update_post( array( 'ID' => $object_id, 'post_status' => 'offline' ) );
				}
			} else {
				delete_post_meta( $object_id, '_offline_date' );
			}
		}
	}
}