

// get agent ID paramert from URL
var queryDict = {}
location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]})
var agentID = queryDict.id;

$('#newsText').keypress(function() {
    var dInput = this.value;
    if(dInput) $( ".field-news" ).removeClass( "has-error" );
});

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
                    allDay: true,
                    isNews: true,

                },
                true);
        }
      });
   }

$( "#agent" ).change(function() {
  agentID = $(this).val();
  url = "/backend/web/calendar/view?id="+agentID;
  document.location.href = url;
});

$('#calendar').fullCalendar({
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay'
  },
  defaultView: 'agendaWeek',
  minTime: '6:00:00',
  maxTime: '23:00:00',
  defaultTimedEventDuration: '0:30:00',
  allDayText: 'Zalecenia',
  selectable: true,
  locale: "pl",
  lang: "pl",
  selectHelper: true,
  editable: true,
  aspectRatio: 2,
  eventLimit: true,
  eventSources: [
    {
      url: '/calendar/agenttask?id='+agentID,
      color: 'yellow',
      textColor: 'black'
    },
    {
     url: '/calendar/agentnews?id='+agentID,
    }
],
  select: function(start, end, allDay) {
      var allDay = !start.hasTime() && !end.hasTime();
     // console.log(end);
      if(allDay){
          //sql format
          var startTime = start.format();
          var endTime = end.format();
          var mywhen = startTime + ' - ' + endTime;

          $('#calendarModal #when').text(mywhen);
          $('#calendarModal #startTime').val(startTime);
          $('#calendarModal #agentID').val(agentID);
          $('#calendarModal #endTime').val(endTime);
          $('#calendarModal').modal();
      }
     $('#calendar').fullCalendar('unselect');
  },
  eventDrop: function(event, delta, revertFunc) {
      console.log(event.allDay);
      console.log(event);
      if (event.isNews && event.allDay) {
          $.get('/calendar/updatenews', {
                  "id": event.id,
                  "start": event.start.format(),
                  "end": event.start.format()
              },
              function(data) {});
      } else if (!event.isNews && !event.allDay) {
          $.get('/calendar/update', {
                  "id": event.id,
                  "start": event.start.format()
              },
              function(data) {});
      } else {
          swal(
              'Niedozwolone!',
              'Nie można mieszać różnego rodzaju zdarzeń',
              'error'
          );
          revertFunc();
          $('#calendar').fullCalendar('undrop');
      }
  },

  eventRender: function(event, element) {
      element.bind('dblclick', function() {
          swal({
            title: "Jesteś pewien, że chcesz to usunąć?",
            text: "Informacja: "+event.title,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Tak",
            cancelButtonText: "Nie",
            closeOnConfirm: false
          },
          function(){
              var event_id = event.id;
              swal("Usunięto!", "Twoja notatka została usunięta", "success");
              $.post('remove', {"event_id": event_id},
                 function(data){
                     console.log(data);
                     $('#calendar').fullCalendar("removeEvents",  (data));
                     $('#calendar').fullCalendar("rerenderEvents");

                   });
          });
      });
      element.popover({
          title: event.title,
          placement: 'bottom',
          trigger: 'hover',
          content: event.description,
          html:true
          // container: '#calendar',
      });

        //element.find('div.fc-title').html(element.find('div.fc-title').text())	;
  },
  eventResize: function(event, delta, revertFunc) {

    if(event.isNews && event.allDay){
        $.get('/calendar/updatenews', {"id": event.id, "start": event.start.format(),"end":event.end.format()},
            function(data){
              });
          }
  }

});
