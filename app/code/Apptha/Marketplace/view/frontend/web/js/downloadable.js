 var marketplaceDownloadableSampleRow = 0;
var marketplaceDownloadableLinkRow = 0;

function addMarketPlaceDownloadableLinksRow(value) {

    var selectLinkOption = $("marketplace_download_link_row_CCC").innerHTML.replace(/CCC/g, marketplaceDownloadableLinkRow);
    $(value).insert(selectLinkOption);
    marketplaceDownloadableLinkRow = marketplaceDownloadableLinkRow + 1;
}

function removeMarketPlaceDownloadableLinksRow(value) {
    value.up('table').remove();
}

function addMarketPlaceDownloadableSamplesRow(value) {



    var selectSampleOption = $("marketplace_download_sample_row_CCC").innerHTML.replace(/CCC/g, marketplaceDownloadableSampleRow);
    $(value).insert(selectSampleOption);
    marketplaceDownloadableSampleRow = marketplaceDownloadableSampleRow + 1;
}
function removeMarketPlaceDownloadableSamplesRow(value) {
    value.up('tr').remove();
}

function updateMarketPlaceDownloadableLinksRow(value) {
	var countLink = document.getElementById("downloadable_link_count_value").value;
	var marketplaceDownloadableLinkRow = parseInt(countLink);
    var selectLinkOption = $("marketplace_download_link_row_CCC").innerHTML.replace(/CCC/g, marketplaceDownloadableLinkRow);
    $(value).insert(selectLinkOption);
    marketplaceDownloadableLinkRow = marketplaceDownloadableLinkRow + 1;
}
function updateMarketPlaceDownloadableSamplesRow(value) {

	var countLink = document.getElementById("downloadable_sample_count_value").value;

	var marketplaceDownloadableSampleRow = parseInt(countLink);


    var selectSampleOption = $("marketplace_download_sample_row_CCC").innerHTML.replace(/CCC/g, marketplaceDownloadableSampleRow);
    $(value).insert(selectSampleOption);
    marketplaceDownloadableSampleRow = marketplaceDownloadableSampleRow + 1;
}