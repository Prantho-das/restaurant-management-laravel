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
        const name = event.detail.name;
        const data = event.detail.data || {};

        // Meta Pixel
        if (event.detail.data) {
            fbq('track', name, data);
        } else {
            fbq('track', name);
        }

        // Google Tag Manager / GA4
        if (typeof dataLayer !== 'undefined') {
            // Standard GA4 Events
            if (name === 'AddToCart') {
                dataLayer.push({
                    event: 'add_to_cart',
                    ecommerce: {
                        value: data.value,
                        currency: data.currency,
                        items: [{
                            item_id: data.content_ids ? data.content_ids[0] : '',
                            item_name: data.content_name || '',
                            price: data.value,
                            quantity: 1
                        }]
                    }
                });
                // Custom event name specifically requested
                dataLayer.push({
                    event: 'incart',
                    ...data
                });
            } else if (name === 'BeginCheckout') {
                dataLayer.push({
                    event: 'begin_checkout',
                    ecommerce: {
                        value: data.value,
                        currency: data.currency,
                        items: data.content_ids ? data.content_ids.map(id => ({ item_id: id })) : []
                    }
                });
                // Custom event name specifically requested
                dataLayer.push({
                    event: 'checkout',
                    ...data
                });
            } else if (name === 'Purchase') {
                dataLayer.push({
                    event: 'purchase',
                    ecommerce: {
                        transaction_id: data.transaction_id || '',
                        value: data.value,
                        currency: data.currency,
                        items: data.content_ids ? data.content_ids.map(id => ({ item_id: id })) : []
                    }
                });
            }
        }
    }
});
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
@endif
