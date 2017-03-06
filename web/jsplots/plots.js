var plotData = [];
var plotTotals = [];

var basicLayout = {
    autosize : false, width: 900, height: 600,
    margin: { l: 70, r: 50, t: 80, b: 80 },
    title: 'Title',
    titlefont: { family: 'Arial, sans-serif', size: 40, color: 'black' },
    xaxis: {
	title: 'Semester',
	titlefont: { family: 'Arial, sans-serif', size: 24, color: 'black' },
	tickfont: { family: 'Arial, sans-serif',  size: 16, color: 'black' },
	tickangle: 45,
    },	
    yaxis: {
	title: 'Number of TAs assigned',
	titlefont: { family: 'Arial, sans-serif', size: 24, color: 'black' },
	tickfont: { family: 'Arial, sans-serif',  size: 20, color: 'black' },
    },
};

function makeTapasPlots(data)
{
    for (var idiv in data.data) {
	var divdata = data.data[idiv];
	var plotDatum = {
            'x': data.legend,
            'y': divdata.numbers,
            'name': divdata.division + '[' + divdata.average + ']',
	    'type': '',
	    'mode': '',
	};
	plotData.push(plotDatum);
    }

    var plotTotal = {
        'x': data.legend,
        'y': data.totals,
        'name': 'Totals',
    	'type': '',
    	'mode': ''
    };
    plotTotals.push(plotTotal);

    toggleType('line');
}

function toggleType(type)
{
    // determine the layout starting from our basicLayout
    var layoutDiv = $.extend( true, {}, basicLayout );
    layoutDiv["title"] = 'Teaching Assistants per Division (Historic)';
    var layoutTot = $.extend( true, {}, basicLayout );
    layoutTot["title"] = 'Total Teaching Assistants (Historic)';

    // push the proper options
    if (type == 'line') {
	for (var d in plotData) {
	    var plotDatum = plotData[d];
	    plotDatum.type = 'scatter';
	    plotDatum.mode = 'lines';
	}
	for (var d in plotTotals) {
	    var plotTotal = plotTotals[d];
	    plotTotal.type = 'scatter';
	    plotTotal.mode = 'lines';
	}
    }
    else if (type == 'bar') {
	layoutDiv.barmode = 'stack';
	layoutTot.barmode = 'stack';
	for (var d in plotData) {
	    var plotDatum = plotData[d];
	    plotDatum.type = 'bar';
	}
	for (var d in plotTotals) {
	    var plotTotal = plotTotals[d];
	    plotTotal.type = 'bar';
	}
    }

    // here the plot is really made
    Plotly.newPlot('canvasDivisions', plotData, layoutDiv);

    layoutTot.yaxis.range = [-2.0,38];
    Plotly.newPlot('canvasTotals', plotTotals, layoutTot);
}
