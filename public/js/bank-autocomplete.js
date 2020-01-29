$(function(){
  var bank_name = [
    { value: 'Bank Name List 1', data: 'Bank Name List 1' },
    { value: 'Bank Name List 2', data: 'Bank Name List 2' },
    { value: 'Bank Name List 3', data: 'Bank Name List 3' },
    { value: 'Bank Name List 4', data: 'Bank Name List 4' },
   
  ];
  
  // setup autocomplete function pulling from currencies[] array
  
  $('.autocomplete').autocomplete({
	  
    lookup: bank_name,
	appendTo: ".autocomplete_appendable",
   /* onSelect: function (suggestion) {
      var thehtml = '<strong>Currency Name:</strong> ' + suggestion.value + ' <br> <strong>Symbol:</strong> ' + suggestion.data;
      $('#outputcontent').html(thehtml);
    }*/
  });
  
  
  
  var bank_branch = [
    { value: 'Bank Branch Name List 1', data: 'Bank Branch Name List 1' },
    { value: 'Bank Branch Name List 2', data: 'Bank Branch Name List 2' },
    { value: 'Bank Branch Name List 3', data: 'Bank Branch Name List 3' },
    { value: 'Bank Branch Name List 4', data: 'Bank Branch Name List 4' },
   
  ];
  
  $('.autocomplete2').autocomplete({
	  
    lookup: bank_branch,
	appendTo: ".autocomplete_appendable2",
   /* onSelect: function (suggestion) {
      var thehtml = '<strong>Currency Name:</strong> ' + suggestion.value + ' <br> <strong>Symbol:</strong> ' + suggestion.data;
      $('#outputcontent').html(thehtml);
    }*/
  });
  

});