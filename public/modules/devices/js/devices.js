$(document).ready(function(){




 $('#day-scan').datetimepicker({
   format : 'YYYY-MM-DD HH:mm'
	});
	var id_scan = 0;

	infoserver = function(id_server){
			return $.ajax({
			url: "/server/"+id_server,
			headers: {
				'GRANADA-TOKEN':readCookie('token'),
			},
			type: "get",
			dataType: "json",
			error: function(xhr, status, error) {
				new PNotify({
					title: 'Oh No!',
					text: xhr.responseText,
					type: 'error',
					styling: 'bootstrap3'
				});
				var err = eval("(" + xhr.responseText + ")");
				console.log(err);
			}
		});
	}


	scripts = function(){
		 	return $.ajax({
			url: "/scripts/list",
			headers: {
				'GRANADA-TOKEN':readCookie('token'),
			},
			type: "get",
			dataType: "json",
			error: function(xhr, status, error) {
				new PNotify({
					title: 'Oh No!',
					text: xhr.responseText,
					type: 'error',
					styling: 'bootstrap3'
				});
				var err = eval("(" + xhr.responseText + ")");
				console.log(err);
			}
		});
	}


	$('#datatable').on("click", ".edit_server", function() {
		id_server = $(this).data("id");
		$.when( infoserver(id_server), scripts() ).done(function ( is, sc ) {
			is = is[0];
			sc = sc[0];
			if(is.response == true && sc.response == true){
				$("#scripts").html("");
				active_scripts = is.result[0].scripts.split(",");
					$.each( sc.result, function( index, value ){
						if($.inArray(value.name,active_scripts) != -1){
							$("#scripts").append('<div class="checkbox"><label><input type="checkbox" checked name="'+value.name+'" value="'+value.id+'">'+value.name+'</label></div>');
						}else{
						$("#scripts").append('<div class="checkbox"><label><input type="checkbox" name="'+value.name+'" value="'+value.id+'">'+value.name+'</label></div>');	
						}
					});
				$("#server-ip-domain").val(is.result[0].ip_domain);
				$("#server-name").val(is.result[0].name);
				$("#add-new-server-modal").modal('show');
			}else{
				if(is.response == false){
					new PNotify({
						title: 'Error server',
						text: data.message,
						styling: 'bootstrap3'
					});
				}
				if(sc.response == false){
					new PNotify({
						title: 'Error server',
						text: data.message,
						styling: 'bootstrap3'
					});
				}
			}	
		});
	});

	$('#datatable').on("click", ".delete_audit", function() {
		
	});



	$("#add-device").click(function(){

		$("#server-ip-domain").val("");
		$("#servers").html("");
		$("#day-scan").val(moment().format('YYYY-MM-DD HH:mm'));
		
		id_scan = 0;
		$.ajax({
			url: "/scripts/list/Nmap",
			headers: {
				'GRANADA-TOKEN':readCookie('token'),
			},
			type: "get",
			dataType: "json",
			success: function(data) {
				if(data.response==true){

					$.each( data.result, function( index, value ){
						$("#servers").append('<div class="checkbox"><label><input type="checkbox" value="'+value.id_server+'">'+value.name_server+'('+value.ip_domain+')</label></div>');
					});
					console.log(data);
					$("#add-new-device-modal").modal('show')


				}else{
					new PNotify({
						title: 'Error Audit',
						text: data.message,
						styling: 'bootstrap3'
					});
				}
			},
			error: function(xhr, status, error) {
				new PNotify({
					title: 'Oh No!',
					text: xhr.responseText,
					type: 'error',
					styling: 'bootstrap3'
				});
				var err = eval("(" + xhr.responseText + ")");
				console.log(err);
			}
		});

	});



	$('#add-new-device-modal').on("click", "#save-device", function() {
		
		var servers_vals = [];
		
		$('#servers input:checkbox:checked').each(function(index) {
			servers_vals.push($(this).val());
		});
		var server_ip_domain = $("#server-ip-domain").val();
		var day_scan = $("#day-scan").val();
		
		var data = {
			'day_scan':day_scan,
			'ip_domain':server_ip_domain,
			'servers':servers_vals,
		}

		if(id_scan == 0){
			url = "/device/new";
			type = "POST"

		}else{
			url = "/device/update/"+id_scan;
			type = "PUT"
		}


		$.ajax({
			url: url,
			headers: {
				'GRANADA-TOKEN':readCookie('token'),
				'audit':readCookie('audit'),
			},
			type: type,
			data:data,
			dataType: "json",
			success: function(data) {
				if(data.response==true){
					new PNotify({
						title: 'Device',
						text: data.message,
						type: 'success',
						styling: 'bootstrap3'
					});
					var oTable = $('#datatable').dataTable();
					if(id_scan == 0){
						oTable.fnAddData( [
							server_ip_domain,
							day_scan,
							'',
							'<button type="button" class="btn btn-default select_audit" data-id="'+data.result[0].id+'">Details</button><button type="button" class="btn btn-danger delete_audit" data-id="'+data.result[0].id+'">eliminar</button>'] ); 
						$("#add-new-device-modal").modal('hide')

					}else{
						var nRow = $("button[data-id="+id_server+"]").parent().parent('tr')[0];
							oTable.fnUpdate(data.result[0].name, nRow, 0  ); // Single cell
							oTable.fnUpdate(data.result[0].ip_domain, nRow, 1  ); // Single cell*/
							oTable.fnUpdate(data.result[0].scripts, nRow, 2 ); // Single cell*/
							$("#add-new-device-modal").modal('hide');
					}
				}else{
					new PNotify({
						title: 'Error device',
						text: data.message,
						styling: 'bootstrap3'
					});
				}
			},
			error: function(xhr, status, error) {
				new PNotify({
					title: 'Oh No!',
					text: xhr.responseText,
					type: 'error',
					styling: 'bootstrap3'
				});
				var err = eval("(" + xhr.responseText + ")");
				console.log(err);
			}
		});


	});
});