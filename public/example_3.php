<!DOCTYPE html>
<html>
<head>
	<title>Sponsor Register</title>
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
		<a style="color:blue;text-decoration:none;" target="_blank" href="/show_db.php?table=sponsors">Show Table [sponsors]</a>
		<h2>Sponsor Register</h2>
		<form id="main_form" action="javascript:void(0);">
			<table>
				<tr><td>Company Name</td><td>:</td><td><input required type="text" id="company_name" value="Tea Inside"></td></tr>
				<tr><td>Company Sector</td><td>:</td><td><input required type="text" id="company_sector" value="Industry"></td></tr>
				<tr><td>Email</td><td>:</td><td><input required type="email" id="email_pic" value="ammarfaizi2@gmail.com"></td></tr>
				<tr><td>Phone</td><td>:</td><td><input required type="text" id="phone" value="085867152777"></td></tr>
				<tr><td>Sponsor Type</td><td>:</td>
					<td>
						<select id="sponsor_type" required>
							<option></option>
							<option value="silver">Silver</option>
							<option value="gold">Gold</option>
							<option value="gold">Platinum</option>
						</select>
					</td>
				</tr>
				<tr><td colspan="3" align="center"><img src="" id="captcha_image"></td></tr>
				<tr><td colspan="3" align="center"><button type="button" id="reload_captcha">Reload Captcha</button></td></tr>
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
				ch.open("GET", "/sponsor_register.php?action=get_token");
				ch.send(null);
		}

		/**
		 * @return void
		 */
		function listenForm() {
			document.getElementById("main_form").addEventListener("submit", function () {
				var data = {
					"company_name": document.getElementById("company_name").value,
					"company_sector": document.getElementById("company_sector").value,
					"email_pic": document.getElementById("email_pic").value,
					"phone": document.getElementById("phone").value,
					"sponsor_type": document.getElementById("sponsor_type").value,
					"captcha": document.getElementById("captcha_input").value,
				};
				var ch = new XMLHttpRequest;
					ch.onreadystatechange = function () {
						if (this.readyState === 4) {
							alert(this.responseText);
							try {
								var res = JSON.parse(this.responseText);
								if (typeof res["data"]["message"] !== "undefined") {
									alert(res["data"]["message"]);
									if (res["data"]["message"] === "register_success") {
										window.location = "?";
									}
								}
							} catch (e) {
								alert("An error occured:\n"+e.message);
							}
						}
					};
					ch.open("POST", "/sponsor_register.php?action=submit");
					ch.setRequestHeader("Authorization", "Bearer "+document.getElementById("_token").value);
					ch.send(JSON.stringify(data));
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