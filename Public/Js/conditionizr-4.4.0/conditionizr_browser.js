/*!
 * Chrome
 * We return `!!window.chrome` to test the truthy value,
 * but Opera 14+ responds true to this, so we need to test
 * the `navigator.vendor` to make sure it's from Google
 */
conditionizr.add('chrome', function () {
  return !!window.chrome && /google/i.test(navigator.vendor);
});

/*!
 * Chromium
 */
conditionizr.add('chromium', function () {
  return /cros i686/i.test(navigator.platform);
});

/*!
 * Firefox
 * Evaluate the presence of `InstallTrigger`
 */
conditionizr.add('firefox', function () {
  return 'InstallTrigger' in window;
});

/*!
 * IE10
 * @cc_on Conditional Compilation to test the
 * JavaScript version and MSIE 10 in the UserAgent
 */
conditionizr.add('ie10', function () {
  return (Function('/*@cc_on return (/^10/.test(@_jscript_version) && /MSIE 10\.0(?!.*IEMobile)/i.test(navigator.userAgent)); @*/')());
});

/*!
 * IE10 Touch
 * We want to ignore IEMobile here and focus
 * on the proper IE10 Touch
 */
conditionizr.add('ie10touch', function () {
  return /MSIE 10\.0.*Touch(?!.*IEMobile)/i.test(navigator.userAgent);
});

/*!
 * IE11
 * RegExp to check out the new IE UserAgent:
 * Trident/7.0; rv:11.0
 */
conditionizr.add('ie11', function () {
  return /(?:\sTrident\/7\.0;.*\srv:11\.0)/i.test(navigator.userAgent);
});

/*!
 * IE6
 * @cc_on Conditional Compilation to test the
 * JavaScript version and MSIE 6 in the UserAgent
 */
conditionizr.add('ie6', function () {
  return (Function('/*@cc_on return (@_jscript_version == 5.6 || (@_jscript_version == 5.7 && /MSIE 6\.0/i.test(navigator.userAgent))); @*/')());
});

/*!
 * IE7
 * @cc_on Conditional Compilation to test the
 * JavaScript version and MSIE 7 in the UserAgent
 */
conditionizr.add('ie7', function () {
  return (Function('/*@cc_on return (@_jscript_version == 5.7 && /MSIE 7\.0(?!.*IEMobile)/i.test(navigator.userAgent)); @*/')());
});

/*!
 * IE8
 * @cc_on Conditional Compilation to test the
 * JavaScript versions
 */
conditionizr.add('ie8', function () {
  return (Function('/*@cc_on return (@_jscript_version > 5.7 && !/^(9|10)/.test(@_jscript_version)); @*/')());
});

/*!
 * IE9
 * @cc_on Conditional Compilation to test the
 * JavaScript version and MSIE 9 in the UserAgent
 */
conditionizr.add('ie9', function () {
  return (Function('/*@cc_on return (/^9/.test(@_jscript_version) && /MSIE 9\.0(?!.*IEMobile)/i.test(navigator.userAgent)); @*/')());
});

/*!
 * iOS [iPad, iPhone, iPod]
 * Simple minimal UserAgent test
 */
conditionizr.add('ios', function () {
  return /iP(ad|hone|od)/i.test(navigator.userAgent);
});

/*!
 * Linux
 * Test the `navigator.platform` but
 * ignore any android phones
 */
conditionizr.add('linux', function () {
  return /linux/i.test(navigator.platform) && !/android|cros/i.test(navigator.userAgent);
});

/*!
 * Localhost
 * Tests `location.host` for the `127.0.0.1` IPv4 address as well
 * as the `localhost` address name
 */
conditionizr.add('localhost', function () {
  return /(?:127\.0\.0\.1|localhost)/.test(location.host);
});

/*!
 * Mac
 */
conditionizr.add('mac', function () {
    return /mac/i.test(navigator.platform);
});

/*!
 * Opera
 * `window.opera` applies to earlier Opera
 * browsers, others respond true to `window.chrome`
 * so we need to test the `navigator.vendor` to be sure
 */
conditionizr.add('opera', function () {
  return !!window.opera || /opera/i.test(navigator.vendor);
});

/*!
 * PhantomJS
 */
conditionizr.add('phantomjs', function () {
  return /\sPhantomJS\/[[0-9]+\]/.test(navigator.userAgent);
});

/*!
 * Retina
 * We're assuming anything greater than 1.5DPR
 * is classed as Retina
 */
conditionizr.add('retina', function () {
  return window.devicePixelRatio >= 1.5;
});

/*!
 * Safari
 * The only browser where the HTMLElement
 * contains `Constructor`
 */
conditionizr.add('safari', function () {
  return /Constructor/.test(window.HTMLElement);
});

/*!
 * Touch
 * Use `in` to test for the `ontouchstart` property,
 * IE10 ships `msMaxTouchPoints` for the touch hardware
 */
conditionizr.add('touch', function () {
  return 'ontouchstart' in window || !!navigator.msMaxTouchPoints;
});

/*!
 * Windows Phone 7
 */
conditionizr.add('winPhone7', function () {
    return /Windows Phone 7.0/i.test(navigator.userAgent);
});

/*!
 * Windows Phone 7.5
 */
conditionizr.add('winPhone75', function () {
  return /Windows Phone 7.5/i.test(navigator.userAgent);
});

/*!
 * Windows Phone 8
 */
conditionizr.add('winPhone8', function () {
    return /Windows Phone 8.0/i.test(navigator.userAgent);
});

/*!
 * Windows
 */
conditionizr.add('windows', function () {
    return /win/i.test(navigator.platform);
});
