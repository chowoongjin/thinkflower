$(function(){

	$(".description").each(function(){

		var fullText = $(this).html();

		$(this).data("full", fullText);

		$(this).append(' <span class="more">더보기</span>');

	});

	$(document).on("click",".description .more",function(){

		var box = $(this).closest(".description");

		if(box.hasClass("open")){

			box.removeClass("open");
			$(this).text("더보기");

		}else{

			box.addClass("open");
			$(this).text("닫기");

		}

	});

	$(document).on("click",".more",function(){

		var box = $(this).closest(".description");
		var fullText = box.data("full");

		box.html(fullText);

	});
	
	$(document).on("click","#faq ul li .q",function(){
		
		var li = $(this).parent();
	
		if(li.hasClass("active")){
			li.removeClass("active");
		}else{
			$("#faq ul li").removeClass("active");
			li.addClass("active");
		}
	
	});

});