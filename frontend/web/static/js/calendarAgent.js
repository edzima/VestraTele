

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
    var url = "addnews";
    var data  = $('#addNews').serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data, // serializes the form's elements.

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
                $('#calendarModal #newsText').val('');
                $('#calendarModal #url').val('');


        }
      });

   }


$('#calendar').fullCalendar({
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay'
  },
  defaultView: 'agendaWeek',
  views: {
      month: {
          aspectRatio: 0.5,
      },
      agendaWeek: {
          height: 'auto',
      }
  },
  minTime: '6:00:00',
  maxTime: '23:00:00',
  defaultTimedEventDuration: '0:30:00',
  allDayText: 'Zalecenia',
//  defaultDate: '2017-02-15',
  selectable: true,
  locale: "pl",
  lang: "pl",
  //selectHelper: true,
  editable: true,
  eventLimit: true,

  eventSources: [
    {
      url: 'oneagent?id='+agentID,

    },

    {
     url: 'agentnews?id='+agentID,
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
        else{
            $('#task-date').val(start.format());
            $('#taskModal').modal();
        }
       $('#calendar').fullCalendar('unselect');
    },
  eventClick: function(event){

      if (event.url) {
       window.open(event.url);
       return false;
   }
  },
  eventDrop: function( event, delta, revertFunc) {
      console.log(event.allDay);
      if(event.isNews && event.allDay){
          $.get('updatenews', {"id": event.id, "start": event.start.format(),"end":event.start.format()},
              function(data){
                });
            }
      else if (!event.isNews && !event.allDay){
          $.get('update', {"id": event.id, "start": event.start.format()},
              function(data){
                });
      }
      else {
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
        // container: '#calendar',
    });

    //element.find('div.fc-title').html(element.find('div.fc-title').text())	;
  },
  eventResize: function(event, delta, revertFunc) {

    if(event.isNews && event.allDay){
        $.get('updatenews', {"id": event.id, "start": event.start.format(),"end":event.end.format()},
            function(data){
              });
          }
  }

});
