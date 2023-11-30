@extends('layouts.app')

@section('css')
    <!-- fullCalendar 2.2.5-->
    <link rel="stylesheet" href="{{ asset('resources/plugin/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/plugin/fullcalendar/fullcalendar.print.css') }}" media="print">

    <style>
        :root {
           /* --red: #ef233c;
            --darkred: #c00424;
            --platinum: #e5e5e5;
            --black: #2b2d42;
            --blue: #3379A1;*/
            --green: #59AB91;
            --white: #fff;
            --thumb: #edf2f4;
        }
        .scroll-container {
            display: flex;
            list-style: none;
            overflow-x: scroll;
            scroll-snap-type: x mandatory;
        }
        .scroll-container::-webkit-scrollbar {
            height: 12px;
        }
        .scroll-container::-webkit-scrollbar-thumb,
        .scroll-container::-webkit-scrollbar-track {
            border-radius: 92px;
        }
        .scroll-container::-webkit-scrollbar-thumb {
            background: var(--green);
        }
        .scroll-container::-webkit-scrollbar-track {
            background: var(--thumb);
        }
        .scroll-item {
            display: flex;
            flex-direction: column;
            flex: 0 0 100%;
            background: var(--white);
            border-radius: 4px;
            scroll-snap-align: start;
            transition: all 0.2s;
        }
        .scroll-item .widget-user {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 15%);
        }
        .scroll-item:not(:last-child) {
            margin-right: 10px;
        }
        .scroll-item .widget-user-header {
            background: var(--green);
            color: white;
        }
        .widget-user-header .widget-user-desc {
            max-width: 65%;
            font-size: 14px;
        }
        .widget-user-header .widget-user-username {
            font-size: 21px;
        }
        .widget-user .widget-user-image {
            position: absolute;
            top: 65px;
            left: 75%;
            margin-left: -45px;
        }
        .with-badge {
            position: relative;
            display: inline-block;
        }
        .with-badge::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50px;
            height: 50px;
            background: linear-gradient(to bottom left, transparent 30%, #ef233c 4%, #ef233c 50.5%, transparent 50.5%);
            transform-origin: bottom right;
            z-index: 1;
        }
        .with-badge::after {
            content: 'Government';
            position: absolute;
            top: 9%;
            right: 5%;
            transform: translate(50%, -50%) rotate(45deg);
            color: white;
            font-size: 8px;
            font-weight: bold;
            z-index: 2;
        }

        .widget-user-image {
            overflow: hidden;
        }
        .widget-user-image img {
            transition: transform .3s ease-in-out;
        }
        .scroll-item:hover .widget-user-image img {
            /*transform: scale(.9);*/
            width: 100px;
        }
        /*===============================================================*/











        /*===============================================================*/
        @media (min-width: 500px) {
            .scroll-item {
                flex-basis: calc(50% - 10px);
            }

            .scroll-item:not(:last-child) {
                margin-right: 20px;
            }
        }
        @media (min-width: 700px) {
            .scroll-item {
                flex-basis: calc(calc(100% / 3) - 20px);
            }

            .scroll-item:not(:last-child) {
                margin-right: 30px;
            }
        }
        @media (min-width: 1100px) {
            .scroll-item {
                flex-basis: calc(25% - 30px);
            }

            .scroll-item:not(:last-child) {
                margin-right: 1px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">Appointment Calendar</h3>


            <div class="scroll-container">
                @if(isset($appointment_sched))
                    @foreach($appointment_sched as $row)
                        <div class="col-md-4 scroll-item">
                            <!-- Widget: user widget style 1 -->
                            <div class="box box-widget widget-user with-badge">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header">
                                    <h3 class="widget-user-username">
                                        <?php
                                        $facility_name = \App\Facility::find($row->facility_id)->name;
                                        echo $facility_name;
                                        ?></h3>
                                    <h5 class="widget-user-desc">
                                        <?php
                                        $address = \App\Facility::find($row->facility_id)->address;
                                        echo $address;
                                        ?>
                                    </h5>
                                </div>
                                <div class="widget-user-image">
                                    <img src="<?php echo e(asset('resources/img/video/doh-logo.png')); ?>" class="img-circle" alt="User Avatar"/>
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">3,200</h5>
                                                <span class="description-text">Slot</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">13,000</h5>
                                                <span class="description-text">Available</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                            <button class="btn btn-block btn-success btn-select" id="selected_data" name="selected_data"
                                                    value={{$row->facility_id}}>Select</button>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
                                </div>
                            </div><!-- /.widget-user -->
                        </div><!-- /.col -->
                    @endforeach
                @endif
            </div>


            {{--------------------------------------------------------}}
            <div class="calendar-container">
                <div class="row">
                    <section class="content">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="box box-primary">
                                    <div class="box-body no-padding">
                                    <!-- THE CALENDAR -->
                                    <div id="calendar"></div>
                                    </div><!-- /.box-body -->
                                </div><!-- /. box -->
                            </div><!-- /.col -->
                            <div class="col-md-3">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Draggable Events</h4>
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">
                                        <div class="external-event bg-green">Lunch</div>
                                        {{--<div class="external-event bg-yellow">Go home</div>
                                        <div class="external-event bg-aqua">Do homework</div>
                                        <div class="external-event bg-light-blue">Work on UI design</div>
                                        <div class="external-event bg-red">Sleep tight</div>--}}
                                        <div class="checkbox">
                                            <label for="drop-remove">
                                            <input type="checkbox" id="drop-remove">
                                            remove after drop
                                            </label>
                                        </div>
                                        </div>
                                    </div><!-- /.box-body -->
                                </div><!-- /. box -->
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Create Event</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                        <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                                        <ul class="fc-color-picker" id="color-chooser">
                                            <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                                            <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                                        </ul>
                                        </div><!-- /btn-group -->
                                        <div class="input-group">
                                        <input id="new-event" type="text" class="form-control" placeholder="Event Title">
                                        <div class="input-group-btn">
                                            <button id="add-new-event" type="button" class="btn btn-primary btn-flat">Add</button>
                                        </div><!-- /btn-group -->
                                        </div><!-- /input-group -->
                                    </div>
                                </div>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </section><!-- /.content -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('resources/plugin/fullcalendar/fullcalendar.min.js') }}"></script>
    <script>
        /*-----------------------------------------------------------------*/

        $(document).ready(function() {
            // Apply default background when the page loads
           /* $(".scroll-item .widget-user-header").css({
                "background-color": "#59AB91",
                "color": "white"
            });
*/
           //try to check selected facility id
//            $('#select').on('click', function () {
//               var data = $('#select').val();
//               console.log("data", data);
//            });

            $(".btn-select").on("click", function() {
                console.log("Button is clicked!..")
                // Remove the "selected" class from all buttons
                $(".scroll-item").removeClass("selected");
                $(".btn-select").text("Select");
                $(".scroll-item .widget-user-header").css({
                    "background-color": "#59AB91",
                    "color": "white"
                });

                // Add the "selected" class to the clicked button
                var $parentItem = $(this).closest(".scroll-item");
                $parentItem.addClass("selected");

                // Change the text of the button based on its current state
                var buttonText = $parentItem.hasClass("selected") ? "Selected" : "Select";
                $(this).text(buttonText);

                // Apply the background color to .widget-user-header if selected
                if ($parentItem.hasClass("selected")) {
                    $parentItem.find(".widget-user-header").css({
                        "background-color": "#008E4D",
                        "color": "white"
                    });
                }
            });
        });




        /*-----------------------------------------------------------------*/
        $(function () {
            /*initialize the external events*/
            function ini_events(ele) {
            ele.each(function () {

                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                title: $.trim($(this).text()) // use the element's text as the event title
                };

                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject);

                // make the event draggable using jQuery UI
                $(this).draggable({
                zIndex: 1070,
                revert: true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
                });

            });
            }
            ini_events($('#external-events div.external-event'));

            /* initialize the calendar
            -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date();
            var d = date.getDate(),
                    m = date.getMonth(),
                    y = date.getFullYear();
            $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day'
            },
            //Random default events
            events: [
                {
                title: 'All Day Event',
                start: new Date(y, m, 1),
                backgroundColor: "#f56954", //red
                borderColor: "#f56954" //red
                },
                {
                title: 'Long Event',
                start: new Date(y, m, d - 5),
                end: new Date(y, m, d - 2),
                backgroundColor: "#f39c12", //yellow
                borderColor: "#f39c12" //yellow
                },
                {
                title: 'Meeting',
                start: new Date(y, m, d, 10, 30),
                allDay: false,
                backgroundColor: "#0073b7", //Blue
                borderColor: "#0073b7" //Blue
                },
                {
                title: 'Lunch',
                start: new Date(y, m, d, 12, 0),
                end: new Date(y, m, d, 14, 0),
                allDay: false,
                backgroundColor: "#00c0ef", //Info (aqua)
                borderColor: "#00c0ef" //Info (aqua)
                },
                {
                title: 'Birthday Party',
                start: new Date(y, m, d + 1, 19, 0),
                end: new Date(y, m, d + 1, 22, 30),
                allDay: false,
                backgroundColor: "#00a65a", //Success (green)
                borderColor: "#00a65a" //Success (green)
                },
                {
                title: 'Click for Google',
                start: new Date(y, m, 28),
                end: new Date(y, m, 29),
                url: 'http://google.com/',
                backgroundColor: "#3c8dbc", //Primary (light-blue)
                borderColor: "#3c8dbc" //Primary (light-blue)
                }
            ],
            editable: true,
            droppable: true, // this allows things to be dropped onto the calendar !!!
            drop: function (date, allDay) { // this function is called when something is dropped

                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject');

                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);

                // assign it the date that was reported
                copiedEventObject.start = date;
                copiedEventObject.allDay = allDay;
                copiedEventObject.backgroundColor = $(this).css("background-color");
                copiedEventObject.borderColor = $(this).css("border-color");

                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                // if so, remove the element from the "Draggable Events" list
                $(this).remove();
                }

            }
            });

            /* ADDING EVENTS */
            var currColor = "#3c8dbc"; //Red by default
            //Color chooser button
            var colorChooser = $("#color-chooser-btn");
            $("#color-chooser > li > a").click(function (e) {
            e.preventDefault();
            //Save color
            currColor = $(this).css("color");
            //Add color effect to button
            $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
            });
            $("#add-new-event").click(function (e) {
            e.preventDefault();
            //Get value and make sure it is not null
            var val = $("#new-event").val();
            if (val.length == 0) {
                return;
            }

            //Create events
            var event = $("<div />");
            event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
            event.html(val);
            $('#external-events').prepend(event);

            //Add draggable funtionality
            ini_events(event);

            //Remove event from text input
            $("#new-event").val("");
            });
        });
    </script>
@endsection

