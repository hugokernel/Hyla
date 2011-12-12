
<script src="http://maps.google.com/maps?file=api&amp;v=2.78&amp;key={GOOGLE_KEY}" type="text/javascript"></script>

<script type="text/javascript" script="javascript">

<!-- BEGIN kml -->
var map;
var geoXml = new GGeoXml("{OBJECT_DOWNLOAD}");

function load() {
    if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(49.496675, 1.65625), 3);
        map.addControl(new GLargeMapControl());
        map.addOverlay(geoXml);
    }
}
<!-- END kml -->

<!-- BEGIN json -->
var poi = {"data": { "item": [
<!-- BEGIN json_line -->
        {
            "lon":  "{LON}",
            "lat":  "{LAT}",
            "label":"{LABEL}"
        },
<!-- END json_line -->
        {}
] } }

function addpoi(map, lat, lon, label) {
    var oIcon = new GIcon();
    oIcon.iconSize = new GSize(16, 16);
    oIcon.iconAnchor = new GPoint(6, 20);
    oIcon.infoWindowAnchor = new GPoint(9, 2);

    var oPoint = new GLatLng(lat, lon);
    var marker = new GMarker(oPoint);
    GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml("<p>" + label + "</p>");
    });
    map.addOverlay(marker);
}

function load()
{
    if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(49.496675, 1.65625), 3);
        map.addControl(new GLargeMapControl());
    }

    for (var key in poi.data.item) {
        if (poi.data.item[key].lon) {
            addpoi(map, poi.data.item[key].lat, poi.data.item[key].lon, poi.data.item[key].label);
        }
    }
}
<!-- END json -->

$(document).ready(function() {
    load();
});

$(document).unload(function() {
    GUnload();
});

</script>

<div class="plugin">

    <div id="map" style="width: 100%; height: 500px; border: 1px solid black; clear: both;"></div>

</div>

