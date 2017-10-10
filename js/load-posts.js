/** 
 * load wordpress posts through ajax 
 * @require: JQuery 
 * @author: Rabiu Mustapha
 * @package: Abbey Load More Posts 
 * @version: 0.1 
 */
(function($){
	
	//bail early if we dont have a valid object storage for the plugin //
	if( !abbeyAjaxLoadPosts || typeof abbeyAjaxLoadPosts == "undefined" ) return false;

	//initialize variables //
	var postIsLoading, postLoadedCount, postPagesLoaded, currentPostPage, maxPostPage; 
	
	// simple indicator to determine when Ajax request have started or ended //
	morePostIsLoading = false;

	// Copy the current page we are from the global abbeyAjaxLoadPosts object //
	currentPostPage = parseInt( abbeyAjaxLoadPosts.query_vars.paged );

	//copy the total pages in the archive from the global abbeyAjaxLoadPosts object //
	maxPostPage = parseInt( abbeyAjaxLoadPosts.query_vars.max_num_pages );
	
	//simple function to show/hide the load more button, check below to see how it works//
	if( maxPostPage > currentPostPage ) showHideLoadButton();

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

			// popup main content //
			popupContent = popup.content;

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

					// convert reponse to a jQuery HTML object //
					responseData = $($.parseHTML( data ));

					if( responseData.hasClass("ajax-error") ){
						popupContent.addClass("warning").find(".popup-body").html(data);
					}
					else{
						currentPostPage++; //increment the currentPostPage //
						popup.close(); //close the popup //
						resultDiv.append( data );
						showHideLoadButton(); //hide or show the load more button if there is or no more posts //
						abbeyAjaxLoadPosts.query_vars.paged = currentPostPage; //set the paged setting to the increment curent page for the next query //
					}
				},
				error: function ( xhr, status, message){
					popupContent.addClass("error").find(".popup-body").html("Error " +status+": "+message);
				}, 
				beforeSend: function( xhr ){
					popup.open({
						items: {
							type: "inline",
							src: "<div class='mini-popup'><div class='popup-body'><span class='fa fa-spinner fa-spin fa-fw'></span> Loading . . .</div></div>" 
						}, 
						mainClass: 'no-bg'
					});

					morePostIsLoading = true;

				}, 
				complete: function (  xhr ){
					morePostIsLoading = false;
				}

			});//end $.ajax //
		}
	}); //end on click .load-more-btn //
	
		

	

	function showHideLoadButton(){
			var loadBtn = $( ".load-more-btn" ); 
			if( loadBtn.length < 1 ){
				$(".archive-content .navigation").append('<div class="load-more-btn">'+abbeyAjaxLoadPosts.btn_text+'</div>');
			}
			else{
				//check if we have load all pages or we are at the last page //
				if( currentPostPage >= maxPostPage ) loadBtn.remove();
			}
	}//end function showHideLoadButton //

	/**
	 * Closure to run our slick carousel lazyloading of post 
	 * post are queried from the db when the slick slide next button is clicked 
	 * post are loaded via AJAX 
	 */
	$(function(){
		// variable containers for the slick slide lazyloading //
		var currentSlide, postsSlides, nextpostIsLoading, slideTemplate, excludeID;

		//element where the slick slide is initiated, this is slick carousel where loaded posts will be added //
		postsSlides = $( ".posts-slides" );

		//set default current slide, since we are on the first slide, set to 0 //
		currentSlide = 0;

		//indicator to know if an AJAX request is still on or finished //
		nextpostIsLoading = false;

		excludeID = []; //arrays of posts to exclude, will be sent together to AJAX  //
		
		//attach a click event to the slic add button in our slide //
		$(document).on("click", ".archive-post-slide .slick-add", function(ev){
			
			ev.preventDefault(); //prevent the default event of this element //

			// our variables //
			var _this, postData, query_vars, ajax, newSlide, i; 

			_this = $(this); //clone the slick add button jQuery button //

			i = excludeID.length; //get the count of the excludeID array //
			
			//if its less than 1, increment  //
			if( parseInt(i) < 1 ) i++;

			/**
			 * Add the value of data-post-id to our excludeID array 
			 * the ID of the current post in the slide is stored in the attribute data-post-id
			 * this ID will be included in our array so that the next AJAX request does not query this post
			 */
			excludeID[i] = _this.data( "postId" ); //add this post to list of post to exclude for next query//

			//kind of making sure the currentSlide indicator is up to date //
			if(currentSlide < 1) currentSlide = postsSlides.slick('slickCurrentSlide');
			
			// increment our global variable currentSlide, continue below to know why we do this //
			currentSlide++;

			// a template to load while our AJAX request is still querying post //
			slideTemplate = '<aside class="archive-post-slide"><div class="post-lazyload"><h4><span class="fa fa-spinner fa-spin fa-fw"></span><span class="loading-message">Loading post ...'+currentSlide+' </span></h4></div></aside>';

	  		/** 
	  		 * We will only 5 posts in our carousel, so we check the currentSlide to know if we should load AJAX
	  		 * or we should just navigate to the next post 
	  		 */
	  		if( currentSlide < 5 ){
	  			 postsSlides.slick('slickAdd', slideTemplate);
	  			 postsSlides.slick('slickGoTo', currentSlide, false);
	  			 newSlide = $("[data-slick-index='" +currentSlide+ "']");
	  			 
	  			 /**
	  			  * Data that will be sent to wp admin ajax.php
	  			  * this data will contain:
	  			  *		action: an action name to be sent with the request, compulsory for wp ajax actions 
	  			  * 	nonce: just a simple validation/authentication 
	  			  *		posts_to_exclude: an array of posts to exclude for our request 
	  			  * 	slide_posts_query_vars: array of query vars to use to load the next post 
	  			  */
	  			 postData = {
	  			 	action: "abbey_archive_slide_posts", 
	  			 	nonce: abbeyAjaxLoadPosts.load_posts_nonce, 
	  			 	posts_to_exclude: excludeID, 
	  			 	slide_posts_query_vars: abbeyAjaxLoadPosts.query_vars
	  			 };

	  			 ajax = $.ajax({
	  			 	url: abbeyAjaxLoadPosts.ajax_url, 
	  			 	data: postData,
	  			 	type: "POST",
	  			 	beforeSend: function( xhr ){
	  			 		newSlide.find(".loading-message").text("Fetching posts....")
	  			 	}, 
	  			 	success: function(data){
	  			 		newSlide.html(data);
	  			 	}
	  			 });

	  		}
	  		else{
	  			postsSlides.slick("slickNext"); //just go to the next slide without loading any AJAX request //
	  		}

		}); //end .click for .slick-add //

	}); //end closure //
		
		
	

})(jQuery);