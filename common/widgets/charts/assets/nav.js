function onClickNavChart(element) {
	const chartId = element.dataset.chartId;
	const seriesData = JSON.parse(element.dataset.seriesData);
	const toggleChart = element.dataset.chartToggle;
	const seriesName = element.dataset.seriesName;
	updateChartsSeries(chartId, seriesData);
	updateChartTitles(chartId, seriesName);
	if (toggleChart) {
		highlightSeries(toggleChart, seriesName);
	}
}


function updateChartsSeries(chartId, data) {
	ApexCharts.exec(chartId, 'updateSeries', data, true);
}

function highlightSeries(chartId, seriesName) {
	ApexCharts.exec(toggleChart, 'highlightSeries', seriesName, true);
}

function updateChartTitles(chartId, title) {
	ApexCharts.exec(chartId, 'updateOptions', {
		title: {
			text: title
		}
	}, true);
}

