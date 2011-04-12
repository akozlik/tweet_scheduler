$(document).ready(function()
{

    $('#datepicker').datepicker({
       
    });

    $('#tabs').tabs();

   $('#message').keyup(function()
    {
       var length = $('#message').attr('value').length;
       var maxLength = 140;

       var charsRemaining = maxLength - length;

       $('#charRemaining').html(charsRemaining);

       if (charsRemaining < 0)
       {
               $('#setReminder').hide('fast');
       } else
           {
               $('#setReminder').show('fast');
           }
    });
});