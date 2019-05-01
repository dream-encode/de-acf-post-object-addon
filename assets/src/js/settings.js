(function ($) {
	"use strict";

	jQuery(document).ready(function($) {		
		$('[data-toggle]').on('change', function(e) { 
			var $table = $(this).closest("TABLE.form-table"),
				isCheckbox = $(this).is(':checkbox'),
				value = $(this).val();
			
			if (isCheckbox) {
				value = $(this).is(":checked");
			}
			
			if ($(this).val() == $(this).data('toggle-value')) {
				$("#de_acfpoftao-"+$(this).data('toggle')).closest("TR").show();
			} else {
				$("#de_acfpoftao-"+$(this).data('toggle')).closest("TR").hide();
			}
		});
		
		$(".conditional-hidden").closest("TR").hide();
		
		$("SELECT.select2").select2();
		
		$("[type='checkbox']").bootstrapSwitch({
			onSwitchChange: function(e, state){
				if (state) {
					this.prop('checked', true).trigger('change');
				} else {
					this.removeProp('checked').trigger('change');
				}
			}
		});
	});
})(jQuery);