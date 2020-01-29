
  $(document).ready(function(e) {
	  
	  $(".menuIcon").click(function(e) {
       $(".menu_outer").fadeIn(); 
	    $("body").addClass("hidden");
	   
    }); 
	$(".crossmenu a").click(function(e) {
       $(".menu_outer").fadeOut(); 
	   $("body").removeClass("hidden");
	   	 return false ;
    }); 
 	 
		
$(document).on('each', 'select', function(index, element){
    var selectVal = $(this).find(":selected").text();
    $(this).parent().find("span").text(selectVal);			 
});

$(document).on('change', 'select', function(){
    var srchFiltr_val = $(this).find(":selected").text();
    $(this).parent().find("span").text(srchFiltr_val);
    $(this).parent().removeClass("focus");
});
		
		$(".checkBoxouter label").click(function(e) {
			 $(".checkBoxouter label").removeClass('select');
             $(this).addClass('select');
			
        });
		
		$(".checkBoxouter2 label").click(function(e) {
			 $(".checkBoxouter2 label").removeClass('select');
             $(this).addClass('select');
			
        });
		
		$(".checkBoxouter3 label").click(function(e) {
			 $(".checkBoxouter3 label").removeClass('select');
             $(this).addClass('select');
			
        });
		
		
		
		$(".checkCardCheck label").click(function(e) {
			$(this).toggleClass('select');
 			
        });
        
        $(document).on('click', '.createRecord_caritage_check label input:checkbox', function(){
            if($(this).prop("checked") == true){
             $(this).parent().addClass('select');
             }else{
             $(this).parent().removeClass('select');
             }
         });
		
 
 
$("#files").change(function(){
	$(".browsFieldsInner").hide();
readURL(this);
});

		
		 
		
		$(".contUs_tbsTitle ul li a").click(function() {
			 var mapTitleId = $(this).attr("href");
 			$(".contUs_tbsTitle ul li a").removeClass("active");
			$(this).addClass("active");
 			$(".contUs_tbsShow").hide();
			$(mapTitleId).show();
 			return false;
         });
		 
		  if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $(function() {
            $('.captionWrapper.valign').css({
                top: '0px'
            });

            $('.parallaxLetter').css({
                display: 'none'
            });
        });


    }
    else{
		 
        $(window).stellar({
            responsive: true,
            horizontalOffset: 0,
            horizontalScrolling:false
        });
    }
	
	$(".mainscrollBtn").click(function() {
        var body     =  $("html, body");
		 body.animate({scrollTop:0},800);
     });
		 
		 
		 $(".customFieldSelect select").focus(function(e) {
            $(this).parent().addClass("focus");
        });
		$(".customFieldSelect select").focusout(function(e) {
            $(this).parent().removeClass("focus");
        });
 	 
	 
	 
	  $(".hdr_srchBar_field select").focus(function(e) {
            $(this).parent().addClass("focus");
        });
		$(".hdr_srchBar_field select").focusout(function(e) {
            $(this).parent().removeClass("focus");
        });
		
		
		$(".navSearch_icon").click(function() {
           $(".hdr_srchBar").addClass("navSrch_show");
        });
		
		$(".contant, .headerNav_topOut, .logo").click(function() {
           $(".hdr_srchBar").removeClass("navSrch_show");
        });
		
 	 
 
  });
 
 
 
 
	
	
 
   
   $(window).load(function(e) {
		$("body").addClass("pageLoded");
	});
	
	
	 


 	function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#showPic').show().attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

	
 
 
 
 
   
 function clearText(field){
 	if(field.defaultValue == field.value){
 		field.value = "";
 	}else if(field.value == ""){
		field.value = field.defaultValue;
	}
}

 




			

 