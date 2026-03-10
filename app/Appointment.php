<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $guarded = array();

    /**
     * Generate iCalendar (.ics) file content
     */
    public function generateIcsContent()
    {
        $startTime = Carbon::parse($this->date . ' ' . $this->time);
        $endTime = $startTime->copy()->addHour();
        
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Department of Health//Telemedicine//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:" . $this->id . "-" . time() . "@cvchd7.com\r\n";
        $ics .= "DTSTAMP:" . Carbon::now()->format('Ymd\THis\Z') . "\r\n";
        $ics .= "DTSTART:" . $startTime->format('Ymd\THis') . "\r\n";
        $ics .= "DTEND:" . $endTime->format('Ymd\THis') . "\r\n";
        $ics .= "SUMMARY:Telemedicine Appointment - " . $this->doctor . "\r\n";
        $ics .= "DESCRIPTION:Patient: " . $this->patient_name . "\n";
        $ics .= "Facility: " . $this->facility . "\n";
        $ics .= "Status: " . strtoupper($this->status) . "\r\n";
        $ics .= "LOCATION:" . $this->facility . " - " . $this->address . "\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }
}
