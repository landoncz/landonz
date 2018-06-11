<script type="text/javascript">

Lats = new Array(<!--{ARRAY_LENGTH}-->);
Long = new Array(<!--{ARRAY_LENGTH}-->);
Desc = new Array(<!--{ARRAY_LENGTH}-->);

<!--{LATS_DECLARE}-->

<!--{LONG_DECLARE}-->

<!--{DESC_DECLARE}-->

    //<![CDATA[
    function createMarker(point, descrip) {
		var marker = new GMarker(point);
		GEvent.addListener(marker, "click", function() {
			marker.openInfoWindowHtml(descrip);
		});
		return marker;
	}

    function load() {
      if (GBrowserIsCompatible()) {
      var map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        //map.setCenter(new GLatLng(30.420699, -87.219718), 4);
        map.setCenter(new GLatLng(36.43, -97.54), 3);

        // Add markers to the map
        for (var i = 0; i < Lats.length; i++) {
          var point = new GLatLng(Lats[i], Long[i]);
          map.addOverlay(createMarker(point, Desc[i]));
          //map.addOverlay(new GMarker(point));
        }
      }
    }

    //]]>
</script>