(function($){
	// @el suggest btn to attach listening event to
	Suggestion = function(el,conf){
		// protected
		var self = this;

		var editpath = BitSystem.urls.suggestion+'edit.php?';

		// call back binding lifted from jQuery TOOLS http://flowplayer.org/tools/
        // generic binding function
        function bind(name, fn) {
            $(self).bind(name, function(e, args)  {
                if (fn && fn.call(this) === false && args) {
                    args.proceed = false;
                }
            });
            return self;
        }

        // bind all callbacks from configuration
        $.each(conf, function(name, fn) {
            if ($.isFunction(fn)) { bind(name, fn); }
        });

        // public
        $.extend(self, {
			// load form
			editSuggestion: function(){
				// ajax in the form
				var fn = function(rslt, textStatus){
					$(conf.dialogbox).html( rslt ).show();
					self.addClickHandlers();
					$(self).trigger( "onEditSuggestion" );
				}
				$.get( editpath, null, fn, 'html' );
			},

			// submit form
			storeSuggestion: function(){
				var $f = $(conf.form);
				var path = editpath + $f.serialize() + "&save_suggestion=y";
				var fn = function(rslt, textStatus){
					var el = $(conf.dialogbox);
					el.html(rslt);
					// there could be an error on the page
					if( $( conf.form ).length > 0 ){
						self.addClickHandlers();
					}else{
						$(self).trigger( "onStoreSuccess" );
					}
				}
				$.post( path, null, fn, 'html' );
			},

			// cancel
			cancelEditSuggestion: function(){
				$(conf.dialogbox).hide();
				$(self).trigger( "onCancelEditSuggestion" );
			},

			// ui
			addClickHandlers: function(){
				var el = $( conf.form );
				el.find( 'input[name="'+conf.savebtn+'"]' ).click( self.storeSuggestion );
				el.find( 'input[name="'+conf.cancelbtn+'"]' ).click( self.cancelEditSuggestion );
			},
 
			// event handler registration
			onStoreSuccess: function(fn) {
                return bind("onStoreSuccess", fn);
            },
			onEditSuggestion: function(fn) {
                return bind("onEditSuggestion", fn);
            },
			onCancelEditSuggestion: function(fn) {
                return bind("onCancelEditSuggestion", fn);
            }
		});

		function init(){
			el.click( self.editSuggestion );
			return self;
		}

		init();
	}
	$.fn.suggestion = function(conf){
        var el = this.eq(typeof conf == 'number' ? conf : 0).data("suggestion");
        if (el) { return el; }

		var opts = {
			// ui and layout
			dialogbox:'#suggestdialog',
			form:'#edit_suggestion',
			savebtn:'save_suggestion',
			cancelbtn:'cancel_suggestion',

			// callback handlers
			onStoreSucess:null,
			onEditSuggestion:null,
			onCancelEditSuggestion:null,

			// api
			api:false
		}

        // set options - merge options passed in (conf) with defaults (opts)
        $.extend(opts, conf);

        this.each( function(){
            el = new Suggestion( $(this), opts );
            $(this).data("suggestion", el);
        });

        return opts.api ? el: this;
	}
})(jQuery);
