<script type="text/javascript">
// =-=-==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// This script requires the Spry effects js class to work !!! //
//
function build_body()
{
	// Main left image (appear from fade)
	Spry.Effect.AppearFade('leftImage', {duration:1500,from:0,to:100,toggle:false});
	
	// Main right image (appear from fade)
	Spry.Effect.AppearFade('rightImage', {duration:1500,from:0,to:100,toggle:false});
	
	// Main blues logo (blind effect)
	//Spry.Effect.Blind('bluesLogoDiv', {duration: 1500, from: '0%', to:'100%', toggle:false});
	
	// Pockets image (blind effect)
	Spry.Effect.Blind('pocketsImage', {duration: 1500, from: '0%', to: '100%', toggle: false});
}
</script>
