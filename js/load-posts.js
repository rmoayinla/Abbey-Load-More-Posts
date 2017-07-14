/** 
 * load wordpress posts through ajax 
 * @require: JQuery 
 * @author: Rabiu Mustapha
 * @package: Abbey Load More Posts 
 * @version: 0.1 
 */
(function($){
	
	if( abbeyAjaxLoadPosts ){

		//initialize variables //
		var postIsLoading, postLoadedCount, postPagesLoaded, currentPostPage, maxPostPage; 
		
		// simple indicator to determine when Ajax request have started or ended //
		postIsLoading = false;

		// Copy the current page we are from the global abbeyAjaxLoadPosts object //
		currentPostPage = parseInt( abbeyAjaxLoadPosts.query_vars.paged );

		//copy the total pages in the archive from the global abbeyAjaxLoadPosts object //
		maxPostPage = parseInt( abbeyAjaxLoadPosts.query_vars.max_num_pages );
		
		//simple function to show/hide the load more button, check below to see how it works//
		showHideLoadButton();

		/**
		 * Bind the AJAX request to the load more button 
		 * This same event can be bind to the scroll event 
		 * @since: 0.1 
		 */
		$(document).on("click", ".load-more-btn", function(ev){

			//the element can be a link, so prevent the default action //
			ev.preventDefault(); 

			// initialize our variables //
			var postsToLoadData, popup, _this, responseData, popupContent, resultDiv; 
			
			/**
			 * The datas that will be sent with our AJAX request 
			 * this datas will be used in php for displaying/querying the post 
			 * query_vars: contains the vars to query the next post e.g. paged, posts_per_page e.tc.
			 * action: required if using wordpress admin-ajax.php to handle AJAX 
			 * nonce: a simple verification to be sure the request is coming from the right source 
			 */
			postsToLoadData = {
				query_vars: abbeyAjaxLoadPosts.query_vars, // the query vars //
				action: "abbey_load_more_posts", // for wp admin-ajax //
				nonce: abbeyAjaxLoadPosts.load_posts_nonce //wp nonce for verification //
			}; 

			//where the results i.e. queried posts will be loaded //
			resultDiv = $( ".archive-posts" );

			/**
			 * Our AJAX results will be displayed in popup so we need magnific popup 
			 * Check if magnific popup is present and  initiate AJAX request 
			 */
			if( $.magnificPopup ){

				//copy the popup instance //
				popup = $.magnificPopup.instance;

				// copy the Jquery $this object which represent the load-more button //
				_this = $(this);
					
				/**
				 * Initiate our AJAx request with settings
				 * this request is handled by jQuery $.aJax API 
				 */
				$.ajax({
					url: abbeyAjaxLoadPosts.ajax_url, //url to send request to http://..../wp-admin/admin-ajax.php //
					data: postsToLoadData, // datas to be sent with request //
					type: "POST", //request method //
					success: 	function( data ){ // on success //

						// convert to reponse to a jQuery HTML object //
						responseData = $($.parseHTML( data ));

						// popup main content //
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
				$(".archive-content .navigation").append('<div class="load-more-btn">'+abbeyAjaxLoadPosts.btn_text+'</div>');
			}
			else{
				if( currentPostPage >= maxPostPage ) loadBtn.remove();
			}
		}
		
	}

})(jQuery);