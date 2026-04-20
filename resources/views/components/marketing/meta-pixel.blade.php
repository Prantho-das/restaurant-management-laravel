@php
    $pixelId = \App\Models\Setting::where('key', 'fb_pixel_id')->value('value');
@endphp

@if($pixelId)
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $pixelId }}');
fbq('track', 'PageView');

// Listen for custom conversion events from Livewire
window.addEventListener('conversion-event', function(event) {
    if (event.detail && event.detail.name) {
        if (event.detail.data) {
            fbq('track', event.detail.name, event.detail.data);
        } else {
            fbq('track', event.detail.name);
        }
    }
});
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
@endif
