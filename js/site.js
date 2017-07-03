function ajax() { //Ajax отправка формы
	var msg = $("#form").serialize();
	$.ajax({
		type: "POST",
		url: "../form/start_test.php",
		data: msg,
		success: function(data) {
			$("#results").html(data);
		},
		error:  function(xhr, str){
			alert("Возникла ошибка!");
		}
	});
}

jQuery.fn.notExists = function() { //Проверка на существование элемента
	return $(this).length == 0;
}

$(document).ready(function(){ //Валидация формы
	$(".send").validation(
		$(".airport").validate({
			test: "blank airport", 
			invalid: function(){
				if($(this).nextAll(".error").notExists()) {
					$(this).after('<div class="error">Введите корректное название</div>');
					$(this).nextAll(".error").delay(2000).fadeOut("slow");
					setTimeout(function () {
						$(".name").next(".error").remove();
					}, 2600);
				}
			},
			valid: function(){
				$(this).nextAll(".error").remove();
			}
		}),
		$(".date_from").validate({
			test: "blank date_from",
			invalid: function(){
				if($(this).nextAll(".error").notExists()) {
					$(this).after('<div class="error">Введите корректно дату</div>');
					$(this).nextAll(".error").delay(2000).fadeOut("slow");
					setTimeout(function () {
						$(".date_from").next(".error").remove();
					}, 2600);
				}
			},
			valid: function(){
				$(this).nextAll(".error").remove();
			}
		}),
		$(".date_to").validate({
			test: "blank", 
			invalid: function(){
				if($(this).nextAll(".error").notExists()) {
					$(this).after('<div class="error">Введите END DATE</div>');
					$(this).nextAll(".error").delay(2000).fadeOut("slow");
					setTimeout(function () {
						$(".subject").next(".error").remove();
					}, 2600);
				}
			},
			valid: function(){
				$(this).nextAll(".error").remove();
			}
		}));
});