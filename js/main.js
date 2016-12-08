
$(document).ready(function() {

	// ==================== LOADER ==================== //
	
     $(window).load(function(){
        $('.doc-loader').fadeOut('slow');
     });
	
	
	// ==================== WOW ANIMATION DELAY ==================== //
    wow = new WOW(
    {
      animateClass: 'animated',
      mobile: false,
      offset:       70
    }
  );
  wow.init();

  // ==================== NIVO LIGHTBOX ==================== //

  $('.thumbnail').nivoLightbox();
  
  
  $('.iNeedForm').submit(function(e){
    
    e.preventDefault();
    var phone = $('input#phone').val();
    if (phone.length < 7) alert('Номер телефона не менее 7 символов');
        else $.post('/do/ineed/',{'phone':phone,'x':255},function(data){
            
            if (data.code == 1) { $('.iNeedForm').html('<p>Спасибо! Мы свяжемся с Вами в ближайшее время!</p>');  }
                else alert(data.text);
            
        },'json');
    
  });


});

