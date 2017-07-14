// load wordpress posts through ajax //
(function($){
	if( abbeyAjaxLoadPosts ){
		var postIsLoading, postLoadedCount, postPagesLoaded, currentPostPage, maxPostPage; 
		postIsLoading = false;
		currentPostPage = parseInt( abbeyAjaxLoadPosts.query_vars.paged );
		maxPostPage = parseInt( abbeyAjaxLoadPosts.query_vars.max_num_pages );
		
		showHideLoadButton();

		$(document).on("click", ".load-more-btn", function(ev){
			var postsToLoadData, popup, _this, responseData, popupContent, resultDiv; 
			
			postsToLoadData = {
				query_vars: abbeyAjaxLoadPosts.query_vars, 
				action: "abbey_load_more_posts",
				nonce: abbeyAjaxLoadPosts.load_posts_nonce
			}; 

			resultDiv = $( ".archive-posts" );

			if( $.magnificPopup ){
				popup = $.magnificPopup.instance;

				_this = $(this);
					
				$.ajax({
					url: abbeyAjaxLoadPosts.ajax_url,
					data: postsToLoadData, 
					type: "POST",
					success: 	function( data ){
						responseData = $($.parseHTML( data ));
						popupContent = popup.content;
						if( responseData.hasClass("ajax-error") ){
							popupContent.addClass("warning").find(".popup-body").html(data);
						}
						else{
							currentPostPage++;
							popup.close();
							resultDiv.append( data );
							showHideLoadButton();
							abbeyAjaxLoadPosts.query_vars.paged = currentPostPage;
						}
					},
					error: function ( xhr, status, message){
						alert( status + ": "+message );
					}, 
					beforeSend: function( xhr ){
						popup.open({
							items: {
								type: "inline",
								src: "<div class='mini-popup'><div class='popup-body'><span class='fa fa-spinner fa-spin fa-fw'></span> Loading . . .</div></div>" 
							}, 
							mainClass: 'no-bg'
						});

					}, 
					complete: function (  xhr ){
						
					}

				});
			}
		}); //end on click .load-more-btn //
		
		function showHideLoadButton(){
			var loadBtn = $( ".load-more-btn" ); 
			if( loadBtn.length < 1 ){
				$(".archive-content .pagination").after('<div class="load-more-btn">'+abbeyAjaxLoadPosts.btn_text+'</div>');
			}
			else{
				if( currentPostPage >= maxPostPage ) loadBtn.remove();
			}
		}
		
	}

})(jQuery);