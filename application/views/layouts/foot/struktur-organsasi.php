<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</div>

<script type="speculationrules">
{"prefetch":[{"source":"document","where":{"and":[{"href_matches":"\/*"},{"not":{"href_matches":["\/wp-*.php","\/wp-admin\/*","\/wp-content\/uploads\/*","\/wp-content\/*","\/wp-content\/plugins\/*","\/wp-content\/themes\/blocksy\/*","\/*\\?(.+)"]}},{"not":{"selector_matches":"a[rel~=\"nofollow\"]"}},{"not":{"selector_matches":".no-prefetch, .no-prefetch a"}}]},"eagerness":"conservative"}]}
</script>
            <script>
                jQuery(function($){

                    
                });
            </script>
            <div id="fb-root"></div>
            <p class="pixelform_form-alert"></p><!-- Alert Notice --><!-- Simple Matomo Tracking Code plugin active --><!-- Matomo -->
<script type="text/javascript">
  var _paq = window._paq = window._paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="http://analytics.stmi.ac.id/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', 4]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code --><div class="ct-drawer-canvas" data-location="end"><div class="ct-drawer-inner">
	<a href="#main-container" class="ct-back-to-top "
		data-shape="circle"
		data-alignment="right"
		title="Go to top" aria-label="Go to top" hidden>

		<svg class="ct-icon" width="15" height="15" viewBox="0 0 20 20"><path d="M18.1,9.4c-0.2,0.4-0.5,0.6-0.9,0.6h-3.7c0,0-0.6,8.7-0.9,9.1C12.2,19.6,11.1,20,10,20c-1,0-2.3-0.3-2.7-0.9C7,18.7,6.5,10,6.5,10H2.8c-0.4,0-0.7-0.2-1-0.6C1.7,9,1.7,8.6,1.9,8.3c2.8-4.1,7.2-8,7.4-8.1C9.5,0.1,9.8,0,10,0s0.5,0.1,0.6,0.2c0.2,0.1,4.6,3.9,7.4,8.1C18.2,8.7,18.3,9.1,18.1,9.4z"/></svg>	</a>

	</div></div>                                                                        <!-- Matomo -->
<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="http://analytics.stmi.ac.id/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '4']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->
                                                                                                            
			<script>
				const lazyloadRunObserver = () => {
					const lazyloadBackgrounds = document.querySelectorAll( `.e-con.e-parent:not(.e-lazyloaded)` );
					const lazyloadBackgroundObserver = new IntersectionObserver( ( entries ) => {
						entries.forEach( ( entry ) => {
							if ( entry.isIntersecting ) {
								let lazyloadBackground = entry.target;
								if( lazyloadBackground ) {
									lazyloadBackground.classList.add( 'e-lazyloaded' );
								}
								lazyloadBackgroundObserver.unobserve( entry.target );
							}
						});
					}, { rootMargin: '200px 0px 200px 0px' } );
					lazyloadBackgrounds.forEach( ( lazyloadBackground ) => {
						lazyloadBackgroundObserver.observe( lazyloadBackground );
					} );
				};
				const events = [
					'DOMContentLoaded',
					'elementor/lazyload/observe',
				];
				events.forEach( ( event ) => {
					document.addEventListener( event, lazyloadRunObserver );
				} );
			</script>
			<script src="<?= base_url('wp-includes/js/dist/hooks.min4fdd.js?ver=4d63a3d491d11ffd8ac6') ?>" id="wp-hooks-js"></script>
