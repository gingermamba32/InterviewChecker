function upLo(file) {
    
   $('input[name="image"]').val(file);
   $('.ImgDest').attr('src','/a/b/' + file);
    
}

$(function(){
    
    
    $('.btn-call-code').click(function(e){
       
       e.preventDefault();
       var id = $(this).attr('data-id');
       var hash = $(this).attr('data-hash');
       $(this).parents('.pcc').html('<div class="col-md-12" style="background:#eee; color:#000; text-align: center; padding:16px 0px;">Код погашен.</div>'); 
        $.post('/do/call-code/',{'id':id, 'hash':hash},function(data){},'html');
    });  
    
    
    $('.btn-user-remove').click(function(e){
       e.preventDefault();
       if (confirm('Вы действительно хотите удалить пользователя?')) {
        
            $.post($(this).attr('href'),{},function(data){
                
                alert(data);
                window.location.reload();
                
            },'text');
        
       }

        
    });
    
    
      $('.btn-offer-remove').click(function(e){
       e.preventDefault();
       if (confirm('Вы действительно хотите удалить заведение?')) {
        
            $.post($(this).attr('href'),{},function(data){
                
                alert(data);
                window.location.reload();
                
            },'text');
        
       }

        
    });

});