<?php

namespace App\Entity;

class Lesson
{
    private $id;
    private $meetingPointId;
    private $instructorId;
    private $startTime;
    private $endTime;

    public function __construct($id, $meetingPointId, $instructorId, $startTime, $endTime)
    {
        $this->id = $id;
        $this->meetingPointId = $meetingPointId;
        $this->instructorId = $instructorId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public static function renderHtml(Lesson $lesson)
    {
        return '<p>' . $lesson->id . '</p>';
    }

    public static function renderText(Lesson $lesson)
    {
        return (string) $lesson->id;
    }
    /**
     * return id
     */
    public function getId(){
        return $this->id;
    }
    /**
     * return meetingPointId
     */
    public function getMeetingPointId(){
        return $this->meetingPointId;
    }
    /**
     * return instructorId
     */
    public function getInstructorId(){
        return $this->instructorId;
    }
    /**
     * return startTime
     */
    public function getStartTime(){
        return $this->startTime;
    }
    /**
     * return endTime
     */
    public function getEndTtime(){
        return $this->endTime;
    }
}