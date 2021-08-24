jQuery( document ).ready( function(){
	
	jQuery( '.faq-list li' ).each(function(){
		jQuery( this ).children( '.title_content' ).children( '.faq_content' ).slideUp();
	});

	jQuery( '.faq_title' ).click( function(){
		
		if( ( jQuery( this ).parent( 'div' ).parent( 'li' ).hasClass( 'active' ) ) == true ){
			jQuery( this ).parent( 'div' ).parent( 'li' ).removeClass( 'active' );
			jQuery( this ).parent( '.title_content' ).children( '.faq_content' ).slideUp();
		}else{
		
			jQuery( '.faq-list li' ).each( function(){
				jQuery( this ).removeClass( 'active' );
				jQuery( this ).children( '.title_content' ).children( '.faq_content' ).slideUp();
			});

			jQuery( this ).parent().children( '.faq_content' ).slideDown();
			jQuery( this ).parent().parent().addClass( 'active' );
		}
		
	});
	
	jQuery( '.cat-list li a' ).click( function(){
				
		var cat_id = jQuery( this ).parent().attr( 'id' );
		
		if( cat_id == "-1" ){
			jQuery( '.cat-list li' ).each( function(){
				jQuery( this ).children( 'a' ).removeClass( 'active' );
			});
			jQuery( this ).addClass( 'active' );
			jQuery( ".faq-list li" ).slideDown();
			return;
		}
	
		jQuery( '.cat-list li' ).each( function(){
			jQuery( this ).children( 'a' ).removeClass( 'active' );
		});
		
		jQuery( this ).addClass( 'active' );

		jQuery( '.faq-list li' ).each( function(){
		  
			jQuery( this ).removeClass( 'active' );
			jQuery( this ).children( '.title_content' ).children( '.faq_content' ).slideUp();

			if( jQuery( this ).attr( 'id' ) === cat_id ){
				jQuery( this ).slideDown();
			}else{
				jQuery( this ).slideUp();
			}
		});

	});
	
});
