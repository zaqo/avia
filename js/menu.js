/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
	function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
	document.getElementById("myDropdown2").classList.remove("show");
	document.getElementById("myDropdown3").classList.remove("show");
	document.getElementById("myDropdown4").classList.remove("show");
	document.getElementById("myDropdown5").classList.remove("show");
	document.getElementById("myDropdown6").classList.remove("show");
	}

// Close the dropdown if the user clicks outside of it
	window.onclick = function(event) {
		if (!event.target.matches('.dropbtn')) {

			var dropdowns = document.getElementsByClassName("dropdown-content");
			var i;
			for (i = 0; i < dropdowns.length; i++) {
			var openDropdown = dropdowns[i];
				if (openDropdown.classList.contains('show')) {
					openDropdown.classList.remove('show');
				}
			}
		}
	
	}
	function myFunction2() {
    document.getElementById("myDropdown2").classList.toggle("show");
	document.getElementById("myDropdown").classList.remove("show");
	document.getElementById("myDropdown3").classList.remove("show");
	document.getElementById("myDropdown4").classList.remove("show");
	document.getElementById("myDropdown5").classList.remove("show");
	document.getElementById("myDropdown6").classList.remove("show");
	}
function myFunction3() {
    document.getElementById("myDropdown3").classList.toggle("show");
	document.getElementById("myDropdown2").classList.remove("show");
	document.getElementById("myDropdown").classList.remove("show");
	document.getElementById("myDropdown4").classList.remove("show");
	document.getElementById("myDropdown5").classList.remove("show");
	document.getElementById("myDropdown6").classList.remove("show");
	}
	function myFunction4() {
    document.getElementById("myDropdown4").classList.toggle("show");
	document.getElementById("myDropdown2").classList.remove("show");
	document.getElementById("myDropdown3").classList.remove("show");
	document.getElementById("myDropdown").classList.remove("show");
	document.getElementById("myDropdown5").classList.remove("show");
	document.getElementById("myDropdown6").classList.remove("show");
	}
	function myFunction5() {
    document.getElementById("myDropdown5").classList.toggle("show");
	document.getElementById("myDropdown2").classList.remove("show");
	document.getElementById("myDropdown3").classList.remove("show");
	document.getElementById("myDropdown4").classList.remove("show");
	document.getElementById("myDropdown").classList.remove("show");
	document.getElementById("myDropdown6").classList.remove("show");
	}
	function myFunction6() {
    document.getElementById("myDropdown6").classList.toggle("show");
	document.getElementById("myDropdown2").classList.remove("show");
	document.getElementById("myDropdown3").classList.remove("show");
	document.getElementById("myDropdown4").classList.remove("show");
	document.getElementById("myDropdown5").classList.remove("show");
	document.getElementById("myDropdown").classList.remove("show");
	}
// Close the dropdown if the user clicks outside of it
	window.onclick = function(event) {
		if (!event.target.matches('.dropbtn')) {

			var dropdowns = document.getElementsByClassName("dropdown-content");
			var i;
			for (i = 0; i < dropdowns.length; i++) {
			var openDropdown = dropdowns[i];
				if (openDropdown.classList.contains('show')) {
					openDropdown.classList.remove('show');
				}
			}
		}
	
	}