<script src="<?= base_url('wp-includes/js/dist/i18n.minc33c.js?ver=5e580eb46a90c2b997e6') ?>" id="wp-i18n-js"></script>
<script id="wp-i18n-js-after">
wp.i18n.setLocaleData( { 'text direction\u0004ltr': [ 'ltr' ] } );
</script>
<script src="<?= base_url('wp-includes/js/jquery/jquery.form.minb2f9.js?ver=4.3.0') ?>" id="jquery-form-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/bootstrap.min8a54.js?ver=1.0.0') ?>" id="bootstrap-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/bootstrap-datepicker.min8a54.js?ver=1.0.0') ?>" id="bootstrap-datepicker-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/aos8a54.js?ver=1.0.0') ?>" id="aos-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/owl.carousel.min8a54.js?ver=1.0.0') ?>" id="owl-carousel-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/jquery-circle-progress8a54.js?ver=1.0.0') ?>" id="circle-progress-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/swiper-bundle.min8a54.js?ver=1.0.0') ?>" id="swiper-bundle-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/smooth-scroll.min8a54.js?ver=1.0.0') ?>" id="smooth-scroll-js"></script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/assets/js/pixelform-custom8a54.js?ver=1.0.0') ?>" id="Pixelform-custom-js"></script>
<script id="pixelform-public-js-extra">
var ajax_obj = {"ajax_url":"https:\/\/stmi.ac.id\/wp-admin\/admin-ajax.php"};
</script>
<script src="<?= base_url('wp-content/plugins/pixel-formbuilder/public/js/pixelform-public8a54.js?ver=1.0.0') ?>" id="pixelform-public-js"></script>
<script src="<?= base_url('wp-content/plugins/awsm-team/js/team.min3ba1.js?ver=1.3.3') ?>" id="awsm-team-js"></script>
<script id="ct-scripts-js-extra">
var ct_localizations = {"ajax_url":"https:\/\/stmi.ac.id\/wp-admin\/admin-ajax.php","public_url":"<?= base_url_json('wp-content/themes/blocksy/static/bundle/') ?>","rest_url":"<?= site_url_json('wp-json/') ?>","search_url":"<?= site_url_json('search/QUERY_STRING/') ?>","show_more_text":"Show more","more_text":"More","search_live_results":"Search results","search_live_no_results":"No results","search_live_no_result":"No results","search_live_one_result":"You got %s result. Please press Tab to select it.","search_live_many_results":"You got %s results. Please press Tab to select one.","clipboard_copied":"Copied!","clipboard_failed":"Failed to Copy","expand_submenu":"Expand dropdown menu","collapse_submenu":"Collapse dropdown menu","dynamic_js_chunks":[{"id":"blocksy_pro_micro_popups","selector":".ct-popup","url":"<?= base_url_json('wp-content/plugins/blocksy-companion-pro/framework/premium/static/bundle/micro-popups.js?ver=2.1.4') ?>"},{"id":"blocksy_sticky_header","selector":"header [data-sticky]","url":"<?= base_url_json('wp-content/plugins/blocksy-companion-pro/static/bundle/sticky.js?ver=2.1.4') ?>"}],"dynamic_styles":{"lazy_load":"<?= base_url_json('wp-content/themes/blocksy/static/bundle/non-critical-styles.min.css?ver=2.1.4') ?>","search_lazy":"<?= base_url_json('wp-content/themes/blocksy/static/bundle/non-critical-search-styles.min.css?ver=2.1.4') ?>","back_to_top":"<?= base_url_json('wp-content/themes/blocksy/static/bundle/back-to-top.min.css?ver=2.1.4') ?>"},"dynamic_styles_selectors":[{"selector":".ct-header-cart, #woo-cart-panel","url":"<?= base_url_json('wp-content/themes/blocksy/static/bundle/cart-header-element-lazy.min.css?ver=2.1.4') ?>"},{"selector":".flexy","url":"<?= base_url_json('wp-content/themes/blocksy/static/bundle/flexy.min.css?ver=2.1.4') ?>"},{"selector":".ct-media-container[data-media-id], .ct-dynamic-media[data-media-id]","url":"<?= base_url_json('wp-content/plugins/blocksy-companion-pro/framework/premium/static/bundle/video-lazy.min.css?ver=2.1.4') ?>"},{"selector":"#account-modal","url":"<?= base_url_json('wp-content/plugins/blocksy-companion-pro/static/bundle/header-account-modal-lazy.min.css?ver=2.0.26') ?>"},{"selector":".ct-header-account","url":"<?= base_url_json('wp-content/plugins/blocksy-companion-pro/static/bundle/header-account-dropdown-lazy.min.css?ver=2.0.26') ?>"}]};
</script>
<script src="<?= base_url('wp-content/themes/blocksy/static/bundle/main6b25.js?ver=2.1.4') ?>" id="ct-scripts-js"></script>
<script id="chaty-front-end-js-extra">
var chaty_settings = {"ajax_url":"https:\/\/stmi.ac.id\/wp-admin\/admin-ajax.php","analytics":"0","capture_analytics":"0","token":"4730dd82c6","chaty_widgets":[{"id":0,"identifier":0,"settings":{"cta_type":"simple-view","cta_body":"","cta_head":"","cta_head_bg_color":"","cta_head_text_color":"","show_close_button":1,"position":"left","custom_position":1,"bottom_spacing":"25","side_spacing":"25","icon_view":"vertical","default_state":"hover","cta_text":"","cta_text_color":"#333333","cta_bg_color":"#ffffff","show_cta":"first_click","is_pending_mesg_enabled":"off","pending_mesg_count":"1","pending_mesg_count_color":"#ffffff","pending_mesg_count_bgcolor":"#dd0000","widget_icon":"chat-db","widget_icon_url":"","font_family":"-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif","widget_size":"44","custom_widget_size":"44","is_google_analytics_enabled":0,"close_text":"Hide","widget_color":"#86CD91","widget_icon_color":"#ffffff","widget_rgb_color":"134,205,145","has_custom_css":0,"custom_css":"","widget_token":"fb5906ab54","widget_index":"","attention_effect":"waggle"},"triggers":{"has_time_delay":1,"time_delay":"0","exit_intent":0,"has_display_after_page_scroll":0,"display_after_page_scroll":"0","auto_hide_widget":0,"hide_after":0,"show_on_pages_rules":[],"time_diff":0,"has_date_scheduling_rules":0,"date_scheduling_rules":{"start_date_time":"","end_date_time":""},"date_scheduling_rules_timezone":0,"day_hours_scheduling_rules_timezone":0,"has_day_hours_scheduling_rules":[],"day_hours_scheduling_rules":[],"day_time_diff":0,"show_on_direct_visit":0,"show_on_referrer_social_network":0,"show_on_referrer_search_engines":0,"show_on_referrer_google_ads":0,"show_on_referrer_urls":[],"has_show_on_specific_referrer_urls":0,"has_traffic_source":0,"has_countries":0,"countries":[],"has_target_rules":0},"channels":[{"channel":"Whatsapp","value":"<?= html_escape(preg_replace('/[^0-9]/', '', strpos($contacts['whatsapp'], '0') === 0 ? '62' . substr($contacts['whatsapp'], 1) : $contacts['whatsapp'])) ?>","hover_text":"WhatsApp","chatway_position":"","svg_icon":"<svg width=\"39\" height=\"39\" viewBox=\"0 0 39 39\" fill=\"none\" xmlns=\"http:\/\/www.w3.org\/2000\/svg\"><circle class=\"color-element\" cx=\"19.4395\" cy=\"19.4395\" r=\"19.4395\" fill=\"#49E670\"\/><path d=\"M12.9821 10.1115C12.7029 10.7767 11.5862 11.442 10.7486 11.575C10.1902 11.7081 9.35269 11.8411 6.84003 10.7767C3.48981 9.44628 1.39593 6.25317 1.25634 6.12012C1.11674 5.85403 2.13001e-06 4.39053 2.13001e-06 2.92702C2.13001e-06 1.46351 0.83755 0.665231 1.11673 0.399139C1.39592 0.133046 1.8147 1.01506e-06 2.23348 1.01506e-06C2.37307 1.01506e-06 2.51267 1.01506e-06 2.65226 1.01506e-06C2.93144 1.01506e-06 3.21063 -2.02219e-06 3.35022 0.532183C3.62941 1.19741 4.32736 2.66092 4.32736 2.79397C4.46696 2.92702 4.46696 3.19311 4.32736 3.32616C4.18777 3.59225 4.18777 3.59224 3.90858 3.85834C3.76899 3.99138 3.6294 4.12443 3.48981 4.39052C3.35022 4.52357 3.21063 4.78966 3.35022 5.05576C3.48981 5.32185 4.18777 6.38622 5.16491 7.18449C6.42125 8.24886 7.39839 8.51496 7.81717 8.78105C8.09636 8.91409 8.37554 8.9141 8.65472 8.648C8.93391 8.38191 9.21309 7.98277 9.49228 7.58363C9.77146 7.31754 10.0507 7.1845 10.3298 7.31754C10.609 7.45059 12.2841 8.11582 12.5633 8.38191C12.8425 8.51496 13.1217 8.648 13.1217 8.78105C13.1217 8.78105 13.1217 9.44628 12.9821 10.1115Z\" transform=\"translate(12.9597 12.9597)\" fill=\"#FAFAFA\"\/><path d=\"M0.196998 23.295L0.131434 23.4862L0.323216 23.4223L5.52771 21.6875C7.4273 22.8471 9.47325 23.4274 11.6637 23.4274C18.134 23.4274 23.4274 18.134 23.4274 11.6637C23.4274 5.19344 18.134 -0.1 11.6637 -0.1C5.19344 -0.1 -0.1 5.19344 -0.1 11.6637C-0.1 13.9996 0.624492 16.3352 1.93021 18.2398L0.196998 23.295ZM5.87658 19.8847L5.84025 19.8665L5.80154 19.8788L2.78138 20.8398L3.73978 17.9646L3.75932 17.906L3.71562 17.8623L3.43104 17.5777C2.27704 15.8437 1.55796 13.8245 1.55796 11.6637C1.55796 6.03288 6.03288 1.55796 11.6637 1.55796C17.2945 1.55796 21.7695 6.03288 21.7695 11.6637C21.7695 17.2945 17.2945 21.7695 11.6637 21.7695C9.64222 21.7695 7.76778 21.1921 6.18227 20.039L6.17557 20.0342L6.16817 20.0305L5.87658 19.8847Z\" transform=\"translate(7.7758 7.77582)\" fill=\"white\" stroke=\"white\" stroke-width=\"0.2\"\/><\/svg>","is_desktop":1,"is_mobile":1,"icon_color":"#49E670","icon_rgb_color":"73,230,112","channel_type":"Whatsapp","custom_image_url":"","order":"","pre_set_message":"","is_use_web_version":"1","is_open_new_tab":"1","is_default_open":"0","has_welcome_message":"1","emoji_picker":"1","input_placeholder":"Write your message...","chat_welcome_message":"<p>Apa yang bisa saya bantu? :)<\/p>","wp_popup_headline":"Let&#039;s chat on WhatsApp","wp_popup_nickname":"","wp_popup_profile":"","wp_popup_head_bg_color":"#4AA485","qr_code_image_url":"","mail_subject":"","channel_account_type":"personal","contact_form_settings":[],"contact_fields":[],"url":"<?= str_replace('/', '\/', html_escape($contacts['whatsapp_url'])) ?>","mobile_target":"","desktop_target":"_blank","target":"_blank","is_agent":0,"agent_data":[],"header_text":"","header_sub_text":"","header_bg_color":"","header_text_color":"","widget_token":"fb5906ab54","widget_index":"","click_event":"","viber_url":""},{"channel":"Email","value":"humas@stmi.ac.id","hover_text":"Email","chatway_position":"","svg_icon":"<svg width=\"39\" height=\"39\" viewBox=\"0 0 39 39\" fill=\"none\" xmlns=\"http:\/\/www.w3.org\/2000\/svg\"><circle class=\"color-element\" cx=\"19.4395\" cy=\"19.4395\" r=\"19.4395\" fill=\"#FF485F\"\/><path d=\"M20.5379 14.2557H1.36919C0.547677 14.2557 0 13.7373 0 12.9597V1.29597C0 0.518387 0.547677 0 1.36919 0H20.5379C21.3594 0 21.9071 0.518387 21.9071 1.29597V12.9597C21.9071 13.7373 21.3594 14.2557 20.5379 14.2557ZM20.5379 12.9597V13.6077V12.9597ZM1.36919 1.29597V12.9597H20.5379V1.29597H1.36919Z\" transform=\"translate(8.48619 12.3117)\" fill=\"white\"\/><path d=\"M10.9659 8.43548C10.829 8.43548 10.692 8.43548 10.5551 8.30588L0.286184 1.17806C0.012346 0.918864 -0.124573 0.530073 0.149265 0.270879C0.423104 0.0116857 0.833862 -0.117911 1.1077 0.141283L10.9659 7.00991L20.8241 0.141283C21.0979 -0.117911 21.5087 0.0116857 21.7825 0.270879C22.0563 0.530073 21.9194 0.918864 21.6456 1.17806L11.3766 8.30588C11.2397 8.43548 11.1028 8.43548 10.9659 8.43548Z\" transform=\"translate(8.47443 12.9478)\" fill=\"white\"\/><path d=\"M9.0906 7.13951C8.95368 7.13951 8.81676 7.13951 8.67984 7.00991L0.327768 1.17806C-0.0829894 0.918864 -0.0829899 0.530073 0.190849 0.270879C0.327768 0.0116855 0.738525 -0.117911 1.14928 0.141282L9.50136 5.97314C9.7752 6.23233 9.91212 6.62112 9.63828 6.88032C9.50136 7.00991 9.36444 7.13951 9.0906 7.13951Z\" transform=\"translate(20.6183 18.7799)\" fill=\"white\"\/><path d=\"M0.696942 7.13951C0.423104 7.13951 0.286185 7.00991 0.149265 6.88032C-0.124573 6.62112 0.012346 6.23233 0.286185 5.97314L8.63826 0.141282C9.04902 -0.117911 9.45977 0.0116855 9.59669 0.270879C9.87053 0.530073 9.73361 0.918864 9.45977 1.17806L1.1077 7.00991C0.970781 7.13951 0.833862 7.13951 0.696942 7.13951Z\" transform=\"translate(8.47443 18.7799)\" fill=\"white\"\/><\/svg>","is_desktop":1,"is_mobile":1,"icon_color":"#FF485F","icon_rgb_color":"255,72,95","channel_type":"Email","custom_image_url":"","order":"","pre_set_message":"","is_use_web_version":"1","is_open_new_tab":"1","is_default_open":"0","has_welcome_message":"0","emoji_picker":"1","input_placeholder":"Write your message...","chat_welcome_message":"","wp_popup_headline":"","wp_popup_nickname":"","wp_popup_profile":"","wp_popup_head_bg_color":"#4AA485","qr_code_image_url":"","mail_subject":"","channel_account_type":"personal","contact_form_settings":[],"contact_fields":[],"url":"mailto:humas@stmi.ac.id","mobile_target":"","desktop_target":"","target":"","is_agent":0,"agent_data":[],"header_text":"","header_sub_text":"","header_bg_color":"","header_text_color":"","widget_token":"fb5906ab54","widget_index":"","click_event":"","viber_url":""}]}],"data_analytics_settings":"off","lang":{"whatsapp_label":"WhatsApp Message","hide_whatsapp_form":"Hide WhatsApp Form","emoji_picker":"Show Emojis"},"has_chatway":"","has_CookieYes":"","has_iubenda_cookie":""};
</script>
<script defer src="<?= base_url('wp-content/plugins/chaty/js/cht-front-script.mina53c.js?ver=3.4.51715736001') ?>" id="chaty-front-end-js"></script>
<script src="<?= base_url('wp-content/plugins/chaty/admin/assets/js/picmo-umd.min4c8b.js?ver=3.4.5') ?>" id="chaty-picmo-js-js"></script>
<script src="<?= base_url('wp-content/plugins/chaty/admin/assets/js/picmo-latest-umd.min4c8b.js?ver=3.4.5') ?>" id="chaty-picmo-latest-js-js"></script>
<script src="<?= base_url('wp-content/plugins/elementor/assets/js/webpack.runtime.min55cb.js?ver=3.30.3') ?>" id="elementor-webpack-runtime-js"></script>
<script src="<?= base_url('wp-content/plugins/elementor/assets/js/frontend-modules.min55cb.js?ver=3.30.3') ?>" id="elementor-frontend-modules-js"></script>
<script src="<?= base_url('wp-includes/js/jquery/ui/core.minb37e.js?ver=1.13.3') ?>" id="jquery-ui-core-js"></script>
<script id="elementor-frontend-js-before">
var elementorFrontendConfig = {"environmentMode":{"edit":false,"wpPreview":false,"isScriptDebug":false},"i18n":{"shareOnFacebook":"Share on Facebook","shareOnTwitter":"Share on Twitter","pinIt":"Pin it","download":"Download","downloadImage":"Download image","fullscreen":"Fullscreen","zoom":"Zoom","share":"Share","playVideo":"Play Video","previous":"Previous","next":"Next","close":"Close","a11yCarouselPrevSlideMessage":"Previous slide","a11yCarouselNextSlideMessage":"Next slide","a11yCarouselFirstSlideMessage":"This is the first slide","a11yCarouselLastSlideMessage":"This is the last slide","a11yCarouselPaginationBulletMessage":"Go to slide"},"is_rtl":false,"breakpoints":{"xs":0,"sm":480,"md":768,"lg":1025,"xl":1440,"xxl":1600},"responsive":{"breakpoints":{"mobile":{"label":"Mobile Portrait","value":767,"default_value":767,"direction":"max","is_enabled":true},"mobile_extra":{"label":"Mobile Landscape","value":880,"default_value":880,"direction":"max","is_enabled":false},"tablet":{"label":"Tablet Portrait","value":1024,"default_value":1024,"direction":"max","is_enabled":true},"tablet_extra":{"label":"Tablet Landscape","value":1200,"default_value":1200,"direction":"max","is_enabled":false},"laptop":{"label":"Laptop","value":1366,"default_value":1366,"direction":"max","is_enabled":false},"widescreen":{"label":"Widescreen","value":2400,"default_value":2400,"direction":"min","is_enabled":false}},
"hasCustomBreakpoints":false},"version":"3.30.3","is_static":false,"experimentalFeatures":{"e_font_icon_svg":true,"additional_custom_breakpoints":true,"container":true,"nested-elements":true,"home_screen":true,"global_classes_should_enforce_capabilities":true,"cloud-library":true,"e_opt_in_v4_page":true},"urls":{"assets":"<?= base_url_json('wp-content/plugins/elementor/assets/') ?>","ajaxurl":"https:\/\/stmi.ac.id\/wp-admin\/admin-ajax.php","uploadUrl":"<?= base_url_json('wp-content/uploads') ?>"},"nonces":{"floatingButtonsClickTracking":"3ff5467c51"},"swiperClass":"swiper","settings":{"page":[],"editorPreferences":[]},"kit":{"active_breakpoints":["viewport_mobile","viewport_tablet"],"global_image_lightbox":"yes","lightbox_enable_counter":"yes","lightbox_enable_fullscreen":"yes","lightbox_enable_zoom":"yes","lightbox_enable_share":"yes","lightbox_title_src":"title","lightbox_description_src":"description"},"post":{"id":804,"title":"Struktur%20Organsasi%20%E2%80%93%20Politeknik%20STMI%20Jakarta","excerpt":"","featuredImage":false}};
</script>
<script src="<?= base_url('wp-content/plugins/elementor/assets/js/frontend.min55cb.js?ver=3.30.3') ?>" id="elementor-frontend-js"></script>
<script id="gt_widget_script_73208722-js-before">
window.gtranslateSettings = /* document.write */ window.gtranslateSettings || {};window.gtranslateSettings['73208722'] = {"default_language":"id","languages":["zh-CN","en","id","ja","ko"],"url_structure":"none","flag_style":"2d","flag_size":24,"wrapper_selector":"#gt-wrapper-73208722","alt_flags":{"en":"usa"},"horizontal_position":"inline","flags_location":"\/wp-content\/plugins\/gtranslate\/flags\/"};
</script><script src="<?= base_url('wp-content/plugins/gtranslate/js/flags6c2d.js?ver=6.8.2') ?>" data-no-optimize="1" data-no-minify="1" data-gt-orig-url="<?= html_escape($gt_orig_url) ?>" data-gt-orig-domain="stmi.ac.id" data-gt-widget-id="73208722" defer></script><script id="gt_widget_script_35407523-js-before">
window.gtranslateSettings = /* document.write */ window.gtranslateSettings || {};window.gtranslateSettings['35407523'] = {"default_language":"id","languages":["zh-CN","en","id","ja","ko"],"url_structure":"none","flag_style":"2d","flag_size":24,"wrapper_selector":"#gt-wrapper-35407523","alt_flags":{"en":"usa"},"horizontal_position":"inline","flags_location":"\/wp-content\/plugins\/gtranslate\/flags\/"};
</script><script src="<?= base_url('wp-content/plugins/gtranslate/js/flags6c2d.js?ver=6.8.2') ?>" data-no-optimize="1" data-no-minify="1" data-gt-orig-url="<?= html_escape($gt_orig_url) ?>" data-gt-orig-domain="stmi.ac.id" data-gt-widget-id="35407523" defer></script>
</body>
</html>
