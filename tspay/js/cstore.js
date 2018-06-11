<script type="text/javascript">
// =-=-==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// This script requires the Spry effects js class to work !!! //
//
var isImageUp = 0;

function Swap()
{
	Spry.Effect.Blind('readMoreDiv', {duration: 1500, from: '1px', to: '420px', toggle: true});
	if ( isImageUp == 1 )
	{
		document.getElementById("moreLessText").innerHTML = 'More >>';
		document.getElementById("moreLessText2").innerHTML = 'More >>';
		document.getElementById("moreLessText3").innerHTML = 'More >>';
		isImageUp = 0;
   }
   else
   {
		document.getElementById("moreLessText").innerHTML = 'Less <<';
		document.getElementById("moreLessText2").innerHTML = 'Less <<';
		document.getElementById("moreLessText3").innerHTML = 'Less <<';
		isImageUp = 1;
	}
	return true;
}
</script>
