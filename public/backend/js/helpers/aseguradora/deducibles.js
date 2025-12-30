
var Deducibles = Deducibles || {
	
};

var containerSADA = null;
var containerSADB = null;
var containerSBDA = null;
var containerSBDB = null;

var hotSADA = null;
var hotSADB = null;
var hotSBDA = null;
var hotSBDB = null;

;(function($, window, undefined)
{
	"use strict";

	var cargar_paquetes_aseguradora_ajax = null;
	var cargar_paquete_deducibles_ajax = null;
	var guardar_paquete_deducibles_ajax = null;

	$.extend(Deducibles, {	
		init: function(){
			this.crearHojaSADA();
			this.crearHojaSADB();
			this.crearHojaSBDA();
			this.crearHojaSBDB();
			jQuery('.select2').select2({
                minimumResultsForSearch: -1
            });
			$("#paquete").prop("disabled", true);
			$("#guardarDeduciblesBtn").prop("disabled", true);
			if($.isFunction($.fn.slimScroll)){
				$(".scrollable").each(function(i, el){
					var $this = $(el), height = attrDefault($this, 'height', $this.height());
					$this.slimScroll({
						height: height,
					});
				});
			}
        },
        crearHojaSADA: function(data){
        	containerSADA = document.getElementById('deducibleSADADiv');
        	
			hotSADA = new Handsontable(containerSADA, {
				//data: edades,
				colWidths: [70, 70, 70],
				rowHeaders: false,
				colHeaders: true,
				startRows: 0,
		  		startCols: 3,
				//minRows: 2,
				maxRows: 3,
				//fixedRowsTop: 2,
				//fixedColumnsLeft: 2,
				currentRowClassName: 'currentRow',
				currentColClassName: 'currentCol',
				autoWrapRow: true,
				//contextMenu: true,
				//manualColumnResize: true,
		  		//manualRowResize: true,
		  		maxRows: 100,
		  		mergeCells: [
		    		
				],
				colHeaders: ['Edad', 'Hombre', 'Mujer'],
				columns: [
					{type: 'numeric',format: '0'},
					{type: 'numeric',format: '$0,0.00'},
					{type: 'numeric',format: '$0,0.00'}
				],
				cells: function (row, col, prop) {
					var cellProperties = {};
					
					if (col === 0) {
						cellProperties.readOnly = true;
					}
					return cellProperties;
			    },
				//minSpareRows: 1
			});
        },
        crearHojaSADB: function(){
        	containerSADB = document.getElementById('deducibleSADBDiv');
        	
			hotSADB = new Handsontable(containerSADB, {
				//data: edades,
				colWidths: [70, 70, 70],
				rowHeaders: false,
				colHeaders: true,
				startRows: 0,
		  		startCols: 3,
				maxRows: 100,
				currentRowClassName: 'currentRow',
				currentColClassName: 'currentCol',
				autoWrapRow: true,
		  		mergeCells: [
		    		
				],
				colHeaders: ['Edad', 'Hombre', 'Mujer'],
				columns: [
					{type: 'numeric',format: '0'},
					{type: 'numeric',format: '$0,0.00'},
					{type: 'numeric',format: '$0,0.00'}
				],
				cells: function (row, col, prop) {
					var cellProperties = {};
					
					if (col === 0) {
						cellProperties.readOnly = true;
					}
					return cellProperties;
			    },
			});
        },
        crearHojaSBDA: function(){
        	containerSBDA = document.getElementById('deducibleSBDADiv');
        	
			hotSBDA = new Handsontable(containerSBDA, {
				//data: edades,
				colWidths: [70, 70, 70],
				rowHeaders: false,
				colHeaders: true,
				startRows: 0,
		  		startCols: 3,
				maxRows: 100,
				currentRowClassName: 'currentRow',
				currentColClassName: 'currentCol',
				autoWrapRow: true,
		  		mergeCells: [
		    		
				],
				colHeaders: ['Edad', 'Hombre', 'Mujer'],
				columns: [
					{type: 'numeric',format: '0'},
					{type: 'numeric',format: '$0,0.00'},
					{type: 'numeric',format: '$0,0.00'}
				],
				cells: function (row, col, prop) {
					var cellProperties = {};
					
					if (col === 0) {
						cellProperties.readOnly = true;
					}
					return cellProperties;
			    },
			});
        },
        crearHojaSBDB: function(){
        	containerSBDB = document.getElementById('deducibleSBDBDiv');
        	
			hotSBDB = new Handsontable(containerSBDB, {
				//data: edades,
				colWidths: [70, 70, 70],
				rowHeaders: false,
				colHeaders: true,
				startRows: 0,
		  		startCols: 3,
				maxRows: 100,
				currentRowClassName: 'currentRow',
				currentColClassName: 'currentCol',
				autoWrapRow: true,
		  		mergeCells: [
		    		
				],
				colHeaders: ['Edad', 'Hombre', 'Mujer'],
				columns: [
					{type: 'numeric',format: '0',readOnly: true},
					{type: 'numeric',format: '$0,0.00'},
					{type: 'numeric',format: '$0,0.00'}
				],
				cells: function (row, col, prop) {
					var cellProperties = {};
					
					if (col === 0) {
						cellProperties.readOnly = true;
					}
					return cellProperties;
			    },
			});
        },
        cargarPaquetesAseguradora: function(aseguradora){
        	if(cargar_paquetes_aseguradora_ajax){
        		cargar_paquetes_aseguradora_ajax.abort();
        	}
        	cargar_paquetes_aseguradora_ajax = $.ajax(_root_ +"aseguradora/cargarPaquetesAseguradora",{
							                        data : { aseguradora : aseguradora },
							                        cache: false,
							                        timeout: 15000,
							                        type: "post",
							                        dataType: "json",
							                        beforeSend: function(){
							                        	$("#paquete").empty();
							                        	$("#paquete").append('<option>Procesando...</option>');
							                        	$("#paquete").prop("disabled", true);
							                        	$("#aseguradora").prop("disabled", true);
							                        	$("#guardarDeduciblesBtn").attr("data-aseguradora", "-1");
							                        	$("#guardarDeduciblesBtn").attr("data-paquete", "-1");
							                        	$("#guardarDeduciblesBtn").prop("disabled", true);

							                        	Deducibles.limpiarTablas();
							                        },
							                        complete: function(){
														$("#aseguradora").prop("disabled", false);
							                        },
							                        success: function(respuesta){
							                        	cargar_paquetes_aseguradora_ajax = null;

							                        	if(respuesta.paquetes.length > 0){
							                        		$("#paquete").prop("disabled", false);
							                        		$("#paquete").empty();
							                        		$("#paquete").append("<option value='-1'>Selecciona un paquete...</option>")
							                        		for(var paquete in respuesta.paquetes){
							                        			$("#paquete").append("<option value='"+respuesta.paquetes[paquete]["valor"]+"'>"+respuesta.paquetes[paquete]["nombre"]+"</option>")
							                        		}
							                        	}
							                        	$("#aseguradora").prop("disabled", false);
							                        	//Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
							                        },
							                        error: function(data){
							                        	$("#aseguradora").prop("disabled", false);
							                        }
							                    });
        },
        cargarPaqueteDeducibles: function(aseguradora, paquete){
        	if(cargar_paquete_deducibles_ajax){
        		cargar_paquete_deducibles_ajax.abort();
        	}
        	cargar_paquete_deducibles_ajax = $.ajax(_root_ + "aseguradora/cargarPaqueteDeducibles",{
							                        data : { aseguradora : aseguradora, paquete : paquete },
							                        cache: false,
							                        timeout: 15000,
							                        type: "post",
							                        dataType: "json",
							                        beforeSend: function(){
							                        	$("#paquete").prop("disabled", true);
							                        	$("#aseguradora").prop("disabled", true);
							                        	Deducibles.limpiarTablas();
							                        },
							                        complete: function(){
														
							                        },
							                        success: function(respuesta){
							                        	cargar_paquete_deducibles_ajax = null;

						                        		$("#paquete").prop("disabled", false);
						                        		$("#aseguradora").prop("disabled", false);
						                        		
							                        	if(respuesta.paquetes["SADA"].length > 0){
							                        		hotSADA.loadData(respuesta.paquetes["SADA"]);
							                        	}
							                        	if(respuesta.paquetes["SADB"].length > 0){
							                        		hotSADB.loadData(respuesta.paquetes["SADB"]);
							                        	}
							                        	if(respuesta.paquetes["SBDA"].length > 0){
							                        		hotSBDA.loadData(respuesta.paquetes["SBDA"]);
							                        	}
							                        	if(respuesta.paquetes["SBDB"].length > 0){
							                        		hotSBDB.loadData(respuesta.paquetes["SBDB"]);
							                        	}
							                        	$("#guardarDeduciblesBtn").prop( "disabled", false);
							                        	$("#guardarDeduciblesBtn").attr("data-aseguradora", aseguradora);
							                        	$("#guardarDeduciblesBtn").attr("data-paquete", paquete);
							                        },
							                        error: function(data){
							                        	
							                        }
							                    });
        },
        guardarPaqueteDeducibles: function(aseguradora, paquete){
        	if(guardar_paquete_deducibles_ajax){
        		guardar_paquete_deducibles_ajax.abort();
        	}
        	guardar_paquete_deducibles_ajax = $.ajax(_root_ + "aseguradora/guardarPaqueteDeducibles",{
							                        data : { aseguradora : aseguradora, paquete : paquete, "SADA" : hotSADA.getData(), "SADB" : hotSADB.getData(), "SBDA": hotSBDA.getData(), "SBDB" : hotSBDB.getData() },
							                        cache: false,
							                        timeout: 15000,
							                        type: "post",
							                        dataType: "json",
							                        beforeSend: function(){
							                        	$("#aseguradora").prop("disabled", true);
							                        	$("#paquete").prop("disabled", true);
							                        	$("#guardarDeduciblesBtn").prop("disabled", true);
							                        },
							                        complete: function(){
														
							                        },
							                        success: function(respuesta){
							                        	guardar_paquete_deducibles_ajax = null;
							                        	$("#guardarDeduciblesBtn").prop( "disabled", false);
							                        	$("#aseguradora").prop( "disabled", false);
							                        	$("#paquete").prop( "disabled", false);
							                        	Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
							                        },
							                        error: function(data){
							                        	
							                        }
							                    });
        },
        limpiarTablas: function(){
        	if(hotSADA.countRows() > 0){
        		//while(hotSADA.countRows() > 0){
    			hotSADA.alter('remove_row', 0, 100);
        		//}
        		//alert(hotSADA.countRows())
        		//alert(hotSADA.countRows())
        		//hotSADA.clear()
        	}
        	if(hotSADB.countRows() > 0){
        		//hotSADB.clear()
        		hotSADB.alter('remove_row', 0, 100);
        	}
        	if(hotSBDA.countRows() > 0){
        		hotSBDA.alter('remove_row', 0, 100);
        	}
        	if(hotSBDB.countRows() > 0){
        		hotSBDB.alter('remove_row', 0, 100);
        	}
        },
    });
	$(document).delegate("#aseguradora", "change", function(event){
    	event.preventDefault();
    	
    	var aseguradora = $(this).val();
    	
    	Deducibles.cargarPaquetesAseguradora(aseguradora);
    });
    $(document).delegate("#paquete", "change", function(event){
    	event.preventDefault();
    	
    	var aseguradora = $("#aseguradora").val();
    	var paquete = $(this).val();
    	
    	Deducibles.cargarPaqueteDeducibles(aseguradora, paquete);
    });
    $(document).delegate("#guardarDeduciblesBtn", "click", function(event){
    	event.preventDefault();
    	
    	//var aseguradora = $(this).data("aseguradora");
    	//var paquete = $(this).data("paquete");
    	var aseguradora = $("#aseguradora").val();
    	var paquete = $("#paquete").val();
    	
    	Deducibles.guardarPaqueteDeducibles(aseguradora, paquete);
    });
})(jQuery, window);

$(document).ready(function(){
    Deducibles.init();
});