var tpj = jQuery;
var revapi;

tpj(document).ready(function () {
  if (tpj("#rev_slider_one").revolution == undefined) {
    revslider_showDoubleJqueryError("#rev_slider_one");
  } else {
    revapi = tpj("#rev_slider_one").show().revolution({
      sliderType: "standard",
      jsFileLocation: "plugins/revolution/js/source/",
      sliderLayout: "auto",
      delay: 10000,
      lazyType: "smart",
      navigation: {
        keyboardNavigation: "off",
        mouseScrollNavigation: "off",
        onHoverStop: "off"
      },
      responsiveLevels: [1200, 1040, 802, 480],
      visibilityLevels: [1200, 1040, 802, 480],
        gridwidth:[1200,1040,800,480],
        gridheight:[870,800,800,800],
      disableProgressBar: "on",
      shadow: 0,
      autoHeight: "off",
      stopLoop: "off",
      stopAfterLoops: -1,
      stopAtSlide: -1,
      shuffle: "off",
      fallbacks: {
        simplifyAll: "off",
        nextSlideOnWindowFocus: "off",
        disableFocusListener: false,
      }
    });
  }
});
