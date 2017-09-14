function addMyField () {
			var telnum = parseInt($('#add_field_area').find('div.add:last').attr('id').slice(3))+1;//увеличиваем значение счетчика
			alert("Number is" + telnum+"!");
			var $content=$("select#val1").html();//grab the dropdown 
			//and draw a new row
			$('div#add_field_area').find('div.add:last').append('<div id="row'+telnum+'"><hr><tr colspan="6"><div id="add'+telnum+'" class="add"><label> №'+telnum+
			'</label><select name="val'+telnum+'" id="val" onblur="writeFieldsValues();" >'+$content+
			'</select></div></tr><tr><div class="deletebutton" onclick="deleteField('+telnum+');"></div></tr></div>');
		}
		
		function deleteField (id) {
			$('div#row'+id).remove();
		}

		function addsomeField () {
			var telnum = parseInt($("#add_field_area").find("div.add:last").attr("id").slice(3))+1;//увеличиваем значение счетчика
			
			var content=$("select#val1").html();//grab the dropdown 
			//and draw a new row
			$("#myTab").append('<tr><td><select name="val[]" id="val" onblur="writeFieldsValues();" >'+content+
			'</select></td><td><select name="to_all[]" id="all" class="services" ><option value=1>Да</option><option value=0>Нет</option></select></td><td><input type="text" value="" name="including[]" placeholder="1,2,3"/></td><td><input type="text" value="" name="excluding[]" placeholder="1,2,3"/></td></div></tr>');
		}
		function addRow () {
			//var telnum = parseInt($("#add_field_area").find("div.add:last").attr("id").slice(3))+1;//увеличиваем значение счетчика
			
			 //var content=$("select#val1").html();//grasp the dropdown 
			//and draw a new row
			
			//tbody.appendChild(row)
			$("div#add_field_area").find("#myTab").append('<tr></td><td><select name="val'+telnum+'" id="val" onblur="writeFieldsValues();" >'+content+
			'</select></td><td><input type="checkbox" name="Servicedata[]" value="all"/></td><td><input type="text" value="" name="including" placeholder="1,2,3"/></td><td id="'+telnum+
			'"><input type="text" value="" name="including" placeholder="1,2,3"/></td></tr>');
		}
		
		function writeFieldsValues () {
			var str = [];
			var tel = '';
			for(var i = 0; i<$("select#val").length; i++) {
			tel = $($("select#val")[i]).val();
				if (tel !== '') {
					str.push($($("input#values")[i]).val());
				}
			}
			$("input#values").val(str.join("|"));
		}
		function checkIt () {
			
			var value=$("#flights").attr("checked");
			if(value=='checked')
			$("input:checkbox").removeAttr("checked");
			else
			$("input:checkbox").attr("checked","checked");
		}