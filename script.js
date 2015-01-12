var id = false;
var timer = false;

function update_job() {
  $.getJSON('job.php?id=' + id, function(json) {
    $('#status-step').text(json.step);
    if (json.perc) {
      $('#progress').outerWidth(json.perc + '%').text(json.perc + '%').show();
    } else {
      $('#progress').hide();
    }
    if (json.url) {
      $('#url').attr('href', json.url).show();
    } else {
      $('#url').hide();
    }
    switch (json.step) {
      case 'connecting':
        setTimeout(update_job, 1500);
        break;
      case 'assembling':
        setTimeout(update_job, 500);
        break;
      case 'scanning':
        setTimeout(update_job, 500);
        break;
      default:
        break;
    }
  });
}

$('#form').submit(function(e){
  e.preventDefault();
  var url = this.action;
  var data = $(this).serialize();

  $.post(url, {}, function(json){
    id = json.id;
    setTimeout(update_job, 1000);

    $.post(url, data + '&id=' + id, function(json){
      // done
    }, 'json');
  }, 'json');
});
