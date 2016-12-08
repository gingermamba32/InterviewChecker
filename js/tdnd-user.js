function userDND() {
    
     $.post('/partner/mediaSave/',$('table.table').find('input').serializeArray(), function(data){  },'html');
}

$(document).ready(function() {
    $("tr.ddd").parents('.table').tableDnD({	  
	    onDrop: function(table, row) {
           $.post('/partner/mediaSave/',table.find('input').serializeArray(), function(data){  },'html');
           
	    }
	});
});