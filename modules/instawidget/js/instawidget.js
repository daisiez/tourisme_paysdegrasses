jQuery(document).ready(function(){window.location.href.split("#access_token=")[1];instafeed_init()});var userid=drupalSettings.userid,access_token_string=drupalSettings.access_token_string;function instafeed_init(){window.instaFeedMeta={},jQuery("#instawidget_instagram.insta").each(function(t){var e=jQuery(this),i=e.find("#insta_images"),n=e.find("#insta_load .insta_load_btn"),s="standard_resolution",o=parseInt(this.getAttribute("data-cols"),10),a=JSON.parse(this.getAttribute("data-options")),r="none",d=userid,l=this.getAttribute("data-num"),c="",u=[];switch(jQuery(this).attr("data-insta-index",t),a.feedIndex=t,window.instaFeedMeta[t]={error:{},idsInFeed:[]},""!==a.sortby&&(r=a.sortby),this.getAttribute("data-res")){case"auto":var h=e.innerWidth(),p=e.innerWidth()/o,m=jQuery(window).width();m<640&&(h<640&&e.is(".insta_col_3, .insta_col_4, .insta_col_5, .insta_col_6")&&(p=300),h<640&&e.is(".insta_col_7, .insta_col_8, .insta_col_9, .insta_col_10")&&(p=100),h>320&&h<480&&m<480&&(p=480),h<320&&m<480&&(p=300)),s=p<150?"thumbnail":p<320?"low_resolution":"standard_resolution",h<=100&&(s="low_resolution");break;case"thumb":s="thumbnail";break;case"medium":s="low_resolution";break;default:s="standard_resolution"}var f=d.replace(/ /g,"").split(","),g="",_="https://api.instagram.com/v1/users/"+userid+"?access_token="+access_token_string;a.headercolor.length&&(g='style="color: #'+a.headercolor+'"'),jQuery.ajax({method:"GET",url:_,dataType:"jsonp",success:function(t){void 0===t.meta.error_message&&(c='<a href="https://instagram.com/'+t.data.username+'" target="_blank" title="@'+t.data.username+'" class="insta_header_link">',c+='<div class="insta_header_text">',c+="<h3 "+g,0!=t.data.bio.length&&"true"===a.showbio||(c+=' class="insta_no_bio"'),c+=">@"+t.data.username+"</h3>",t.data.bio.length&&"true"===a.showbio&&(c+='<p class="insta_bio" '+g+">"+t.data.bio+"</p>"),c+="</div>",c+='<div class="insta_header_img">',c+='<div class="insta_header_img_hover"><i></i></div>',c+='<img src="'+t.data.profile_picture+'" alt="'+t.data.full_name+'" width="50" height="50">',c+="</div>",c+="</a>",e.find(".instawidget_instagram_header").prepend(c),e.find(".insta_follow_btn").length&&e.find(".insta_follow_btn a").attr("href","https://instagram.com/"+t.data.username))}}),jQuery.each(f,function(d,c){window.instaFeedMeta[t].idsInFeed.push(c);var h=new instagramfeed({target:i,get:"user",sortBy:r,resolution:s,limit:parseInt(l,10),template:'<div class="insta_item insta_type_{{model.type}} insta_new" id="insta_{{id}}" data-date="{{model.created_time_raw}}"><div class="insta_photo_wrap"><a class="insta_photo" href="{{link}}" target="_blank"><img src="{{image}}" alt="{{caption}}" width="100" height="100" /></a></div></div>',filter:function(t){var e=new Date(1e3*t.created_time).getTime();return t.created_time_raw=e,null!=t.caption&&(t.caption.text=t.caption.text.replace(/[^a-zA-Z ]/g,"")),t.images.thumbnail.url=t.images.thumbnail.url.split("?ig_cache_key")[0],t.images.standard_resolution.url=t.images.standard_resolution.url.split("?ig_cache_key")[0],t.images.low_resolution.url=t.images.low_resolution.url.split("?ig_cache_key")[0],!0},userId:parseInt(c,10),accessToken:access_token_string,after:function(){if(e.find(".insta_loader").remove(),e.find(".insta_load_btn .fa-spinner").hide(),e.find(".insta_load_btn .insta_btn_text").css("opacity",1),this.hasNext()&&u.push("1"),u.length>0?n.show():(n.hide(),e.css("padding-bottom",0)),"function"==typeof insta_custom_js&&setTimeout(function(){insta_custom_js()},100),"thumbnail"!==s){var t=t||{VER:"0.9.944"};t.bgs_Available=!1,t.bgs_CheckRunned=!1,function(e){e.fn.extend({insta_imgLiquid:function(i){this.defaults={fill:!0,verticalAlign:"center",horizontalAlign:"center",useBackgroundSize:!0,useDataHtmlAttr:!0,responsive:!0,delay:0,fadeInTime:0,removeBoxBackground:!0,hardPixels:!0,responsiveCheckTime:500,timecheckvisibility:500,onStart:null,onFinish:null,onItemStart:null,onItemFinish:null,onItemError:null},function(){if(!t.bgs_CheckRunned){t.bgs_CheckRunned=!0;var i=e('<span style="background-size:cover" />');e("body").append(i),function(){var e=i[0];if(e&&window.getComputedStyle){var n=window.getComputedStyle(e,null);n&&n.backgroundSize&&(t.bgs_Available="cover"===n.backgroundSize)}}(),i.remove()}}();var n=this;return this.options=i,this.settings=e.extend({},this.defaults,this.options),this.settings.onStart&&this.settings.onStart(),this.each(function(i){function s(){(d.responsive||c.data("insta_imgLiquid_oldProcessed"))&&c.data("insta_imgLiquid_settings")&&(d=c.data("insta_imgLiquid_settings"),l.actualSize=l.get(0).offsetWidth+l.get(0).offsetHeight/1e4,l.sizeOld&&l.actualSize!==l.sizeOld&&a(),l.sizeOld=l.actualSize,setTimeout(s,d.responsiveCheckTime))}function o(){c.data("insta_imgLiquid_error",!0),l.addClass("insta_imgLiquid_error"),d.onItemError&&d.onItemError(i,l,c),r()}function a(){var t,e,n,s,o,a,u,h,p=0,m=0,f=l.width(),g=l.height();void 0===c.data("owidth")&&c.data("owidth",c[0].width),void 0===c.data("oheight")&&c.data("oheight",c[0].height),d.fill===f/g>=c.data("owidth")/c.data("oheight")?(t="100%",e="auto",n=Math.floor(f),s=Math.floor(f*(c.data("oheight")/c.data("owidth")))):(t="auto",e="100%",n=Math.floor(g*(c.data("owidth")/c.data("oheight"))),s=Math.floor(g)),u=f-n,"left"===(o=d.horizontalAlign.toLowerCase())&&(m=0),"center"===o&&(m=.5*u),"right"===o&&(m=u),-1!==o.indexOf("%")&&((o=parseInt(o.replace("%",""),10))>0&&(m=u*o*.01)),h=g-s,"left"===(a=d.verticalAlign.toLowerCase())&&(p=0),"center"===a&&(p=.5*h),"bottom"===a&&(p=h),-1!==a.indexOf("%")&&((a=parseInt(a.replace("%",""),10))>0&&(p=h*a*.01)),d.hardPixels&&(t=n,e=s),c.css({width:t,height:e,"margin-left":Math.floor(m),"margin-top":Math.floor(p)}),c.data("insta_imgLiquid_oldProcessed")||(c.fadeTo(d.fadeInTime,1),c.data("insta_imgLiquid_oldProcessed",!0),d.removeBoxBackground&&l.css("background-image","none"),l.addClass("insta_imgLiquid_nobgSize"),l.addClass("insta_imgLiquid_ready")),d.onItemFinish&&d.onItemFinish(i,l,c),r()}function r(){i===n.length-1&&n.settings.onFinish&&n.settings.onFinish()}var d=n.settings,l=e(this),c=e("img:first",l);return c.length?(c.data("insta_imgLiquid_settings")?(l.removeClass("insta_imgLiquid_error").removeClass("insta_imgLiquid_ready"),d=e.extend({},c.data("insta_imgLiquid_settings"),n.options)):d=e.extend({},n.settings,function(){var e={};if(n.settings.useDataHtmlAttr){var i=l.attr("data-insta_imgLiquid-fill"),s=l.attr("data-insta_imgLiquid-horizontalAlign"),o=l.attr("data-insta_imgLiquid-verticalAlign");("true"===i||"false"===i)&&(e.fill=Boolean("true"===i)),void 0===s||"left"!==s&&"center"!==s&&"right"!==s&&-1===s.indexOf("%")||(e.horizontalAlign=s),void 0===o||"top"!==o&&"bottom"!==o&&"center"!==o&&-1===o.indexOf("%")||(e.verticalAlign=o)}return t.isIE&&n.settings.ieFadeInDisabled&&(e.fadeInTime=0),e}()),c.data("insta_imgLiquid_settings",d),d.onItemStart&&d.onItemStart(i,l,c),void(t.bgs_Available&&d.useBackgroundSize?(-1===l.css("background-image").indexOf(encodeURI(c.attr("src")))&&l.css({"background-image":'url("'+encodeURI(c.attr("src"))+'")'}),l.css({"background-size":d.fill?"cover":"contain","background-position":(d.horizontalAlign+" "+d.verticalAlign).toLowerCase(),"background-repeat":"no-repeat"}),e("a:first",l).css({display:"block",width:"100%",height:"100%"}),e("img",l).css({display:"none"}),d.onItemFinish&&d.onItemFinish(i,l,c),l.addClass("insta_imgLiquid_bgSize"),l.addClass("insta_imgLiquid_ready"),r()):function t(){if(c.data("oldSrc")&&c.data("oldSrc")!==c.attr("src")){var n=c.clone().removeAttr("style");return n.data("insta_imgLiquid_settings",c.data("insta_imgLiquid_settings")),c.parent().prepend(n),c.remove(),(c=n)[0].width=0,void setTimeout(t,10)}return c.data("insta_imgLiquid_oldProcessed")?void a():(c.data("insta_imgLiquid_oldProcessed",!1),c.data("oldSrc",c.attr("src")),e("img:not(:first)",l).css("display","none"),l.css({overflow:"hidden"}),c.fadeTo(0,0).removeAttr("width").removeAttr("height").css({visibility:"visible","max-width":"none","max-height":"none",width:"auto",height:"auto",display:"block"}),c.on("error",o),c[0].onerror=o,function t(){c.data("insta_imgLiquid_error")||c.data("insta_imgLiquid_loaded")||c.data("insta_imgLiquid_oldProcessed")||(l.is(":visible")&&c[0].complete&&c[0].width>0&&c[0].height>0?(c.data("insta_imgLiquid_loaded",!0),setTimeout(a,i*d.delay)):setTimeout(t,d.timecheckvisibility))}(),void s())}())):void o()})}})}(jQuery),i=t.injectCss,d=document.getElementsByTagName("head")[0],(l=document.createElement("style")).type="text/css",l.styleSheet?l.styleSheet.cssText=i:l.appendChild(document.createTextNode(i)),d.appendChild(l),e.find(".insta_photo").insta_imgLiquid({fill:!0})}var i,d,l,c,h,p,m,f=(c=0,function(t,e){clearTimeout(c),c=setTimeout(t,e)});function g(){if("thumbnail"!==s){var t=e.find(".insta_photo").eq(0).innerWidth(),i=parseInt(o);if(!e.hasClass("insta_disable_mobile")){var n=jQuery(window).width();n<640&&parseInt(o)>2&&parseInt(o)<7&&(i=2),n<640&&parseInt(o)>6&&parseInt(o)<11&&(i=4),n<=480&&parseInt(o)>2&&(i=1)}var r=e.find("#insta_images").width()/i-2*a.imagepadding;t<=r&&(t=r),e.find(".insta_photo").css("height",t)}}jQuery(window).resize(function(){f(function(){g()},500)}),g(),h=jQuery,p={callback:function(){},runOnLoad:!0,frequency:100,instaPreviousVisibility:null},m={instaCheckVisibility:function(t,e){if(jQuery.contains(document,t[0])){var i=e.instaPreviousVisibility,n=t.is(":visible");e.instaPreviousVisibility=n,null==i?e.runOnLoad&&e.callback(t,n):i!==n&&e.callback(t,n),setTimeout(function(){m.instaCheckVisibility(t,e)},e.frequency)}}},h.fn.instaVisibilityChanged=function(t){var e=h.extend({},p,t);return this.each(function(){m.instaCheckVisibility(h(this),e)})},jQuery(".insta").filter(":hidden").instaVisibilityChanged({callback:function(t,e){g()},runOnLoad:!1}),jQuery("#instawidget_instagram .insta_photo").each(function(){$insta_photo=jQuery(this),$insta_photo.hover(function(){jQuery(this).fadeTo(200,.85)},function(){jQuery(this).stop().fadeTo(500,1)}),$insta_photo.closest(".insta_item").hasClass("insta_type_video")&&($insta_photo.find(".insta_playbtn").length||$insta_photo.append('<i class="fa fa-play insta_playbtn"></i>'))}),e.find("#insta_images .insta_item.insta_new").sort(function(t,e){var i=jQuery(t).data("date"),n=jQuery(e).data("date");return"none"==r?n-i:Math.round(Math.random())-.5}).appendTo(e.find("#insta_images")),setTimeout(function(){jQuery("#insta_images .insta_item.insta_new").removeClass("insta_new"),u=[]},500),function(){e.removeClass("insta_small insta_medium");var t=e.find(".insta_item").innerWidth();t>120&&t<240?e.addClass("insta_medium"):t<=120&&e.addClass("insta_small")}()},error:function(i){var n="",s="",o=i;if(o.indexOf("access_token")>-1)return n+="<p><b>Error: Access Token is not valid or has expired</b><br /><span>This error message is only visible to WordPress admins</span>",s="<p>There's an issue with the Instagram Access Token that you are using. Please obtain a new Access Token on the plugin's Settings page.<br />If you continue to have an issue with your Access Token then please see <a href='https://smashballoon.com/my-instagram-access-token-keep-expiring/' target='_blank'>this FAQ</a> for more information.",void jQuery("#instawidget_instagram").empty().append('<p style="text-align: center;">Unable to show Instagram photos</p><div id="insta_mod_error">'+n+s+"</div>");o.indexOf("user does not exist")>-1||o.indexOf("you cannot view this resource")>-1?(window.instaFeedMeta[t].error={errorMsg:'<p><b>Error: User ID <span class="instaErrorIds">'+window.instaFeedMeta[t].idsInFeed[d]+"</span> does not exist, is invalid, or is private</b><br /><span>This error is only visible to WordPress admins</span>",errorDir:"<p>Please double check the Instagram User ID that you are using and ensure that it is valid and not from a private account. To find your User ID simply enter your Instagram user name into this <a href='https://smashballoon.com/instagram-feed/find-instagram-user-id/' target='_blank'>tool</a>.</p>"},e.find("#insta_mod_error").length?-1==e.find(".instaErrorIds").text().indexOf(window.instaFeedMeta[t].idsInFeed[d])&&e.find(".instaErrorIds").append(","+window.instaFeedMeta[t].idsInFeed[d]):e.prepend('<div id="insta_mod_error">'+window.instaFeedMeta[t].error.errorMsg+window.instaFeedMeta[t].error.errorDir+"</div>")):o.indexOf("No images were returned")>-1&&(window.instaFeedMeta[t].error={errorMsg:'<p><b>Error: User ID <span class="instaErrorNone">'+window.instaFeedMeta[t].idsInFeed[d]+"</span> has no posts</b><br /><span>This error is only visible to WordPress admins</span>",errorDir:"<p>If you are the owner of this account, make a post on Instagram to see it in your feed.</p>"},e.find("#insta_mod_error.insta_error_none").length?-1==e.find(".instaErrorNone").text().indexOf(window.instaFeedMeta[t].idsInFeed[d])&&e.find(".instaErrorNone").append(","+window.instaFeedMeta[t].idsInFeed[d]):e.prepend('<div id="insta_mod_error" class="insta_error_none">'+window.instaFeedMeta[t].error.errorMsg+window.instaFeedMeta[t].error.errorDir+"</div>"))}});n.click(function(){jQuery(this).find(".fa-spinner").show(),jQuery(this).find(".insta_btn_text").css("opacity",0),h.next()}),h.run()})})}(function(){var t;t=function(){function t(t,e){var i,n;if(this.options={target:"instafeed",get:"popular",resolution:"thumbnail",sortBy:"none",links:!0,mock:!1,useHttp:!1},"object"==typeof t)for(i in t)n=t[i],this.options[i]=n;this.context=null!=e?e:this,this.unique=this._genKey()}return t.prototype.hasNext=function(){return"string"==typeof this.context.nextUrl&&this.context.nextUrl.length>0},t.prototype.next=function(){return!!this.hasNext()&&this.run(this.context.nextUrl)},t.prototype.run=function(e){var i,n;if("string"!=typeof this.options.clientId&&"string"!=typeof this.options.accessToken)throw new Error("Missing clientId or accessToken.");if("string"!=typeof this.options.accessToken&&"string"!=typeof this.options.clientId)throw new Error("Missing clientId or accessToken.");return null!=this.options.before&&"function"==typeof this.options.before&&this.options.before.call(this),"undefined"!=typeof document&&null!==document&&((n=document.createElement("script")).id="instafeed-fetcher",n.src=e||this._buildUrl(),document.getElementsByTagName("head")[0].appendChild(n),i="instafeedCache"+this.unique,window[i]=new t(this.options,this),window[i].unique=this.unique),!0},t.prototype.parse=function(t){var e,i,n,s,o,a,r,d,l,c,u,h,p,m,f,g,_,y,w,b;if("object"!=typeof t){if(null!=this.options.error&&"function"==typeof this.options.error)return this.options.error.call(this,"Invalid JSON data"),!1;throw new Error("Invalid JSON response")}if(200!==t.meta.code){if(null!=this.options.error&&"function"==typeof this.options.error)return this.options.error.call(this,t.meta.error_message),!1;throw new Error("Error from Instagram: "+t.meta.error_message)}if(0===t.data.length){if(null!=this.options.error&&"function"==typeof this.options.error)return this.options.error.call(this,"No images were returned from Instagram"),!1;throw new Error("No images were returned from Instagram")}if(null!=this.options.success&&"function"==typeof this.options.success&&this.options.success.call(this,t),this.context.nextUrl="",null!=t.pagination&&(this.context.nextUrl=t.pagination.next_url),"none"!==this.options.sortBy)switch(u="least"===(h="random"===this.options.sortBy?["","random"]:this.options.sortBy.split("-"))[0],h[1]){case"random":t.data.sort(function(){return.5-Math.random()});break;case"recent":t.data=this._sortBy(t.data,"created_time",u);break;case"liked":t.data=this._sortBy(t.data,"likes.count",u);break;case"commented":t.data=this._sortBy(t.data,"comments.count",u);break;default:throw new Error("Invalid option for sortBy: '"+this.options.sortBy+"'.")}if("undefined"!=typeof document&&null!==document&&!1===this.options.mock){if(r=t.data,null!=this.options.limit&&r.length>this.options.limit&&(r=r.slice(0,this.options.limit+1||9e9)),i=document.createDocumentFragment(),null!=this.options.filter&&"function"==typeof this.options.filter&&(r=this._filter(r,this.options.filter)),null!=this.options.template&&"string"==typeof this.options.template){for(n="",o="","",p=document.createElement("div"),m=0,_=r.length;m<_;m++)s=r[m],a=s.images[this.options.resolution].url,this.options.useHttp||(a=a.replace("http://","//")),o=this._makeTemplate(this.options.template,{model:s,id:s.id,link:s.link,image:a,caption:this._getObjectProperty(s,"caption.text"),likes:s.likes.count,comments:s.comments.count,location:this._getObjectProperty(s,"location.name")}),n+=o;for(p.innerHTML=n,f=0,y=(b=[].slice.call(p.childNodes)).length;f<y;f++)c=b[f],i.appendChild(c)}else for(g=0,w=r.length;g<w;g++)s=r[g],d=document.createElement("img"),a=s.images[this.options.resolution].url,this.options.useHttp||(a=a.replace("http://","//")),d.src=a,!0===this.options.links?(e=document.createElement("a"),e.href=s.link,e.appendChild(d),i.appendChild(e)):i.appendChild(d);this.options.target.append(i),document.getElementsByTagName("head")[0].removeChild(document.getElementById("instafeed-fetcher")),l="instafeedCache"+this.unique,window[l]=void 0;try{delete window[l]}catch(t){}}return null!=this.options.after&&"function"==typeof this.options.after&&this.options.after.call(this),!0},t.prototype._buildUrl=function(){var t,e,i;switch(t="https://api.instagram.com/v1",this.options.get){case"popular":e="media/popular";break;case"tagged":if("string"!=typeof this.options.tagName)throw new Error("No tag name specified. Use the 'tagName' option.");e="tags/"+this.options.tagName+"/media/recent";break;case"location":if("number"!=typeof this.options.locationId)throw new Error("No location specified. Use the 'locationId' option.");e="locations/"+this.options.locationId+"/media/recent";break;case"user":if("number"!=typeof this.options.userId)throw new Error("No user specified. Use the 'userId' option.");if("string"!=typeof this.options.accessToken)throw new Error("No access token. Use the 'accessToken' option.");e="users/"+userid+"/media/recent";break;default:throw new Error("Invalid option for get: '"+this.options.get+"'.")}return i=t+"/"+e,i+=null!=access_token_string?"?access_token="+this.options.accessToken:"?client_id="+this.options.clientId,null!=this.options.limit&&(i+="&count="+this.options.limit),i+="&callback=instafeedCache"+this.unique+".parse"},t.prototype._genKey=function(){var t;return""+(t=function(){return(65536*(1+Math.random())|0).toString(16).substring(1)})()+t()+t()+t()},t.prototype._makeTemplate=function(t,e){var i,n,s,o,a;for(n=/(?:\{{2})([\w\[\]\.]+)(?:\}{2})/,i=t;n.test(i);)s=i.match(n)[1],o=null!=(a=this._getObjectProperty(e,s))?a:"",i=i.replace(n,""+o);return i},t.prototype._getObjectProperty=function(t,e){var i,n;for(n=(e=e.replace(/\[(\w+)\]/g,".$1")).split(".");n.length;){if(i=n.shift(),!(null!=t&&i in t))return null;t=t[i]}return t},t.prototype._sortBy=function(t,e,i){var n;return n=function(t,n){var s,o;return s=this._getObjectProperty(t,e),o=this._getObjectProperty(n,e),i?s>o?1:-1:s<o?1:-1},t.sort(n.bind(this)),t},t.prototype._filter=function(t,e){var i,n,s,o,a;for(i=[],s=function(t){if(e(t))return i.push(t)},o=0,a=t.length;o<a;o++)n=t[o],s(n);return i},t}(),("undefined"!=typeof exports&&null!==exports?exports:window).instagramfeed=t}).call(this),function(){"use strict";var t=Array.prototype.slice;try{t.call(document.documentElement)}catch(e){Array.prototype.slice=function(e,i){if(i=void 0!==i?i:this.length,"[object Array]"===Object.prototype.toString.call(this))return t.call(this,e,i);var n,s,o=[],a=this.length,r=e||0;r=r>=0?r:a+r;var d=i||a;if(i<0&&(d=a+i),(s=d-r)>0)if(o=new Array(s),this.charAt)for(n=0;n<s;n++)o[n]=this.charAt(r+n);else for(n=0;n<s;n++)o[n]=this[r+n];return o}}}(),Function.prototype.bind||(Function.prototype.bind=function(t){if("function"!=typeof this)throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");var e=Array.prototype.slice.call(arguments,1),i=this,n=function(){},s=function(){return i.apply(this instanceof n&&t?this:t,e.concat(Array.prototype.slice.call(arguments)))};return n.prototype=this.prototype,s.prototype=new n,s});