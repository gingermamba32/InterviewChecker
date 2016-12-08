function userDND() {    
     $.post('/partner/mediaCategorySave/',$('table.table').find('input').serializeArray(), function(data){  },'html');
}

$(document).ready(function() {
    $("tr.ddd").parents('.table').tableDnD({	  
	    onDrop: function(table, row) {
           $.post('/partner/mediaCategorySave/',table.find('input').serializeArray(), function(data){  },'html');
           
	    }
	});
});