@extends('layouts.app')

@section('css')
    <!-- fullCalendar 2.2.5-->
    <link rel="stylesheet" href="{{ asset('resources/plugin/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/plugin/fullcalendar/fullcalendar.print.css') }}" media="print">

    <style>
        :root {
            --red: #ef233c;
            --darkred: #c00424;
            --platinum: #e5e5e5;
            --black: #2b2d42;
            --white: #fff;
            --thumb: #edf2f4;
            --green: #00904E;
            --blue: #00A7D0;
        }

       /* * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }*/

       /* body {
            font: 16px / 24px "Rubik", sans-serif;
            color: var(--black);
            background: var(--platinum);
            margin: 50px 0;
        }
*/
       /* .container {
            max-width: 1400px;
            !*padding: 0 25px;
            margin: 0 auto;*!
        }*/

       /* h2 {
            font-size: 32px;
            margin-bottom: 1em;
        }*/

        .cards {
            display: flex;
            padding: 25px 0px;
            list-style: none;
            overflow-x: scroll;
            scroll-snap-type: x mandatory;
        }

        .card {
            display: flex;
            flex-direction: column;
            flex: 0 0 100%;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 15%);
            scroll-snap-align: start;
            transition: all 0.2s;
            padding-bottom: 20px;
        }

        .card:not(:last-child) {
            margin-right: 10px;
        }

        .card:hover .card-header {
            /*color: var(--white);
            background: var(--red);*/
            background-color: #007F99;
        }

        /*.card .card-title {
            font-size: 18px;
        }*/

        .card .card-content {
           /* margin: 5px 0;*/
            max-width: 100%;
            font-size: 13px;
           /* border-bottom: solid 1px darkgray;
            margin-bottom: 20px;*/
        }

        /*.card .card-link-wrapper {
            margin-top: auto;
        }*/

        .card .card-link {
            display: inline-block;
            text-decoration: none;
            color: white;
            background: var(--blue);
            padding: 6px 12px;
            border-radius: 8px;
            border: none;
            transition: background 0.2s;
            margin-top: 15px;
            margin-left: 15px;
            margin-bottom: 15px;
        }

        .card-link:hover {
            background-color: #007F99;
        }

       /* .card:hover .card-link {
            background: var(--darkred);
        }*/

        .cards::-webkit-scrollbar {
            height: 12px;
        }

        .cards::-webkit-scrollbar-thumb,
        .cards::-webkit-scrollbar-track {
            border-radius: 92px;
        }

        .cards::-webkit-scrollbar-thumb {
            background: var(--blue);
        }

        .cards::-webkit-scrollbar-track {
            background: var(--thumb);
        }

        .card .card-header {
            background-color:var(--blue); /* Add your desired background color here */
           /* padding: 10px;*/ /* Adjust padding as needed */
            padding: 20px 20px 0px;
            border-radius: 12px 12px 0 0; /* Round only the top corners */
            color: white;
        }

        /*.card-header:hover {
            background-color: #007F99;
        }*/

        .card .card-header h3 {
            font-size: 18px;
            margin: 0; /* Remove default margin for the heading */
        }

        .widget-user-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px; /* Adjust as needed */
        }

        .border-right {
            border-right: solid 1px lightgrey;
        }

        .description-text {
            font-size: 13px;
        }

        .description-block .description-header {
            font-size: 14px;
        }

        .img-circle {
            height: 20%;
            width: 20%;
        }


        @media (min-width: 500px) {
            .card {
                flex-basis: calc(50% - 10px);
            }

            .card:not(:last-child) {
                margin-right: 20px;
            }
        }

        @media (min-width: 700px) {
            .card {
                flex-basis: calc(calc(100% / 3) - 20px);
            }

            .card:not(:last-child) {
                margin-right: 30px;
            }
        }

        @media (min-width: 1100px) {
            .card {
                flex-basis: calc(25% - 30px);
            }

            .card:not(:last-child) {
                margin-right: 40px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">Appointment Calendar</h3>

            {{--------------------------------------------------------}}

            <ul class="cards">
                <li class="card">
                    <div class="card-header">
                        <h3>DUMDUM MEDICAL CLINIC</h3>
                        <div class="card-content">
                            <p>Brgy. Sambag II, Jones Ave., Cebu City</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="widget-user-image text-center">
                            <img src="<?php echo e(asset('resources/img/avatar5.png')); ?>" class="img-circle" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <p class="description-header">3,200</p>
                                <span class="description-text">Slot</span>
                            </div>
                        </div>
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <p class="description-header">13,000</p>
                                <span class="description-text">Available</span>
                            </div>
                        </div>
                        <div class="card-link-wrapper text-center">
                            <button class="card-link btn-sm" type="button">Select</button>
                            {{--<input type="radio" name="selectedCard"> Select--}}
                        </div>
                    </div>
                </li>

                <li class="card">
                    <div class="card-header">
                        <h3>Facility 2</h3>
                        <div class="card-content">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                        </div>
                    </div>
                    <div class="card-link-wrapper">
                        <a href="" class="card-link">Learn More</a>
                    </div>
                </li>

                <li class="card">
                    <div class="card-header">
                        <h3>Facility 3</h3>
                        <div class="card-content">
                            <p>Phasellus ultrices lorem vel bibendum ultricies.</p>
                        </div>
                    </div>
                    <div class="card-link-wrapper">
                        <a href="" class="card-link">Learn More</a>
                    </div>
                </li>

                <li class="card">
                    <div  class="card-header">
                        <h3>Facility 4</h3>
                        <div class="card-content">
                            <p>Aenean posuere mauris quam, pellentesque auctor mi.</p>
                        </div>
                    </div>
                    <div class="card-link-wrapper">
                        <a href="" class="card-link">Learn More</a>
                    </div>
                </li>

                <li class="card">
                    <div  class="card-header">
                        <h3>Facility 5</h3>
                        <div class="card-content">
                            <p>Vestibulum pharetra fringilla felis sit amet tempor.</p>
                        </div>
                    </div>
                    <div class="card-link-wrapper">
                        <a href="" class="card-link">Learn More</a>
                    </div>
                </li>

                <li class="card">
                    <div  class="card-header">
                        <h3>Facility 6</h3>
                        <div class="card-content">
                            <p>Donec ut tincidunt nisl. Vivamus eget eros id elit feugiat mollis.</p>
                        </div>
                    </div>
                    <div class="card-link-wrapper">
                        <a href="" class="card-link">Learn More</a>
                    </div>
                </li>
            </ul>


            {{--<div class="col-md-4">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-aqua-active">
                        <h3 class="widget-user-username">Alexander Pierce</h3>
                        <h5 class="widget-user-desc">Founder & CEO</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" src="/resources/img/user1-128x128.jpg" alt="User Avatar">
                    </div>

                    <div class="box-footer">
                        <div class="row">

                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">3,200</h5>
                                    <span class="description-text">SALES</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->

                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">13,000</h5>
                                    <span class="description-text">FOLLOWERS</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->

                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">35</h5>
                                    <span class="description-text">PRODUCTS</span>
                                </div><!-- /.description-block -->
                            </div><!-- /.col -->

                        </div><!-- /.row -->
                    </div>

                </div><!-- /.widget-user -->
            </div><!-- /.col -->
--}}


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
                                        <div class="external-event bg-yellow">Go home</div>
                                        <div class="external-event bg-aqua">Do homework</div>
                                        <div class="external-event bg-light-blue">Work on UI design</div>
                                        <div class="external-event bg-red">Sleep tight</div>
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



        $(function () {

            /* initialize the external events
            -----------------------------------------------------------------*/
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

