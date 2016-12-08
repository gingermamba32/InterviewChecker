Screen = {};


/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options=$.extend({},options);options.expires=-1;}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000));}else{date=options.expires;}expires='; expires='+date.toUTCString();}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}return cookieValue;}};


var ux = new String(window.location.href);



(function($) {
    $.fn.spinner = function(options) {
        var opts = $.extend({}, $.fn.spinner.defaults, options);

        return this.each(function() {
            var l=0, t=0, w=0, h=0, shim=0, $s;
            var $this = $(this);
            
            // removal handling
            if (options == 'remove' || options == 'close') {
                var $s = $this.data('spinner');
                var o = $this.data('opts');
                if (typeof $s != 'undefined') {
                    $s.remove();
                    $this.removeData('spinner').removeData('opts');
                    if (o.hide) $this.css('visibility', 'visible');
                    o.onFinish.call(this);
                    return;
                }
            }
            
            // retrieve element positioning
            var pos = $this.offset();
            w = $this.outerWidth();
            h = $this.outerHeight();
            
            // calculate vertical centering
            if (h > opts.height) shim = Math.round((h - opts.height)/ 2);
            else if (h < opts.height) shim = 0 - Math.round((opts.height - h) / 2);
            t = pos.top + shim + 'px';
            
            // calculate horizontal positioning
            if (opts.position == 'right') {
                l = pos.left + w + 10 + 'px';
            } else if (opts.position == 'left') {
                l = pos.left - opts.width - 10 + 'px';
            } else {
                l = pos.left + Math.round(.5 * w) - Math.round(.5 * opts.width) + 'px';
            }
            
            // call start callback
            opts.onStart.call(this);
            
            // hide element?
            if (opts.hide) $this.css('visibility', 'hidden');
            
            // create the spinner and attach
            $s = $('<img src="' + opts.img + '" style="position: absolute; left: ' + l +'; top: ' + t + '; width: ' + opts.width + 'px; height: ' + opts.height + 'px; z-index: ' + opts.zIndex + ';" />').appendTo('body');
            
            // removal handling
            $this.data('spinner', $s).data('opts', opts);
        });
    };
    
    // default spinner options
    $.fn.spinner.defaults = {
        position    : 'center'       // left, right, center
        , img       : '/data/ajax-loader.gif' // path to spinner img
        , height    : 11            // height of spinner img
        , width     : 16            // width of spinner img
        , zIndex    : 1001          // z-index of spinner
        , hide      : true         // whether to hide the elem
        , onStart   : function(){ } // start callback
        , onFinish  : function(){ } // end callback
    };
})(jQuery);




// Проскроленность
Screen.getBodyScrollTop = function(){
  return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
};

$(function(){
	
	(function(){
    var 
    DarkBG = $('.oDarkBG');
    
    var 
    Popup = {};
    Popup.node  = $('div.oPopup');
    Popup.close = Popup.node.find('div.ToClose, .oBut.Grey');
    
    
    $('.voteForm').submit(function(e){
        
        e.preventDefault();
        
        
        
        $.post('/do/vote/', $(this).serializeArray(),function(data){
            $('.popSiteMessage').html('');
            $('.oPopup .PopupTitle').html('Спасибо за оценку!').css('padding', '0 30px 0 0');
            $(this).spinner('remove');
        },'text');
        
    });
    
    $('.eSubmit').click(function(e){ e.preventDefault(); $(this).spinner(); $('.voteForm').submit(); });
    
    
    $('div.RankSite div.Rank span').click(function(){
      var
      D1 = $.Deferred(),
      D2 = $.Deferred();
     $.cookie('rank', 1, {expires:30,path:'/'});
       
     $('.popSiteMessage input[name="vote"]').val($(this).parent('td').index());
       
     if ($(this).parent('td').index() > 1) {
        
        $('.popSiteMessage').html('');
        $('.oPopup .PopupTitle').html('Спасибо за оценку!').css('padding', '0 30px 0 0');
        
        $.post('/do/vote/', {'vote':$(this).parent('td').index(),'name':'','text':''},function(data){},'text');
        
     }
     
    $('.RankSite').slideUp('normal');
      
      Popup.node.css('top',Screen.getBodyScrollTop()+50);
      if($.support.opacity){
        DarkBG.fadeIn(300,function(){ D1.resolve(); });
        Popup.node.fadeIn(300,function(){ D2.resolve(); });
      }
      else{
        DarkBG.show();
        Popup.node.show();
        D1.resolve();
        D2.resolve();
      }
      
      $.when( D1, D2 ).done(function(){
        var 
        EventID = 'ID_'+Math.floor( Math.random()*1000000 );
        
        var
        CloseFunc = function(){
          DarkBG.off('click.'+EventID);
          Popup.close.off('click.'+EventID);
          
          if($.support.opacity){
            DarkBG.fadeOut(300);
            Popup.node.fadeOut(300);
          }
          else{
            DarkBG.hide();
            Popup.node.hide();
          }
        };
        
        DarkBG.on('click.'+EventID,CloseFunc);
        Popup.close.on('click.'+EventID,CloseFunc);
      });
    });
    
  }());
	
function setMenu(url){$('.MainMenu a.active').removeClass('active'); $('.MainMenu a[href="'+url+'"]').addClass('active');}

	(function(){
	   
        setMenu('/');
       
		var 
		MP     = $('div.DownloadFiles>div.in'),
		before = MP.children('div.before'),
		after  = MP.children('div.after');
		
		before.on('click',function(e){
			if(after.is(':hidden')){
				e.stopPropagation();
				after.show();
				
				var EventID = 'download_files';
				$(document).on('click.'+EventID,function(e){
					var 
					E = $(e.target),
					P = E.parents();
					if( E.is(before) || P.is(before) || !E.is(after) && !P.is(after) ){
						$(document).off('click.'+EventID);
						after.hide();
					}
				});
			}
		});
        
        for (var i = 1; i< $('.MainMenu a').length; i++)  if  (ux.indexOf($('.MainMenu a').eq(i).attr('href')) > -1) setMenu($('.MainMenu a').eq(i).attr('href'));       

        $('.uniForm').submit(function(e){
            
           e.preventDefault();
           
           $('.uniForm .Error').removeClass('Error');
           
            $.post('/do/uniform/',$(this).serializeArray(), function(data){
                $('.uniForm .uSubmit').spinner('remove');
                if (data.code) 
                {
                    
                    $('.uniForm').slideUp('normal');
                    $('.formResult').slideDown('normal');
                    
                } else {
                    
                      
                    
                      for (i = 0; i<data.invalid.length; i++)
                            {
                                
                                
                                $('.uniForm input[name='+data.invalid[i]+']').parents('.inputText').addClass('Error');
                                $('.uniForm textarea[name='+data.invalid[i]+']').parents('.textarea').addClass('Error');
                            }
                            
                      alert('Обнаружены ошибки заполнения.');
                }
                
                
                
            }, 'json');
            
        });
        
        $('.uniForm .uSubmit').click(function(e){ $(this).spinner(); $(this).parents('form').submit(); });
        
        
	}());
});