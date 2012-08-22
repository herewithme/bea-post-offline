var beaPostOffline = {
	"init" : function() {
		// Global var
		previous_status = 'no';
		checkbox_state = jQuery('#enable-offline').is(':checked');
		
		jQuery('#offline-timestampdiv').siblings('a.edit-offline-timestamp').click(function() {
			if (jQuery('#offline-timestampdiv').is(":hidden")) {
				jQuery('#offline-timestampdiv').slideDown('fast');
				
				jQuery('#offline_timestamp').hide();
				jQuery(this).hide();
			}
			return false;
		});
		
		jQuery('.offline-timestamp-wrap input, .offline-timestamp-wrap select').change(function() {
			jQuery('#enable-offline').attr('checked', 'checked');
		});
	
		jQuery('.cancel-offline-timestamp', '#offline-timestampdiv').click(function() {
			// Restore date
			jQuery('#offline-timestampdiv').slideUp('fast');
			jQuery('#offline-mm').val(jQuery('#offline-hidden_mm').val());
			jQuery('#offline-jj').val(jQuery('#offline-hidden_jj').val());
			jQuery('#offline-aa').val(jQuery('#offline-hidden_aa').val());
			jQuery('#offline-hh').val(jQuery('#offline-hidden_hh').val());
			jQuery('#offline-mn').val(jQuery('#offline-hidden_mn').val());
			
			// Restore checkbox
			jQuery('#enable-offline').attr('checked', checkbox_state);
			
			// Restore post status
			beaPostOffline.restorePreviousStatus();
			
			jQuery('#offline_timestamp').show();
			jQuery('#offline-timestampdiv').siblings('a.edit-offline-timestamp').show();
			
			beaPostOffline.updateText( false );
			
			return false;
		});
	
		jQuery('.save-offline-timestamp', '#offline-timestampdiv').click(function() {// crazyhorse - multiple ok cancels
			if (beaPostOffline.updateText(true)) {
				jQuery('#offline-timestampdiv').slideUp('fast');
				
				jQuery('#offline_timestamp').show();
				jQuery('#offline-timestampdiv').siblings('a.edit-offline-timestamp').show();
			}
			return false;
		});
	},
	
	"updateText" : function( update_status ) {
		var attemptedDate, 
			originalDate, 
			currentDate, 
			publishOn, 
			enabled = jQuery('#enable-offline').is(':checked');
			aa = jQuery('#offline-aa').val(), 
			mm = jQuery('#offline-mm').val(), 
			jj = jQuery('#offline-jj').val(), 
			hh = jQuery('#offline-hh').val(), 
			mn = jQuery('#offline-mn').val();
		
		attemptedDate = new Date(aa, mm - 1, jj, hh, mn);
		originalDate = new Date(
			jQuery('#offline-hidden_aa').val(), 
			jQuery('#offline-hidden_mm').val() - 1, 
			jQuery('#offline-hidden_jj').val(), 
			jQuery('#offline-hidden_hh').val(), 
			jQuery('#offline-hidden_mn').val());
		currentDate = new Date(
			jQuery('#offline-cur_aa').val(), 
			jQuery('#offline-cur_mm').val() - 1,
			jQuery('#offline-cur_jj').val(), 
			jQuery('#offline-cur_hh').val(), 
			jQuery('#offline-cur_mn').val()
		);
		
		if (attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn) {
			jQuery('.offline-timestamp-wrap', '#offline-timestampdiv').addClass('form-invalid');
			return false;
		} else {
			jQuery('.offline-timestamp-wrap', '#offline-timestampdiv').removeClass('form-invalid');
		}

		if ( enabled == true && attemptedDate > currentDate) {
			publishOn = beaPostL10n.schedule;
			if ( update_status == true )
				beaPostOffline.restorePreviousStatus();
		} else if ( enabled == true && attemptedDate <= currentDate ) {
			publishOn = beaPostL10n.since;
			if ( update_status == true )
				beaPostOffline.setPostStatus('offline');
		} else {
			publishOn = beaPostL10n.never;
			if ( update_status == true )
				beaPostOffline.restorePreviousStatus();
		}

		if ( publishOn == beaPostL10n.never ) {
			jQuery('#offline_timestamp').html(publishOn);
		} else {
			jQuery('#offline_timestamp').html(publishOn + ' <b>' + jQuery('option[value="' + jQuery('#offline-mm').val() + '"]', '#offline-mm').text() + ' ' + jj + ', ' + aa + ' @ ' + hh + ':' + mn + '</b> ');
		}
		
		return true;
	},
	
	/**
	 * Set new post status 
	 */
	"setPostStatus" : function( new_status ) {
		if ( previous_status == 'no' )
			previous_status = jQuery("select#post_status option:selected").val();
			
		jQuery("select#post_status option").each(function() {
			jQuery(this).attr("selected", (jQuery(this).attr("value") == new_status) );
		});
		jQuery('.save-post-status').click();
	},
	
	/**
	 * try to previous post status 
	 */
	"restorePreviousStatus" : function() {
		if ( previous_status != 'no' ) {
			beaPostOffline.setPostStatus( previous_status );
			previous_status = 'no';
		} else {
			beaPostOffline.setPostStatus(jQuery('#original_post_status').val());
		}
	},
}; 

jQuery(document).ready(function() {
	beaPostOffline.init();
}); 