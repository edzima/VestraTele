

// get agent ID paramert from URL
var queryDict = {}
location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]})
var agentID = queryDict.id;

$('#newsText').keypress(function() {
    var dInput = this.value;
    if(dInput) $( ".field-news" ).removeClass( "has-error" );
});
/*
$('#submitButton').on('click', function(e){

  // We don't want this to act as a link so cancel the link action
  e.preventDefault();
  if($('#addNews #newsText').val()){
      $("#calendarModal").modal('hide');
      doSubmit();
  }
  else{
      $( ".field-news" ).addClass( "has-error" );
  }
});
*/
function doSubmit(){
    var url = "/backend/web/calendar/addnews";
    $.ajax({
        type: "POST",
        url: url,
        data: $("#addNews").serialize(), // serializes the form's elements.
        success: function(data)
        {
            $("#calendar").fullCalendar('renderEvent',
                {
                    id: data,
                    title: $('#newsText').val(),
                    start: new Date($('#startTime').val()),
                    end: new Date($('#endTime').val()),
                    allDay: "true"
                },
                true);
        }
      });
   }

$( "#agent" ).change(function() {
  agentID = $(this).val();
  url = "view?id="+agentID;
  document.location.href = url;
});

$('#calendar').fullCalendar({
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay'
  },
  defaultView: 'agendaWeek',
  views: {
      month: {
          aspectRatio: 1.3,
      },
      agendaWeek: {
          height: 'auto',
      }
  },
  minTime: '6:00:00',
  maxTime: '23:00:00',
  defaultTimedEventDuration: '0:30:00',
  allDayText: 'Zalecenia',
  defaultDate: '2017-02-10',
  selectable: true,
  locale: "pl",
  lang: "pl",
  //selectHelper: true,
  editable: true,
  eventLimit: true,
  eventSources: [
    {
      url: 'agenttask?id='+agentID,

    },
    {
     url: 'agentnews?id='+agentID,
     color: 'red',
     textColor: 'white'
    }
],
  select: function(start, end, allDay) {
      var allDay = !start.hasTime() && !end.hasTime();
     // console.log(end);
      if(!allDay){

          $('#task-date').val(start.format());
          $('#calendarModal').modal();
      }
     $('#calendar').fullCalendar('unselect');
  },
  eventClick: function(event){
      if (event.url) {
       window.open(event.url);
       return false;
   }
  },
  eventDrop: function(event) {
      if(!event.allDay){
          $.get('update', {"id": event.id, "start": event.start.format()},
              function(data){
                });
            }
    },
  eventRender: function(event, element) {

    element.bind('dblclick', function() {
    if(event._allDay){
        console.log("lldaty");
    }
    if(confirm("Czy napewno chcesz usunąc?")){
        var event_id = event.id;
        $.post('/backend/web/calendar/remove', {"event_id": event_id},
           function(data){
               console.log(data);
               $('#calendar').fullCalendar("removeEvents",  (data));
               $('#calendar').fullCalendar("rerenderEvents");
             });
        }
    });
    element.popover({
        title: event.title,
        placement: 'bottom',
         trigger: 'hover',
        // content: event.description,
    });
        //element.find('div.fc-title').html(element.find('div.fc-title').text())	;
  },

});
