<!DOCTYPE html>
<html>
<head>
	<title>Participant Register</title>
	<style type="text/css">
		* {
			font-family: Arial;
		}
		button {
			cursor: pointer;
		}
	</style>
</head>
<body>
	<center>
		<h2>Participant Register</h2>
		<form id="main_form" action="javascript:void(0);">
			<table>
				<tr><td>Name</td><td>:</td><td><input required type="text" id="name" value="Ammar Faizi"></td></tr>
				<tr><td>Company Name</td><td>:</td><td><input required type="text" id="company_name" value="Tea Inside"></td></tr>
				<tr><td>Position</td><td>:</td><td><input required type="text" id="position" value="CEO/Founder"></td></tr>
				<tr><td>Company Sector</td><td>:</td><td><input required type="text" id="company_sector" value="Industry"></td></tr>
				<tr><td>Email</td><td>:</td><td><input required type="email" id="email" value="ammarfaizi2@gmail.com"></td></tr>
				<tr><td>Phone</td><td>:</td><td><input required type="text" id="phone" value="085867152777"></td></tr>
				<tr><td>Problem Description</td><td>:</td><td><textarea required id="problem_desc" style="width: 195px; height: 84px;">I am the Bone of my Sword, Steel is my Body and Fire is my Blood. I have created over a Thousand Blades, Unknown to Death, Nor known to Life. Have withstood Pain to create many Weapons Yet those Hands will never hold Anything. So, as I Pray Unlimited Blade Works</textarea></td></tr>
				<tr><td colspan="3" align="center"><img src="" id="captcha_image"></td></tr>
				<tr><td colspan="3" align="center"><button id="reload_captcha">Reload Captcha</button></td></tr>
				<tr><td colspan="3" align="center">Please enter the captcha above!</td></tr>
				<tr><td colspan="3" align="center"><input required type="text" id="captcha_input"></td></tr>
				<tr><td colspan="3" align="center"><button type="submit">Submit</button></td></tr>
			</table>
			<input type="hidden" id="_token">
		</form>
	</center>
	<script type="text/javascript">

		/**
		 * @return void
		 */
		function loadToken() {
			var ch = new XMLHttpRequest;
				ch.onreadystatechange = function () {
					if (this.readyState === 4) {
						try	{
							var res = JSON.parse(this.responseText);
							document.getElementById("_token").value = res["data"]["token"];
							var catpcha_url = "/captcha.php?token="+encodeURIComponent(res["data"]["token"]);
							document.getElementById("captcha_image").src = catpcha_url;
						} catch (e) {
							alert("An error occured:\n"+e.message);
						}
					}
				};
				ch.open("GET", "/participant_register.php?action=get_token");
				ch.send(null);
		}

		/**
		 * @return void
		 */
		function listenForm() {
			document.getElementById("main_form").addEventListener("submit", function () {
				var data = {

				};
			});
		}


		loadToken();
		listenForm();

		document.getElementById("reload_captcha").addEventListener("click", function () {
			loadToken();
		});
		
	</script>
</body>
</html>