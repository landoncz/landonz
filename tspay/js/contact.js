
<script type="text/javascript">

function echeck(str) {

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   alert("Invalid E-mail Address")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   alert("Invalid E-mail Address")
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    alert("Invalid E-mail Address")
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    alert("Invalid E-mail Address")
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    alert("Invalid E-mail Address")
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    alert("Invalid E-mail Address")
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    alert("Invalid E-mail Address")
		    return false
		 }

 		 return true					
	}

function ValidateForm(){
	var emailID=document.contactForm.from_email
	
	if ((emailID.value==null)||(emailID.value=="")){
		alert("Please Enter your Email Address")
		emailID.focus()
		return false
	}
	if (echeck(emailID.value)==false){
		emailID.value=""
		emailID.focus()
		return false
	}
	return true
 }

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
        map.setCenter(new GLatLng(30.348231, -87.153247),10);

        // Add markers to the map
        
          var point = new GLatLng(30.348231, -87.153247);
          map.addOverlay(createMarker(point, "Transaction Solutions, LLC"));
          map.addOverlay(new GMarker(point));
        
      }
    }

    //]]>
</script>
