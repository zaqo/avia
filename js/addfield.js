
		function addMyField () {
			var telnum = parseInt($('#add_field_area').find('div.add:last').attr('id').slice(3))+1;
			var $content=$("select#val1").html();
			$('div#add_field_area').find('div.add:last').append('<div id="field'+telnum+'"><hr><tr><div id="add'+telnum+'" class="add"><label> №'+telnum+
			'</label><select name="val'+telnum+'" id="val" onblur="writeFieldsValues();" >'+$content+
			'</select></div></tr><tr><div class="deletebutton" onclick="deleteField('+telnum+');"></div></tr></div>');
		}
		function addMyRow () {
			var telnum = parseInt($('#add_field_area').find('div.add:last').attr('id').slice(3))+1;
			var $content=$("select#val1").html();
			$('#tbody').append('<div id="field'+telnum+'"><hr><div id="add'+telnum+'" class="add"><label> #'+telnum+
			'</label><select name="val[]" id="val" onblur="writeFieldsValues();" >'+$content+
			'</select><div class="deletebutton_bundle" onclick="deleteField('+telnum+');"></div><hr></div>');
		}
		function addMyDiv () {
			var telnum = parseInt($('#add_field_area').find('div.add:last').attr('id').slice(3))+1;
			var $content=$("select#val1").html();
			$('#add_field_area').append('<div id="field'+telnum+'"><hr><div id="add'+telnum+'" class="row add"><div class="input-group-prepend"><div class="input-group-text">#'+telnum+
			'</div></div><div class="col"><input type="number" name="qt[]" class="form-control"></div>'+
			'<div class="col"><select name="val[]" id="val'+telnum+'" class="custom-select d-block w-100" onblur="writeFieldsValues();" >'+$content+
			'</select></div><div class=" deletebutton_bundle" onclick="deleteField('+telnum+');"></div><hr></div>');
		}
		function deleteField (id) {
			$('div#field'+id).remove();
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
			