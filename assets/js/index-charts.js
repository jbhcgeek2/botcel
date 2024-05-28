'use strict';

/* Chart.js docs: https://www.chartjs.org/ */

window.chartColors = {
	green: '#75c181',
	gray: '#a9b5c9',
	text: '#252930',
	border: '#e7e9ed'
};

/* Random number generator for demo purpose */
var randomDataPoint = function(){ return Math.round(Math.random()*10000)};


//Chart.js Line Chart Example 
let datos = new FormData();
datos.append('getVentasWeek','yes');

let envio = new XMLHttpRequest();
envio.open('POST','../includes/indexOperations.php',false);
envio.send(datos);

let res = JSON.parse(envio.responseText);
console.log(res);
// console.log(res.actual['sabado']);
// console.log(res.datoSemActual);

let lunesActual = res.actual['lunes'];
let valorLunesActual = res.datoSemActual[lunesActual];
let martesActual = res.actual['martes'];
let valorMartesActual = res.datoSemActual[martesActual];
let miercolesActual = res.actual['miercoles'];
let valorMiercolesActual = res.datoSemActual[miercolesActual];
let juevesActual = res.actual['jueves'];
let valorJuevesActual = res.datoSemActual[juevesActual];
let viernesActual = res.actual['viernes'];
let valorViernesActual = res.datoSemActual[viernesActual];
let sabadoActual = res.actual['sabado'];
let valorSabadoActual = res.datoSemActual[sabadoActual];
let domingoActual = res.actual['domingo'];
let valorDomingoActual = res.datoSemActual[domingoActual];

let lunesPasado = res.pasada['lunes'];
let valorLunesPasado = res.datoSemPasada[lunesPasado];
let martesPasado = res.pasada['martes'];
let valorMartesPasado = res.datoSemPasada[martesPasado];
let miercolesPasado = res.pasada['miercoles'];
let valorMiercolesPasado = res.datoSemPasada[miercolesPasado];
let juevesPasado = res.pasada['jueves'];
let valorJuevesPasado = res.datoSemPasada[juevesPasado];
let viernesPasado = res.pasada['viernes'];
let valorViernesPasado = res.datoSemPasada[viernesPasado];
let sabadoPasado = res.pasada['sabado'];
let valorSabadoPasado = res.datoSemPasada[sabadoPasado];
let domingoPasado = res.pasada['domingo'];
let valorDomingoPasado = res.datoSemPasada[domingoPasado];



var lineChartConfig = {
	type: 'line',

	data: {
		labels: ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'],
		datasets: [{
			label: 'Semana Actual',
			fill: false,
			backgroundColor: window.chartColors.green,
			borderColor: window.chartColors.green,
			data: [
				valorLunesActual,
				valorMartesActual,
				valorMiercolesActual,
				valorJuevesActual,
				valorViernesActual,
				valorSabadoActual,
				valorDomingoActual
			],
		}, {
			label: 'Semana Pasada',
		    borderDash: [3, 5],
			backgroundColor: window.chartColors.gray,
			borderColor: window.chartColors.gray,
			data: [
				valorLunesPasado,
				valorMartesPasado,
				valorMiercolesPasado,
				valorJuevesPasado,
				valorViernesPasado,
				valorSabadoPasado,
				valorDomingoPasado
			],
			fill: false,
		}]
	},
	options: {
		responsive: true,	
		aspectRatio: 1.5,
		
		legend: {
			display: true,
			position: 'bottom',
			align: 'end',
		},
		
		title: {
			display: true,
			text: 'Semana Actual VS Semana Paasada',
			
		}, 
		tooltips: {
			mode: 'index',
			intersect: false,
			titleMarginBottom: 10,
			bodySpacing: 10,
			xPadding: 16,
			yPadding: 16,
			borderColor: window.chartColors.border,
			borderWidth: 1,
			backgroundColor: '#fff',
			bodyFontColor: window.chartColors.text,
			titleFontColor: window.chartColors.text,

            callbacks: {
	            //Ref: https://stackoverflow.com/questions/38800226/chart-js-add-commas-to-tooltip-and-y-axis
                label: function(tooltipItem, data) {
	                if (parseInt(tooltipItem.value) >= 1000) {
                        return "$" + tooltipItem.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    } else {
	                    return '$' + tooltipItem.value;
                    }
                }
            },

		},
		hover: {
			mode: 'nearest',
			intersect: true
		},
		scales: {
			xAxes: [{
				display: true,
				gridLines: {
					drawBorder: false,
					color: window.chartColors.border,
				},
				scaleLabel: {
					display: false,
				
				}
			}],
			yAxes: [{
				display: true,
				gridLines: {
					drawBorder: false,
					color: window.chartColors.border,
				},
				scaleLabel: {
					display: false,
				},
				ticks: {
		            beginAtZero: true,
		            userCallback: function(value, index, values) {
		                return '$' + value.toLocaleString();   //Ref: https://stackoverflow.com/questions/38800226/chart-js-add-commas-to-tooltip-and-y-axis
		            }
		        },
			}]
		}
	}
};



// Chart.js Bar Chart Example 
// solicitamos los datos de articulos mas vendidos

// var barChartConfig = {
// 	type: 'polarArea',

// 	data: {
// 		labels: [name1, name2, name3, name4, name5, name6, name7],
// 		datasets: [{
// 			label: 'Productos',
// 			backgroundColor: window.chartColors.green,
// 			borderColor: window.chartColors.green,
// 			borderWidth: 1,
// 			maxBarThickness: 16,
			
// 			data: [
// 				23,
// 				45,
// 				76,
// 				75,
// 				62,
// 				37,
// 				83
// 			]
// 		}]
// 	},
// 	options: {
// 		responsive: true,
// 		aspectRatio: 1.5,
// 		legend: {
// 			position: 'bottom',
// 			align: 'end',
// 		},
// 		title: {
// 			display: true,
// 			text: 'Chart.js Bar Chart Example'
// 		},
// 		tooltips: {
// 			mode: 'index',
// 			intersect: false,
// 			titleMarginBottom: 10,
// 			bodySpacing: 10,
// 			xPadding: 16,
// 			yPadding: 16,
// 			borderColor: window.chartColors.border,
// 			borderWidth: 1,
// 			backgroundColor: '#fff',
// 			bodyFontColor: window.chartColors.text,
// 			titleFontColor: window.chartColors.text,

// 		},
// 		scales: {
// 			xAxes: [{
// 				display: true,
// 				gridLines: {
// 					drawBorder: false,
// 					color: window.chartColors.border,
// 				},

// 			}],
// 			yAxes: [{
// 				display: true,
// 				gridLines: {
// 					drawBorder: false,
// 					color: window.chartColors.borders,
// 				},

				
// 			}]
// 		}
		
// 	}
// }







// Generate charts on load
window.addEventListener('load', function(){
	
	var lineChart = document.getElementById('canvas-linechart').getContext('2d');
	window.myLine = new Chart(lineChart, lineChartConfig);
	
	// var barChart = document.getElementById('canvas-barchart').getContext('2d');
	// window.myBar = new Chart(barChart, barChartConfig);
	

});	
	
